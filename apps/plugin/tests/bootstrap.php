<?php
/**
 * PHPUnit bootstrap for pure runtime boundary tests.
 */

declare(strict_types=1);

if ( ! function_exists( 'wp_parse_url' ) ) {
	/**
	 * Minimal WordPress URL parser shim for pure unit tests.
	 *
	 * @return array<string, int|string>|false
	 */
	function wp_parse_url( string $url ): array|false {
		return parse_url( $url );
	}
}

require_once dirname( __DIR__ ) . '/includes/class-runtime-path.php';
require_once dirname( __DIR__ ) . '/includes/class-runtime-home.php';
require_once dirname( __DIR__ ) . '/includes/class-runtime-probe.php';
require_once dirname( __DIR__ ) . '/includes/class-model-catalog.php';
require_once dirname( __DIR__ ) . '/includes/class-github-updater.php';
