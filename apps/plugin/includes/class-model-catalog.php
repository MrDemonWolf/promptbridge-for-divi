<?php
/**
 * Validated Codex model catalog projection.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

/**
 * Parses and validates the small model projection stored by the plugin.
 */
final class Model_Catalog {
	public const OPTION_NAME = 'mdw_pbd_model_catalog';

	private const MAX_MODELS      = 64;
	private const MAX_SLUG_BYTES  = 100;
	private const MAX_LABEL_BYTES = 120;

	/**
	 * Parse the JSON emitted by `codex debug models --bundled`.
	 *
	 * @return array{ok: bool, code: string, message: string, models: array<string, string>}
	 */
	public static function parse( string $output ): array {
		try {
			$decoded = json_decode( $output, true, 32, JSON_THROW_ON_ERROR );
		} catch ( \JsonException $exception ) {
			return self::failure( 'invalid_json', 'Codex returned an invalid model catalog.' );
		}

		if ( ! is_array( $decoded ) || ! isset( $decoded['models'] ) || ! is_array( $decoded['models'] ) ) {
			return self::failure( 'invalid_shape', 'Codex returned an unexpected model catalog.' );
		}

		if ( count( $decoded['models'] ) > self::MAX_MODELS ) {
			return self::failure( 'too_many_models', 'Codex returned too many models.' );
		}

		$models = array();
		foreach ( $decoded['models'] as $model ) {
			if ( ! is_array( $model ) ) {
				continue;
			}

			$slug  = $model['slug'] ?? null;
			$label = $model['display_name'] ?? null;
			if ( true !== ( $model['supported_in_api'] ?? false ) || 'list' !== ( $model['visibility'] ?? null ) ) {
				continue;
			}
			if ( ! is_string( $slug ) || ! self::valid_slug( $slug ) || ! is_string( $label ) || ! self::valid_label( $label ) ) {
				continue;
			}

			$models[ $slug ] = $label;
		}

		if ( ! isset( $models['gpt-5.6-luna'] ) ) {
			return self::failure( 'missing_fallback', 'Codex did not report the required Luna fallback model.' );
		}

		return array(
			'ok'      => true,
			'code'    => 'ready',
			'message' => 'Codex returned a validated model catalog.',
			'models'  => $models,
		);
	}

	/**
	 * Build the small non-autoloaded option projection.
	 *
	 * @param array<string, string> $models
	 * @return array{models: array<string, string>, fetched_at: int, codex_version: string}
	 */
	public static function projection( array $models, int $fetched_at, string $codex_version = '' ): array {
		return array(
			'models'        => $models,
			'fetched_at'    => max( 0, $fetched_at ),
			'codex_version' => substr( self::safe_text( $codex_version ), 0, self::MAX_LABEL_BYTES ),
		);
	}

	/**
	 * Validate a previously stored projection without requiring WordPress.
	 *
	 * @return array<string, string>|null
	 */
	public static function models_from_projection( mixed $value ): ?array {
		if ( ! is_array( $value ) || ! isset( $value['models'] ) || ! is_array( $value['models'] ) ) {
			return null;
		}

		$models = array();
		foreach ( $value['models'] as $slug => $label ) {
			if ( ! is_string( $slug ) || ! self::valid_slug( $slug ) || ! is_string( $label ) || ! self::valid_label( $label ) ) {
				return null;
			}
			$models[ $slug ] = $label;
		}

		if ( count( $models ) > self::MAX_MODELS || ! isset( $models['gpt-5.6-luna'] ) ) {
			return null;
		}

		return $models;
	}

	private static function valid_slug( string $slug ): bool {
		return strlen( $slug ) <= self::MAX_SLUG_BYTES && 1 === preg_match( '/\A[a-z0-9][a-z0-9._-]*\z/', $slug );
	}

	private static function valid_label( string $label ): bool {
		return '' !== trim( $label )
			&& strlen( $label ) <= self::MAX_LABEL_BYTES
			&& 1 !== preg_match( '/[\x00-\x1F\x7F]/', $label );
	}

	private static function safe_text( string $value ): string {
		return preg_replace( '/[\x00-\x1F\x7F]/', '', $value ) ?? '';
	}

	/**
	 * @return array{ok: bool, code: string, message: string, models: array<string, string>}
	 */
	private static function failure( string $code, string $message ): array {
		return array(
			'ok'      => false,
			'code'    => $code,
			'message' => $message,
			'models'  => array(),
		);
	}
}
