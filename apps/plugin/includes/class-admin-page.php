<?php
/**
 * Focused setup and diagnostics screen.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

/**
 * Renders the M1 administrator experience.
 */
final class Admin_Page {
	private const NONCE_ACTION = 'mdw_pbd_admin';

	private ?string $page_hook = null;

	/**
	 * @param Diagnostics $diagnostics Diagnostic collector.
	 */
	public function __construct( private readonly Diagnostics $diagnostics ) {
	}

	/**
	 * Register WordPress admin hooks.
	 */
	public function register(): void {
		// Divi registers its menu during admin_menu; run after it when available.
		add_action( 'admin_menu', array( $this, 'add_menu' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add the settings page.
	 */
	public function add_menu(): void {
		$parent_slug = $this->find_divi_menu_slug();
		if ( null !== $parent_slug ) {
			$page_hook       = add_submenu_page(
				$parent_slug,
				__( 'PromptBridge for Divi', 'promptbridge-for-divi' ),
				__( 'PromptBridge', 'promptbridge-for-divi' ),
				Plugin::CAPABILITY,
				Plugin::MENU_SLUG,
				array( $this, 'render' )
			);
			$this->page_hook = is_string( $page_hook ) ? $page_hook : null;
			if ( null !== $this->page_hook ) {
				add_action( 'load-' . $this->page_hook, array( $this, 'handle_post' ) );
			}

			return;
		}

		$page_hook       = add_options_page(
			__( 'PromptBridge for Divi', 'promptbridge-for-divi' ),
			__( 'PromptBridge', 'promptbridge-for-divi' ),
			Plugin::CAPABILITY,
			Plugin::MENU_SLUG,
			array( $this, 'render' )
		);
		$this->page_hook = is_string( $page_hook ) ? $page_hook : null;
		if ( null !== $this->page_hook ) {
			add_action( 'load-' . $this->page_hook, array( $this, 'handle_post' ) );
		}
	}

	/**
	 * Find an existing Divi top-level menu without assuming one specific Divi build.
	 */
	private function find_divi_menu_slug(): ?string {
		global $menu;

		if ( ! is_array( $menu ) ) {
			return null;
		}

		foreach ( array( 'et_onboarding', 'et_divi_options' ) as $known_slug ) {
			foreach ( $menu as $item ) {
				if ( isset( $item[2] ) && $known_slug === (string) $item[2] ) {
					return $known_slug;
				}
			}
		}

		foreach ( $menu as $item ) {
			$label = isset( $item[0] ) ? strtolower( wp_strip_all_tags( (string) $item[0] ) ) : '';
			$slug  = isset( $item[2] ) ? (string) $item[2] : '';

			if ( '' !== $slug && 0 === strpos( $label, 'divi' ) ) {
				return $slug;
			}
		}

		return null;
	}

	/**
	 * Load the small, local stylesheet only on this page.
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		if ( null === $this->page_hook || $this->page_hook !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'mdw-pbd-admin',
			MDW_PBD_URL . 'admin/css/admin.css',
			array(),
			MDW_PBD_VERSION
		);
	}

	/**
	 * Process settings before WordPress renders the admin page.
	 */
	public function handle_post(): void {
		$request_method = isset( $_SERVER['REQUEST_METHOD'] ) && is_string( $_SERVER['REQUEST_METHOD'] )
			? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) )
			: '';
		if ( 'POST' !== $request_method ) {
			return;
		}

		if ( ! current_user_can( Plugin::CAPABILITY ) ) {
			wp_die( esc_html__( 'You are not allowed to manage PromptBridge.', 'promptbridge-for-divi' ) );
		}

		check_admin_referer( self::NONCE_ACTION );
		$action      = isset( $_POST['mdw_pbd_action'] )
			? sanitize_key( wp_unslash( $_POST['mdw_pbd_action'] ) )
			: '';
		$notice_code = '';
		$tab         = 'general';

		if ( 'save_consent' === $action ) {
			$model = isset( $_POST['mdw_pbd_model'] ) && is_string( $_POST['mdw_pbd_model'] )
				? sanitize_text_field( wp_unslash( $_POST['mdw_pbd_model'] ) )
				: Plugin::selected_model();

			if ( ! Plugin::is_supported_model( $model ) ) {
				$notice_code = 'invalid_model';
			} else {
				$consent                     = isset( $_POST['mdw_pbd_service_consent'] );
				$settings                    = get_option( Plugin::OPTION_SETTINGS, array() );
				$settings                    = is_array( $settings ) ? $settings : array();
				$settings['service_consent'] = $consent;
				$settings['model']           = $model;
				update_option( Plugin::OPTION_SETTINGS, $settings, false );
				$notice_code = $consent ? 'consent_saved' : 'consent_withdrawn';
			}
		} elseif ( 'run_diagnostics' === $action ) {
			$tab = 'diagnostics';
			set_transient(
				'mdw_pbd_diagnostics_' . get_current_user_id(),
				$this->diagnostics->collect( true ),
				60
			);
			$notice_code = 'diagnostics_finished';
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'tab'            => $tab,
					'mdw_pbd_notice' => $notice_code,
				),
				menu_page_url( Plugin::MENU_SLUG, false )
			)
		);
		exit;
	}

	/**
	 * Render the settings page.
	 */
	public function render(): void {
		if ( ! current_user_can( Plugin::CAPABILITY ) ) {
			wp_die( esc_html__( 'You are not allowed to manage PromptBridge.', 'promptbridge-for-divi' ) );
		}

		$base_url = menu_page_url( Plugin::MENU_SLUG, false );

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- These GET values only select read-only page state.
		$requested_tab = isset( $_GET['tab'] ) && is_string( $_GET['tab'] )
			? sanitize_key( wp_unslash( $_GET['tab'] ) )
			: 'general';
		$tab           = in_array( $requested_tab, array( 'general', 'diagnostics', 'advanced' ), true )
			? $requested_tab
			: 'general';

		$notice_code = isset( $_GET['mdw_pbd_notice'] ) && is_string( $_GET['mdw_pbd_notice'] )
			? sanitize_key( wp_unslash( $_GET['mdw_pbd_notice'] ) )
			: '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		$notices     = array(
			'invalid_model'        => array( 'error', __( 'Choose one of the supported models.', 'promptbridge-for-divi' ) ),
			'consent_saved'        => array( 'success', __( 'Service consent and model saved.', 'promptbridge-for-divi' ) ),
			'consent_withdrawn'    => array( 'success', __( 'Service consent withdrawn. Codex will not be launched.', 'promptbridge-for-divi' ) ),
			'diagnostics_finished' => array( 'info', __( 'Diagnostics finished.', 'promptbridge-for-divi' ) ),
		);
		$notice      = isset( $notices[ $notice_code ] ) ? $notices[ $notice_code ][1] : '';
		$notice_type = isset( $notices[ $notice_code ] ) ? $notices[ $notice_code ][0] : 'success';

		$results = $this->diagnostics->collect( false );
		if ( 'diagnostics' === $tab ) {
			$transient_key     = 'mdw_pbd_diagnostics_' . get_current_user_id();
			$transient_results = get_transient( $transient_key );
			delete_transient( $transient_key );
			if ( is_array( $transient_results ) ) {
				/** @var list<array{key: string, label: string, status: string, detail: string}> $transient_results */
				$results = $transient_results;
			}
		}

		$runtime_configured = defined( 'MDW_PBD_CODEX_PATH' ) && defined( 'MDW_PBD_CODEX_HOME' );
		$tabs               = array(
			'general'     => __( 'General', 'promptbridge-for-divi' ),
			'diagnostics' => __( 'Diagnostics', 'promptbridge-for-divi' ),
			'advanced'    => __( 'Advanced', 'promptbridge-for-divi' ),
		);
		?>
		<div class="wrap mdw-pbd-wrap">
			<div class="mdw-pbd-page-actions">
				<?php if ( 'general' === $tab ) : ?>
					<button type="submit" form="mdw-pbd-settings-form" class="button button-primary mdw-pbd-primary-action"><?php esc_html_e( 'Save Changes', 'promptbridge-for-divi' ); ?></button>
				<?php elseif ( 'diagnostics' === $tab ) : ?>
					<button type="submit" form="mdw-pbd-diagnostics-form" class="button button-primary mdw-pbd-primary-action"><?php esc_html_e( 'Run Diagnostics', 'promptbridge-for-divi' ); ?></button>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $notice ) : ?>
				<div class="notice notice-<?php echo esc_attr( $notice_type ); ?> is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
			<?php endif; ?>

			<section class="mdw-pbd-panel" aria-labelledby="mdw-pbd-title">
				<header class="mdw-pbd-panel-header">
					<span class="mdw-pbd-mark" aria-hidden="true">PB</span>
					<div class="mdw-pbd-heading">
						<h1 id="mdw-pbd-title"><?php esc_html_e( 'PromptBridge for Divi', 'promptbridge-for-divi' ); ?></h1>
						<p><?php esc_html_e( 'Local Codex controls for Divi, with explicit consent and server checks.', 'promptbridge-for-divi' ); ?></p>
					</div>
					<span class="mdw-pbd-release"><?php esc_html_e( 'Staging alpha', 'promptbridge-for-divi' ); ?></span>
				</header>

				<nav class="mdw-pbd-tabs" aria-label="<?php esc_attr_e( 'PromptBridge settings', 'promptbridge-for-divi' ); ?>">
					<?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
						<a class="mdw-pbd-tab<?php echo $tab === $tab_key ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'tab', $tab_key, $base_url ) ); ?>" <?php echo $tab === $tab_key ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $tab_label ); ?></a>
					<?php endforeach; ?>
				</nav>

				<?php if ( 'general' === $tab ) : ?>
					<div class="mdw-pbd-section-heading"><?php esc_html_e( 'General settings', 'promptbridge-for-divi' ); ?></div>
					<form method="post" id="mdw-pbd-settings-form" class="mdw-pbd-options">
						<?php wp_nonce_field( self::NONCE_ACTION ); ?>
						<input type="hidden" name="mdw_pbd_action" value="save_consent" />

						<div class="mdw-pbd-option-row">
							<div class="mdw-pbd-option-copy">
								<label for="mdw-pbd-model"><?php esc_html_e( 'Default model', 'promptbridge-for-divi' ); ?></label>
								<p><?php esc_html_e( 'Run Diagnostics refreshes this list from the installed Codex runtime. Luna remains the safe fallback.', 'promptbridge-for-divi' ); ?></p>
							</div>
							<select id="mdw-pbd-model" name="mdw_pbd_model">
								<?php foreach ( Plugin::supported_models() as $model_id => $model_label ) : ?>
									<option value="<?php echo esc_attr( $model_id ); ?>" <?php selected( Plugin::selected_model(), $model_id ); ?>><?php echo esc_html( $model_label ); ?><?php echo Plugin::DEFAULT_MODEL === $model_id ? esc_html__( ' — Recommended', 'promptbridge-for-divi' ) : ''; ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="mdw-pbd-option-row">
							<div class="mdw-pbd-option-copy">
								<span class="mdw-pbd-option-label"><?php esc_html_e( 'Allow Codex service access', 'promptbridge-for-divi' ); ?></span>
								<p><?php esc_html_e( 'When generation is implemented and you request it, Codex may send prompts, selected Divi content, account data, and runtime metadata to OpenAI.', 'promptbridge-for-divi' ); ?></p>
								<p class="mdw-pbd-policy-links"><a href="https://openai.com/policies/privacy-policy/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Privacy policy', 'promptbridge-for-divi' ); ?></a><span aria-hidden="true"> · </span><a href="https://openai.com/policies/terms-of-use/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Terms', 'promptbridge-for-divi' ); ?></a></p>
							</div>
							<label class="mdw-pbd-switch">
								<input type="checkbox" name="mdw_pbd_service_consent" value="1" <?php checked( Plugin::service_consent_granted() ); ?> />
								<span class="mdw-pbd-switch-track" aria-hidden="true"></span>
								<span class="screen-reader-text"><?php esc_html_e( 'Allow requested Codex operations to contact OpenAI.', 'promptbridge-for-divi' ); ?></span>
							</label>
						</div>
					</form>
				<?php elseif ( 'diagnostics' === $tab ) : ?>
					<div class="mdw-pbd-section-heading"><?php esc_html_e( 'Environment diagnostics', 'promptbridge-for-divi' ); ?></div>
					<form method="post" id="mdw-pbd-diagnostics-form">
						<?php wp_nonce_field( self::NONCE_ACTION ); ?>
						<input type="hidden" name="mdw_pbd_action" value="run_diagnostics" />
					</form>

					<div class="mdw-pbd-runtime">
						<div>
							<h2><?php esc_html_e( 'Runtime setup', 'promptbridge-for-divi' ); ?></h2>
							<?php if ( $runtime_configured ) : ?>
								<p><strong><?php esc_html_e( 'Configured.', 'promptbridge-for-divi' ); ?></strong> <?php esc_html_e( 'The Codex executable and private home are defined in wp-config.php.', 'promptbridge-for-divi' ); ?></p>
							<?php else : ?>
								<p><?php esc_html_e( 'Ask the server administrator to add both constants to wp-config.php before running diagnostics.', 'promptbridge-for-divi' ); ?></p>
								<pre><code>define( 'MDW_PBD_CODEX_PATH', '/absolute/path/to/codex' );
