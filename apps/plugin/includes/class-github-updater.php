<?php
/**
 * Native WordPress updates backed by GitHub Releases.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

use JsonException;

final class GitHub_Updater {
	private const UPDATE_URI = 'https://github.com/MrDemonWolf/promptbridge-for-divi';
	private const API_URL    = 'https://api.github.com/repos/MrDemonWolf/promptbridge-for-divi/releases/latest';
	private const CACHE_KEY  = 'mdw_pbd_github_release';

	/**
	 * Register the native Update URI provider.
	 */
	public function register(): void {
		add_filter( 'update_plugins_github.com', array( $this, 'filter_update' ), 10, 4 );
	}

	/**
	 * Supply update metadata for this plugin only.
	 *
	 * @param array<string, mixed>|false $update Existing update data.
	 * @param array<string, mixed>       $plugin_data Plugin headers.
	 * @param string                     $plugin_file Plugin basename.
	 * @param string[]                   $locales Requested locales.
	 * @return array<string, mixed>|false
	 */
	public function filter_update( array|false $update, array $plugin_data, string $plugin_file, array $locales ): array|false {
		unset( $locales );

		if ( plugin_basename( MDW_PBD_FILE ) !== $plugin_file || self::UPDATE_URI !== ( $plugin_data['UpdateURI'] ?? '' ) ) {
			return $update;
		}

		$parsed = $this->latest_release();

		if ( null === $parsed || ! version_compare( $parsed['version'], MDW_PBD_VERSION, '>' ) ) {
			return $update;
		}

		return array(
			'version'      => $parsed['version'],
			'url'          => $parsed['url'],
			'package'      => $parsed['package'],
			'requires_php' => '8.3',
			'autoupdate'   => false,
		);
	}

	/**
	 * Parse trusted fields from a GitHub release response.
	 *
	 * @param array<string, mixed> $release GitHub release response.
	 * @return array{version: string, url: string, package: string}|null
	 */
	public static function parse_release( array $release ): ?array {
		$version = ltrim( (string) ( $release['tag_name'] ?? '' ), 'v' );
		$assets  = $release['assets'] ?? null;

		if ( '' === $version || ! is_array( $assets ) ) {
			return null;
		}

		$expected = 'promptbridge-for-divi-' . $version . '.zip';

		foreach ( $assets as $asset ) {
			if ( ! is_array( $asset ) || ( $asset['name'] ?? '' ) !== $expected ) {
				continue;
			}

			$package = (string) ( $asset['browser_download_url'] ?? '' );
			$parts   = wp_parse_url( $package );

			if ( ! is_array( $parts ) || 'https' !== ( $parts['scheme'] ?? '' ) || 'github.com' !== ( $parts['host'] ?? '' ) ) {
				return null;
			}

			return array(
				'version' => $version,
				'url'     => self::UPDATE_URI . '/releases/tag/' . rawurlencode( (string) $release['tag_name'] ),
				'package' => $package,
			);
		}

		return null;
	}

	/**
	 * Fetch and cache the latest public release.
	 *
	 * @return array{version: string, url: string, package: string}|null
	 */
	private function latest_release(): ?array {
		$cached = get_site_transient( self::CACHE_KEY );

		if ( is_array( $cached ) && isset( $cached['version'], $cached['url'], $cached['package'] ) ) {
			return $cached;
		}

		$response = wp_remote_get(
			self::API_URL,
			array(
				'headers' => array(
					'Accept'     => 'application/vnd.github+json',
					'User-Agent' => 'PromptBridge-for-Divi/' . MDW_PBD_VERSION,
				),
				'timeout' => 5,
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}

		try {
			$release = json_decode( wp_remote_retrieve_body( $response ), true, 32, JSON_THROW_ON_ERROR );
		} catch ( JsonException ) {
			return null;
		}

		if ( ! is_array( $release ) ) {
			return null;
		}

		$parsed = self::parse_release( $release );

		if ( null === $parsed ) {
			return null;
		}

		set_site_transient( self::CACHE_KEY, $parsed, 12 * HOUR_IN_SECONDS );

		return $parsed;
	}
}
