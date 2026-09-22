<?php
/**
 * Runtime executable path validation.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

/**
 * Validates a server-owner-configured Codex executable path.
 */
final class Runtime_Path {
	/**
	 * Inspect an executable path without running it.
	 *
	 * @param string $configured_path Configured absolute path.
	 * @return array{ok: bool, code: string, message: string, path: string}
	 */
	public static function inspect( string $configured_path ): array {
		$path = trim( $configured_path );

		if ( '' === $path ) {
			return self::failure( 'not_configured', 'No Codex executable path is configured.' );
		}

		if ( str_contains( $path, "\0" ) ) {
			return self::failure( 'invalid_path', 'The configured path contains an invalid null byte.' );
		}

		if ( DIRECTORY_SEPARATOR !== $path[0] ) {
			return self::failure( 'not_absolute', 'The Codex executable path must be absolute.' );
		}

		$canonical_path = realpath( $path );
		if ( false === $canonical_path ) {
			return self::failure( 'not_found', 'The configured Codex executable does not exist.' );
		}

		if ( ! is_file( $canonical_path ) ) {
			return self::failure( 'not_file', 'The configured Codex path is not a file.' );
		}

		if ( ! is_executable( $canonical_path ) ) {
			return self::failure( 'not_executable', 'The configured Codex file is not executable.' );
		}

		return array(
			'ok'      => true,
			'code'    => 'ready',
			'message' => 'The configured Codex executable is present and executable.',
			'path'    => $canonical_path,
		);
	}

	/**
	 * Build a failed inspection result.
	 *
	 * @param string $code    Stable result code.
	 * @param string $message User-facing message.
	 * @return array{ok: bool, code: string, message: string, path: string}
	 */
	private static function failure( string $code, string $message ): array {
		return array(
			'ok'      => false,
			'code'    => $code,
			'message' => $message,
			'path'    => '',
		);
	}
}
