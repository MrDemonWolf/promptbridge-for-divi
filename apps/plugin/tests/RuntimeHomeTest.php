<?php
/**
 * Runtime home tests.
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge\Tests;

use MrDemonWolf\PromptBridge\Runtime_Home;
use PHPUnit\Framework\TestCase;

final class RuntimeHomeTest extends TestCase {
	public function test_requires_an_empty_marker_and_mode_0700(): void {
		if ( '\\' === DIRECTORY_SEPARATOR ) {
			self::markTestSkipped( 'The cPanel runtime target uses Unix permissions.' );
		}

		$suffix   = bin2hex( random_bytes( 8 ) );
		$home     = sys_get_temp_dir() . '/mdw-pbd-home-' . $suffix;
		$web_root = sys_get_temp_dir() . '/mdw-pbd-web-' . $suffix;
		self::assertTrue( mkdir( $home, 0700 ) );
		self::assertTrue( mkdir( $web_root, 0700 ) );
		self::assertIsInt( file_put_contents( $home . '/.promptbridge-for-divi', 'not empty' ) );

		try {
			self::assertSame( 'invalid_marker', Runtime_Home::inspect( $home, $web_root )['code'] );

			self::assertSame( 0, file_put_contents( $home . '/.promptbridge-for-divi', '' ) );
			self::assertTrue( chmod( $home, 0770 ) );
			clearstatcache( true, $home );
			self::assertSame( 'unsafe_permissions', Runtime_Home::inspect( $home, $web_root )['code'] );

			self::assertTrue( chmod( $home, 0700 ) );
			clearstatcache( true, $home );
			self::assertTrue( Runtime_Home::inspect( $home, $web_root )['ok'] );
		} finally {
			unlink( $home . '/.promptbridge-for-divi' );
			rmdir( $home );
			rmdir( $web_root );
		}
	}
}
