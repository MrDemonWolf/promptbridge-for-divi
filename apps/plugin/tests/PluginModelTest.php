<?php
/**
 * Dynamic plugin model selection tests.
 */

declare(strict_types=1);

namespace {
	function get_option( string $option, mixed $default = false ): mixed {
		return $GLOBALS['mdw_pbd_test_options'][ $option ] ?? $default;
	}

	function update_option( string $option, mixed $value, mixed $autoload = null ): bool {
		$GLOBALS['mdw_pbd_test_options'][ $option ] = $value;
		return true;
	}

	function user_can( mixed $user, string $capability ): bool {
		return 1 === $user && 'mdw_pbd_manage' === $capability;
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

		public function test_policy_acceptance_requires_current_versions_and_a_recorded_admin(): void {
			self::assertFalse( Plugin::legal_acceptance_current() );

			$GLOBALS['mdw_pbd_test_options'][ Plugin::OPTION_SETTINGS ]['legal_acceptance'] = array(
				'terms_version'   => Plugin::TERMS_VERSION,
				'privacy_version' => Plugin::PRIVACY_VERSION,
				'accepted_at'     => '2026-10-02T12:00:00+00:00',
				'accepted_by'     => 1,
			);
			self::assertTrue( Plugin::legal_acceptance_current() );

			$GLOBALS['mdw_pbd_test_options'][ Plugin::OPTION_SETTINGS ]['legal_acceptance']['privacy_version'] = 'older';
			self::assertFalse( Plugin::legal_acceptance_current() );
		}

		public function test_recording_policy_acceptance_resets_service_access_until_separately_enabled(): void {
			$GLOBALS['mdw_pbd_test_options'][ Plugin::OPTION_SETTINGS ]['service_consent'] = true;

			self::assertTrue( Plugin::record_legal_acceptance( 1 ) );
			self::assertTrue( Plugin::legal_acceptance_current() );
			self::assertFalse( Plugin::service_consent_granted() );
			self::assertSame(
				1,
				$GLOBALS['mdw_pbd_test_options'][ Plugin::OPTION_SETTINGS ]['legal_acceptance']['accepted_by']
			);
		}
	}
}
