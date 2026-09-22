<?php
/**
 * Runtime path tests.
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge\Tests;

use MrDemonWolf\PromptBridge\Runtime_Path;
use PHPUnit\Framework\TestCase;

final class RuntimePathTest extends TestCase {
	public function test_rejects_relative_paths(): void {
		$result = Runtime_Path::inspect( 'codex' );

		self::assertFalse( $result['ok'] );
		self::assertSame( 'not_absolute', $result['code'] );
	}

	public function test_accepts_an_absolute_executable(): void {
		$result = Runtime_Path::inspect( PHP_BINARY );

		self::assertTrue( $result['ok'] );
		self::assertSame( realpath( PHP_BINARY ), $result['path'] );
	}
}

