<?php
/**
 * Dedicated Codex home validation.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

/**
 * Validates a server-owner-configured Codex home.
 */
final class Runtime_Home {
	/**
	 * Inspect a dedicated home without changing it.
	 *
	 * @param string $configured_path Configured absolute path.
	 * @param string $web_root       Canonicalizable WordPress root.
	 * @return array{ok: bool, code: string, message: string, path: string}
	 */
	public static function inspect( string $configured_path, string $web_root ): array {
		$path = trim( $configured_path );

		if ( '' === $path ) {
			return self::failure( 'not_configured', 'Define an absolute MDW_PBD_CODEX_HOME outside the public web root.' );
		}

		if ( str_contains( $path, "\0" ) || DIRECTORY_SEPARATOR !== $path[0] ) {
			return self::failure( 'not_absolute', 'The dedicated Codex home path must be absolute.' );
		}

		$canonical_path = realpath( $path );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_is_writable -- Runtime homes are outside WordPress filesystem transports.
		if ( false === $canonical_path || ! is_dir( $canonical_path ) || ! is_writable( $canonical_path ) ) {
			return self::failure( 'not_writable', 'The dedicated Codex home must exist and be writable by the PHP worker.' );
		}

		$canonical_web_root = realpath( $web_root );
		if (
			false !== $canonical_web_root
			&& (
				$canonical_path === $canonical_web_root
				|| str_starts_with( $canonical_path . DIRECTORY_SEPARATOR, $canonical_web_root . DIRECTORY_SEPARATOR )
			)
		) {
			return self::failure( 'inside_web_root', 'The dedicated Codex home is inside the public WordPress tree; move it outside the web root.' );
		}

		$marker      = $canonical_path . DIRECTORY_SEPARATOR . '.promptbridge-for-divi';
		$marker_size = is_file( $marker ) && ! is_link( $marker )
			? @filesize( $marker ) // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_system_operations_filesize -- A race becomes a failed validation.
			: false;
		if ( 0 !== $marker_size ) {
			return self::failure( 'invalid_marker', 'Add an empty regular .promptbridge-for-divi marker file to confirm this home is reserved for the plugin.' );
		}

		$permissions = @fileperms( $canonical_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_system_operations_fileperms -- A race becomes a failed validation.
		if ( false === $permissions || 0700 !== ( $permissions & 0777 ) ) {
			return self::failure( 'unsafe_permissions', 'Set the configured Codex home directory mode to 0700.' );
		}

		return array(
			'ok'      => true,
			'code'    => 'ready',
			'message' => 'The reserved Codex home exists outside the WordPress web root with private permissions.',
			'path'    => $canonical_path,
		);
	}

	/**
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
