<?php
/**
 * PromptBridge for Divi uninstall cleanup.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$mdw_pbd_cleanup_site = static function (): void {
	delete_option( 'mdw_pbd_settings' );
	delete_option( 'mdw_pbd_model_catalog' );
	delete_option( 'mdw_pbd_version' );
	wp_clear_scheduled_hook( 'mdw_pbd_process_jobs' );
	wp_clear_scheduled_hook( 'mdw_pbd_cleanup_jobs' );

	$mdw_pbd_jobs = get_posts(
		array(
			'post_type'      => 'mdw_pbd_job',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	foreach ( $mdw_pbd_jobs as $mdw_pbd_job_id ) {
		wp_delete_post( (int) $mdw_pbd_job_id, true );
	}

	$mdw_pbd_roles = wp_roles();
	foreach ( array_keys( $mdw_pbd_roles->roles ) as $mdw_pbd_role_name ) {
		$mdw_pbd_role = get_role( $mdw_pbd_role_name );
		if ( null !== $mdw_pbd_role ) {
			$mdw_pbd_role->remove_cap( 'mdw_pbd_manage' );
			$mdw_pbd_role->remove_cap( 'mdw_pbd_generate' );
		}
	}
};

if ( is_multisite() ) {
	$mdw_pbd_site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);
	foreach ( $mdw_pbd_site_ids as $mdw_pbd_site_id ) {
		switch_to_blog( (int) $mdw_pbd_site_id );
		$mdw_pbd_cleanup_site();
		restore_current_blog();
	}
} else {
	$mdw_pbd_cleanup_site();
}

delete_site_option( 'mdw_pbd_settings' );
delete_site_option( 'mdw_pbd_model_catalog' );
