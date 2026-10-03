<?php
/**
 * Plugin lifecycle and composition root.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

/**
 * Owns plugin hooks and lifecycle boundaries.
 */
final class Plugin {
	public const CAPABILITY          = 'mdw_pbd_manage';
	public const GENERATE_CAPABILITY = 'mdw_pbd_generate';
	public const MENU_SLUG           = 'promptbridge-for-divi';
	public const OPTION_SETTINGS     = 'mdw_pbd_settings';
	public const DEFAULT_MODEL       = 'gpt-5.6-luna';
	public const TERMS_VERSION       = '2026-10-02';
	public const PRIVACY_VERSION     = '2026-10-02';

	/**
	 * Safe fallback choices used before a validated local catalog is cached.
	 *
	 * @var array<string, string>
	 */
	private const SUPPORTED_MODELS = array(
		'gpt-5.6-luna'  => 'GPT-5.6 Luna',
		'gpt-5.6-sol'   => 'GPT-5.6 Sol',
		'gpt-5.6-terra' => 'GPT-5.6 Terra',
	);

	private static ?self $instance = null;

	/**
	 * Get the single plugin composition root.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register runtime hooks.
	 */
	public function register(): void {
		add_action( 'admin_init', array( self::class, 'add_privacy_policy_suggestion' ) );
		if ( get_option( 'mdw_pbd_version', '' ) !== MDW_PBD_VERSION ) {
			self::grant_capabilities();
			update_option( 'mdw_pbd_version', MDW_PBD_VERSION, false );
		}
		( new GitHub_Updater() )->register();
		( new Generation_API() )->register();
		( new Divi_Editor() )->register();

		if ( is_admin() ) {
			( new Admin_Page( new Diagnostics() ) )->register();
		}
	}

	/** Add a site-specific disclosure to WordPress's Privacy Policy Guide. */
	public static function add_privacy_policy_suggestion(): void {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$policy_text  = '<p>' . esc_html__( 'When an administrator enables Codex service access and an authorized editor requests generation, PromptBridge sends the instruction and selected Divi Text field content to OpenAI through the separately installed Codex runtime. PromptBridge stores these fields in a private WordPress job only while queued or running, then removes them. A generated preview and job metadata are scheduled for deletion after one day; WordPress Cron may run late.', 'promptbridge-for-divi' ) . '</p>';
		$policy_text .= '<p>' . esc_html__( 'The Codex runtime may keep its own session history in the configured Codex home. PromptBridge does not manage or delete that history. WordPress update checks may send the site server IP address and ordinary request metadata to GitHub when requesting release information.', 'promptbridge-for-divi' ) . '</p>';
		$policy_text .= '<p><a href="https://promptbridge.mrdemonwolf.dev/docs/privacy">' . esc_html__( 'PromptBridge Privacy Policy', 'promptbridge-for-divi' ) . '</a></p>';

		wp_add_privacy_policy_content( __( 'PromptBridge for Divi', 'promptbridge-for-divi' ), $policy_text );
	}

	/**
	 * Safe activation: grant only the plugin-specific administrator capability.
	 */
	public static function activate(): void {
		self::grant_capabilities();
		add_option( 'mdw_pbd_version', MDW_PBD_VERSION, '', false );

		add_option(
			self::OPTION_SETTINGS,
			array(
				'service_consent' => false,
				'model'           => self::DEFAULT_MODEL,
			),
			'',
			false
		);
	}

	/** Grant settings access to administrators and generation to editors. */
	private static function grant_capabilities(): void {
		$administrator = get_role( 'administrator' );
		if ( null !== $administrator ) {
			$administrator->add_cap( self::CAPABILITY );
			$administrator->add_cap( self::GENERATE_CAPABILITY );
		}

		$editor = get_role( 'editor' );
		if ( null !== $editor ) {
			$editor->add_cap( self::GENERATE_CAPABILITY );
		}
	}

	/**
	 * Deactivation restores hooks without deleting generated WordPress content.
	 */
	public static function deactivate(): void {
		wp_clear_scheduled_hook( 'mdw_pbd_process_jobs' );
		wp_clear_scheduled_hook( 'mdw_pbd_cleanup_jobs' );
	}

	/**
	 * Whether an administrator has explicitly opted in to service use.
	 */
	public static function service_consent_granted(): bool {
		$settings = get_option( self::OPTION_SETTINGS, array() );
		return self::legal_acceptance_current()
			&& is_array( $settings )
			&& true === ( $settings['service_consent'] ?? false );
	}

	/** Whether an administrator accepted the current policy versions. */
	public static function legal_acceptance_current(): bool {
		$settings   = get_option( self::OPTION_SETTINGS, array() );
		$acceptance = is_array( $settings ) ? ( $settings['legal_acceptance'] ?? null ) : null;
		return is_array( $acceptance )
			&& self::TERMS_VERSION === ( $acceptance['terms_version'] ?? null )
			&& self::PRIVACY_VERSION === ( $acceptance['privacy_version'] ?? null )
			&& isset( $acceptance['accepted_at'], $acceptance['accepted_by'] )
			&& is_string( $acceptance['accepted_at'] )
			&& is_int( $acceptance['accepted_by'] )
			&& $acceptance['accepted_by'] > 0;
	}

	/** Store the administrator's explicit acceptance of the current policies. */
	public static function record_legal_acceptance( int $user_id ): bool {
		if ( $user_id <= 0 || ! user_can( $user_id, self::CAPABILITY ) ) {
			return false;
		}

		$settings                     = get_option( self::OPTION_SETTINGS, array() );
		$settings                     = is_array( $settings ) ? $settings : array();
		$settings['service_consent']  = false;
		$settings['legal_acceptance'] = array(
			'terms_version'   => self::TERMS_VERSION,
			'privacy_version' => self::PRIVACY_VERSION,
			'accepted_at'     => gmdate( 'c' ),
			'accepted_by'     => $user_id,
		);
		update_option( self::OPTION_SETTINGS, $settings, false );

		return self::legal_acceptance_current();
	}

	/**
	 * Return the validated cached model choices or the bundled fallback list.
	 *
	 * @return array<string, string>
	 */
	public static function supported_models(): array {
		if ( function_exists( 'get_option' ) ) {
			$cached = Model_Catalog::models_from_projection( get_option( Model_Catalog::OPTION_NAME, array() ) );
			if ( null !== $cached ) {
				return $cached;
			}
		}

		return self::SUPPORTED_MODELS;
	}

	/**
	 * Check whether a model is explicitly supported by this staging alpha.
	 */
	public static function is_supported_model( string $model ): bool {
		return array_key_exists( $model, self::supported_models() );
	}

	/**
	 * Read the saved model, falling back safely for older settings records.
	 */
	public static function selected_model(): string {
		$settings = function_exists( 'get_option' ) ? get_option( self::OPTION_SETTINGS, array() ) : array();
		$model    = is_array( $settings ) && isset( $settings['model'] ) && is_string( $settings['model'] )
			? $settings['model']
			: self::DEFAULT_MODEL;

		return self::is_supported_model( $model ) ? $model : self::DEFAULT_MODEL;
	}

	private function __construct() {
	}
}
