<?php
/**
 * Model catalog tests.
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge\Tests;

use MrDemonWolf\PromptBridge\Model_Catalog;
use PHPUnit\Framework\TestCase;

final class ModelCatalogTest extends TestCase {
	public function test_parser_keeps_only_listed_api_models(): void {
		$result = Model_Catalog::parse(
			(string) json_encode(
				array(
					'models' => array(
						array(
							'slug'             => 'gpt-5.6-luna',
							'display_name'     => 'GPT-5.6 Luna',
							'visibility'       => 'list',
							'supported_in_api' => true,
						),
						array(
							'slug'             => 'hidden-model',
							'display_name'     => 'Hidden',
							'visibility'       => 'hide',
							'supported_in_api' => true,
						),
						array(
							'slug'             => 'unsupported-model',
							'display_name'     => 'Unsupported',
							'visibility'       => 'list',
							'supported_in_api' => false,
						),
					),
				)
			)
		);

		self::assertTrue( $result['ok'], $result['message'] );
		self::assertSame( array( 'gpt-5.6-luna' => 'GPT-5.6 Luna' ), $result['models'] );
	}

	public function test_projection_rejects_invalid_cached_models(): void {
		$projection = Model_Catalog::projection( array( 'gpt-5.6-luna' => 'GPT-5.6 Luna' ), 123, 'codex 1.2.3' );

		self::assertSame( $projection['models'], Model_Catalog::models_from_projection( $projection ) );
		self::assertNull( Model_Catalog::models_from_projection( array( 'models' => array( 'bad slug' => 'Bad' ) ) ) );
		self::assertNull( Model_Catalog::models_from_projection( array( 'models' => array( 'gpt-5.6-sol' => 'Sol' ) ) ) );
	}

	public function test_parser_requires_luna_as_the_safe_fallback(): void {
		$result = Model_Catalog::parse(
			(string) json_encode(
				array(
					'models' => array(
						array(
							'slug'             => 'gpt-5.6-sol',
							'display_name'     => 'GPT-5.6 Sol',
							'visibility'       => 'list',
							'supported_in_api' => true,
						),
					),
				)
			)
		);

		self::assertFalse( $result['ok'] );
		self::assertSame( 'missing_fallback', $result['code'] );
	}
}
