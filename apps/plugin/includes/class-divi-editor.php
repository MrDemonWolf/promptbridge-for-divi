<?php
/**
 * Registers the small Divi Visual Builder field action.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

/** Adds the PromptBridge action to the core Text module Body field. */
final class Divi_Editor {
	private const SCRIPT_HANDLE = 'mdw-pbd-divi-editor';

	/** Register Divi's Visual Builder asset hook. */
	public function register(): void {
		add_action( 'divi_visual_builder_assets_before_enqueue_scripts', array( $this, 'register_script' ) );
	}

	/** Register the script in Divi's app window, where field filters run. */
	public function register_script(): void {
		if ( ! et_builder_d5_enabled() || ! et_core_is_fb_enabled() || ! Plugin::service_consent_granted() ) {
			return;
		}

		\ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
			array(
				'name'    => self::SCRIPT_HANDLE,
				'version' => MDW_PBD_VERSION,
				'script'  => array(
					'src'                => MDW_PBD_URL . 'admin/js/divi-editor.js',
					'deps'               => array( 'divi-vendor-wp-hooks', 'divi-tooltip' ),
					'enqueue_top_window' => false,
					'enqueue_app_window' => true,
					'args'               => array( 'in_footer' => false ),
				),
			)
		);

		wp_localize_script(
			self::SCRIPT_HANDLE,
			'MDW_PBD_EDITOR',
			array(
				'jobsUrl' => rest_url( 'mdw-promptbridge/v1/jobs' ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			)
		);
	}
}