define( 'MDW_PBD_CODEX_HOME', '/private/path/to/promptbridge-codex-home' );</code></pre>
								<p><?php esc_html_e( 'The Codex home needs an empty .promptbridge-for-divi marker file and directory mode 0700.', 'promptbridge-for-divi' ); ?></p>
							<?php endif; ?>
						</div>
						<p class="mdw-pbd-runtime-note"><?php esc_html_e( 'Running diagnostics also refreshes the bundled model catalog. Nothing runs on page load, and PromptBridge never installs or updates Codex.', 'promptbridge-for-divi' ); ?></p>
					</div>

					<ul class="mdw-pbd-status-list">
						<?php foreach ( $results as $result ) : ?>
							<li class="mdw-pbd-status mdw-pbd-status--<?php echo esc_attr( $result['status'] ); ?>">
								<div class="mdw-pbd-status-summary">
									<strong><?php echo esc_html( $result['label'] ); ?></strong>
									<span><?php echo esc_html( ucfirst( $result['status'] ) ); ?></span>
								</div>
								<p><?php echo esc_html( $result['detail'] ); ?></p>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<div class="mdw-pbd-section-heading"><?php esc_html_e( 'Advanced', 'promptbridge-for-divi' ); ?></div>
					<div class="mdw-pbd-advanced">
						<h2><?php esc_html_e( 'Current alpha scope', 'promptbridge-for-divi' ); ?></h2>
						<p><?php esc_html_e( 'This build validates the host, stores explicit consent, and caches the installed Codex model list. Content generation and Divi editor controls are not available yet.', 'promptbridge-for-divi' ); ?></p>
						<h2><?php esc_html_e( 'Runtime ownership', 'promptbridge-for-divi' ); ?></h2>
						<p><?php esc_html_e( 'The server owner installs and updates Codex. PromptBridge only uses the fixed executable and private home configured in wp-config.php.', 'promptbridge-for-divi' ); ?></p>
					</div>
				<?php endif; ?>
			</section>
		</div>
		<?php
	}
}
