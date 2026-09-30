<?php
/**
 * Model selection tests.
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge\Tests;

use MrDemonWolf\PromptBridge\Plugin;
use PHPUnit\Framework\TestCase;

require_once dirname( __DIR__ ) . '/includes/class-plugin.php';

final class ModelSelectionTest extends TestCase {
	public function test_luna_is_the_default_model(): void {
		self::assertSame( 'gpt-5.6-luna', Plugin::DEFAULT_MODEL );
		self::assertArrayHasKey( Plugin::DEFAULT_MODEL, Plugin::supported_models() );
	}

	public function test_only_allowlisted_models_are_supported(): void {
		self::assertTrue( Plugin::is_supported_model( 'gpt-5.6-luna' ) );
		self::assertTrue( Plugin::is_supported_model( 'gpt-5.6-sol' ) );
		self::assertTrue( Plugin::is_supported_model( 'gpt-5.6-terra' ) );
		self::assertFalse( Plugin::is_supported_model( 'gpt-5.6-custom' ) );
	}
}
