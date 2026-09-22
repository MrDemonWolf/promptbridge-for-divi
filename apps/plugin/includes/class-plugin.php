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
	public const CAPABILITY      = 'mdw_pbd_manage';
	public const MENU_SLUG       = 'promptbridge-for-divi';
	public const OPTION_SETTINGS = 'mdw_pbd_settings';
	public const DEFAULT_MODEL   = 'gpt-5.6-luna';

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
		if ( is_admin() ) {
			( new Admin_Page( new Diagnostics() ) )->register();
		}
	}

	/**
	 * Safe activation: grant only the plugin-specific administrator capability.
	 */
	public static function activate(): void {
		$administrator = get_role( 'administrator' );
		if ( null !== $administrator ) {
			$administrator->add_cap( self::CAPABILITY );
		}

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

	/**
	 * Deactivation restores hooks without deleting generated WordPress content.
	 */
	public static function deactivate(): void {
		wp_clear_scheduled_hook( 'mdw_pbd_process_jobs' );
	}

	/**
	 * Whether an administrator has explicitly opted in to service use.
	 */
	public static function service_consent_granted(): bool {
		$settings = get_option( self::OPTION_SETTINGS, array() );
		return is_array( $settings ) && true === ( $settings['service_consent'] ?? false );
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
