<?php
/**
 * Dynamic plugin model selection tests.
 */

declare(strict_types=1);

namespace {
	function get_option( string $option, mixed $default = false ): mixed {
		return $GLOBALS['mdw_pbd_test_options'][ $option ] ?? $default;
	}

	require_once dirname( __DIR__ ) . '/includes/class-plugin.php';
}

namespace MrDemonWolf\PromptBridge\Tests {
	use MrDemonWolf\PromptBridge\Model_Catalog;
	use MrDemonWolf\PromptBridge\Plugin;
	use PHPUnit\Framework\TestCase;

	final class PluginModelTest extends TestCase {
		protected function setUp(): void {
			$GLOBALS['mdw_pbd_test_options'] = array(
				Model_Catalog::OPTION_NAME => Model_Catalog::projection(
					array(
						'gpt-5.6-luna' => 'GPT-5.6 Luna',
						'gpt-6-astra'   => 'GPT-6 Astra',
					),
					123
				),
				Plugin::OPTION_SETTINGS     => array( 'model' => 'gpt-6-astra' ),
			);
		}

		public function test_cached_model_can_be_selected(): void {
			self::assertTrue( Plugin::is_supported_model( 'gpt-6-astra' ) );
			self::assertSame( 'gpt-6-astra', Plugin::selected_model() );
		}
	}
}
