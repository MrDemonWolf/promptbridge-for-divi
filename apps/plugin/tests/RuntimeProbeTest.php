<?php
/**
 * Runtime probe tests.
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge\Tests;

use MrDemonWolf\PromptBridge\Runtime_Probe;
use PHPUnit\Framework\TestCase;

final class RuntimeProbeTest extends TestCase {
	public function test_drain_has_a_per_iteration_read_budget(): void {
		$stream = fopen( 'php://temp', 'w+' );
		self::assertIsResource( $stream );
		fwrite( $stream, str_repeat( 'x', 16384 ) );
		rewind( $stream );

		$output = '';
		$method = ( new \ReflectionClass( Runtime_Probe::class ) )->getMethod( 'drain_stream' );
		$args   = array( $stream, &$output );
		$method->invokeArgs( new Runtime_Probe(), $args );

		self::assertSame( 8192, ftell( $stream ) );
		self::assertSame( 4096, strlen( $output ) );
		fclose( $stream );
	}

	public function test_reads_a_version_without_a_shell(): void {
		if ( ! function_exists( 'proc_open' ) ) {
			self::markTestSkipped( 'proc_open is unavailable.' );
		}

		$result = ( new Runtime_Probe() )->version( PHP_BINARY, sys_get_temp_dir() );

		self::assertTrue( $result['ok'], $result['message'] );
		self::assertStringContainsString( 'PHP', $result['version'] );
	}

	public function test_models_uses_the_fixed_bundled_catalog_arguments(): void {
		if ( ! function_exists( 'proc_open' ) || '\\' === DIRECTORY_SEPARATOR ) {
			self::markTestSkipped( 'This command fixture requires proc_open on a Unix-like host.' );
		}

		$fixture = tempnam( sys_get_temp_dir(), 'mdw-pbd-' );
		self::assertIsString( $fixture );
		file_put_contents(
			$fixture,
			<<<'SH'
#!/bin/sh
[ "$1" = debug ] && [ "$2" = models ] && [ "$3" = --bundled ] || exit 9
printf '%s' '{"models":[{"slug":"gpt-5.6-luna","display_name":"GPT-5.6 Luna","visibility":"list","supported_in_api":true}]}'
SH
		);
		chmod( $fixture, 0700 );

		try {
			$result = ( new Runtime_Probe() )->models( $fixture, sys_get_temp_dir() );
			self::assertTrue( $result['ok'], $result['message'] );
			self::assertStringContainsString( 'gpt-5.6-luna', $result['output'] );
		} finally {
			unlink( $fixture );
		}
	}

	public function test_models_drains_large_output_after_process_exit(): void {
		if ( ! function_exists( 'proc_open' ) || '\\' === DIRECTORY_SEPARATOR ) {
			self::markTestSkipped( 'This command fixture requires proc_open on a Unix-like host.' );
		}

		$fixture = tempnam( sys_get_temp_dir(), 'mdw-pbd-' );
		self::assertIsString( $fixture );
		file_put_contents(
			$fixture,
			<<<'SH'
#!/bin/sh
[ "$1" = debug ] && [ "$2" = models ] && [ "$3" = --bundled ] || exit 9
printf '%s' '{"models":[{"slug":"gpt-5.6-luna","display_name":"GPT-5.6 Luna","visibility":"list","supported_in_api":true}],"padding":"'
head -c 600000 /dev/zero | tr '\0' x
printf '%s' '"}'
SH
		);
		chmod( $fixture, 0700 );

		try {
			$result = ( new Runtime_Probe() )->models( $fixture, sys_get_temp_dir() );
			self::assertTrue( $result['ok'], $result['message'] );
			self::assertGreaterThan( 600000, strlen( $result['output'] ) );
			self::assertStringEndsWith( '"}', $result['output'] );
		} finally {
			unlink( $fixture );
		}
	}

	public function test_terminates_a_stalled_process(): void {
		if ( ! function_exists( 'proc_open' ) || '\\' === DIRECTORY_SEPARATOR ) {
			self::markTestSkipped( 'This timeout fixture requires proc_open on a Unix-like host.' );
		}

		$fixture = tempnam( sys_get_temp_dir(), 'mdw-pbd-' );
		self::assertIsString( $fixture );
		file_put_contents( $fixture, "#!/bin/sh\nexec sleep 5\n" );
		chmod( $fixture, 0700 );

		try {
			$result = ( new Runtime_Probe( 0.05 ) )->version( $fixture, sys_get_temp_dir() );
			self::assertFalse( $result['ok'] );
			self::assertSame( 'timeout', $result['code'] );
		} finally {
			unlink( $fixture );
		}
	}

	public function test_does_not_wait_for_a_descendant_holding_output_pipes(): void {
		if ( ! function_exists( 'proc_open' ) || '\\' === DIRECTORY_SEPARATOR ) {
			self::markTestSkipped( 'This descendant fixture requires proc_open on a Unix-like host.' );
		}

		$fixture = tempnam( sys_get_temp_dir(), 'mdw-pbd-' );
		self::assertIsString( $fixture );
		file_put_contents(
			$fixture,
			"#!/bin/sh\n(sleep 2) &\nprintf '%s' '{\"models\":[]}'\n"
		);
		chmod( $fixture, 0700 );

		try {
			$started = microtime( true );
			$result  = ( new Runtime_Probe() )->models( $fixture, sys_get_temp_dir() );
			self::assertTrue( $result['ok'], $result['message'] );
			self::assertLessThan( 1.0, microtime( true ) - $started );
		} finally {
			unlink( $fixture );
		}
	}
}
