<?php
/**
 * Read-only environment diagnostics.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

/**
 * Collects local compatibility facts without downloading or changing dependencies.
 */
final class Diagnostics {
	/**
	 * Collect current diagnostics.
	 *
	 * @param bool $run_probe Whether to run the explicit local Codex probes and refresh.
	 * @return list<array{key: string, label: string, status: string, detail: string}>
	 */
	public function collect( bool $run_probe = false ): array {
		$results = array();

		$results[] = $this->result(
			'php',
			'PHP',
			version_compare( PHP_VERSION, '8.3.0', '>=' ) ? 'pass' : 'fail',
			'Running PHP ' . PHP_VERSION . '; PromptBridge requires PHP 8.3 or newer.'
		);

		$wp_version = isset( $GLOBALS['wp_version'] ) ? (string) $GLOBALS['wp_version'] : 'unknown';
		$results[]  = $this->result(
			'wp',
			'WordPress',
			'unknown' === $wp_version || version_compare( $wp_version, '6.7', '<' ) ? 'warn' : 'pass',
			'Detected WordPress ' . $wp_version . '; live compatibility is not yet certified.'
		);

		$divi      = $this->divi_version();
		$results[] = $this->result(
			'divi',
			'Divi 5',
			'' === $divi ? 'warn' : 'pass',
			'' === $divi
				? 'Divi was not detected. The plugin remains safe to activate, but builder integration is unavailable.'
				: 'Detected Divi version ' . $divi . '; editor actions are not implemented in this alpha yet.'
		);

		$process_available = $this->process_available();
		$results[]         = $this->result(
			'process',
			'Local processes',
			$process_available ? 'pass' : 'fail',
			$process_available
				? 'PHP can start a fixed-argument local process.'
				: 'The host disables proc_open; Codex integration cannot run on this host.'
		);

		$configured_path = defined( 'MDW_PBD_CODEX_PATH' ) && is_string( MDW_PBD_CODEX_PATH )
			? MDW_PBD_CODEX_PATH
			: '';
		$path_result     = Runtime_Path::inspect( $configured_path );
		$results[]       = $this->result(
			'codex_path',
			'Codex executable',
			$path_result['ok'] ? 'pass' : 'warn',
			$path_result['message']
		);

		$home_result = $this->inspect_codex_home();
		$results[]   = $this->result(
			'codex_home',
			'Dedicated Codex home',
			$home_result['ok'] ? 'pass' : 'warn',
			$home_result['message']
		);

		$consent   = Plugin::service_consent_granted();
		$results[] = $this->result(
			'consent',
			'External service consent',
			$consent ? 'pass' : 'warn',
			$consent
				? 'An administrator has opted in to Codex service use.'
				: 'No service use is allowed until an administrator reviews the disclosure and opts in.'
		);

		if ( ! $run_probe ) {
			$results[] = $this->result(
				'codex_version',
				'Codex version',
				'info',
				'Run diagnostics to perform the explicit, local version check.'
			);
			$results[] = $this->result(
				'codex_models',
				'Codex model catalog',
				'info',
				'Run diagnostics to refresh the local bundled model catalog. No process runs on page load.'
			);
			return $results;
		}

		if ( ! $process_available || ! $path_result['ok'] || ! $home_result['ok'] ) {
			$results[] = $this->result(
				'codex_version',
				'Codex version',
				'warn',
				'The version check was skipped until consent and all local runtime prerequisites pass.'
			);
			$results[] = $this->result(
				'codex_models',
				'Codex model catalog',
				'warn',
				'The model catalog refresh was skipped until the local process, executable, and private home checks pass.'
			);
			return $results;
		}

		$probe = new Runtime_Probe();
		if ( $consent ) {
			$version_probe = $probe->version( $path_result['path'], $home_result['path'] );
			$detail        = $version_probe['message'];
			$detail       .= $version_probe['ok'] ? ' Reported value: ' . $version_probe['version'] : '';
			$results[]     = $this->result(
				'codex_version',
				'Codex version',
				$version_probe['ok'] ? 'pass' : 'fail',
				$detail
			);
		} else {
			$version_probe = array(
				'ok'      => false,
				'version' => '',
			);
			$results[]     = $this->result(
				'codex_version',
				'Codex version',
				'warn',
				'The version check was skipped until an administrator grants service consent.'
			);
			$results[]     = $this->result(
				'codex_models',
				'Codex model catalog',
				'warn',
				'The model catalog refresh was skipped until an administrator grants service consent.'
			);
			return $results;
		}

		$models_probe = $probe->models( $path_result['path'], $home_result['path'] );
		if ( ! $models_probe['ok'] ) {
			$results[] = $this->result(
				'codex_models',
				'Codex model catalog',
				'fail',
				'Model catalog refresh failed: ' . $models_probe['message'] . ' The last good catalog was kept.'
			);
			return $results;
		}

		$catalog = Model_Catalog::parse( $models_probe['output'] );
		if ( ! $catalog['ok'] ) {
			$results[] = $this->result(
				'codex_models',
				'Codex model catalog',
				'fail',
				'Model catalog refresh failed: ' . $catalog['message'] . ' The last good catalog was kept.'
			);
			return $results;
		}

		$projection = Model_Catalog::projection( $catalog['models'], time(), $version_probe['version'] );
		if ( ! function_exists( 'update_option' ) || ! function_exists( 'get_option' ) ) {
			$results[] = $this->result(
				'codex_models',
				'Codex model catalog',
				'fail',
				'The validated model catalog could not be saved. The last good catalog was kept.'
			);
			return $results;
		}

		$updated = update_option( Model_Catalog::OPTION_NAME, $projection, false );
		if ( false === $updated && get_option( Model_Catalog::OPTION_NAME, null ) !== $projection ) {
			$results[] = $this->result(
				'codex_models',
				'Codex model catalog',
				'fail',
				'The validated model catalog could not be saved. The last good catalog was kept.'
			);
			return $results;
		}
		$results[] = $this->result(
			'codex_models',
			'Codex model catalog',
			'pass',
			'Refreshed the local bundled model catalog (' . count( $catalog['models'] ) . ' supported models).'
		);

		return $results;
	}

	/**
	 * Detect a loaded Divi version without assuming a specific entitlement.
	 */
	private function divi_version(): string {
		$candidates = array( 'ET_BUILDER_VERSION', 'ET_CORE_VERSION' );
		foreach ( $candidates as $constant ) {
			if ( defined( $constant ) ) {
				$value = constant( $constant );
				if ( is_scalar( $value ) ) {
					return (string) $value;
				}
			}
		}

		$theme = wp_get_theme();
		if ( 'Divi' === $theme->get( 'Name' ) || 'Divi' === $theme->get_template() ) {
			return (string) $theme->get( 'Version' );
		}

		return '';
	}

	/**
	 * Check whether proc_open is callable and not disabled by php.ini.
	 */
	private function process_available(): bool {
		if ( ! function_exists( 'proc_open' ) ) {
			return false;
		}

		$disabled = array_map( 'trim', explode( ',', (string) ini_get( 'disable_functions' ) ) );
		return ! in_array( 'proc_open', $disabled, true );
	}

	/**
	 * Inspect the dedicated Codex home configured in wp-config.php.
	 *
	 * @return array{ok: bool, code: string, message: string, path: string}
	 */
	private function inspect_codex_home(): array {
		$configured = defined( 'MDW_PBD_CODEX_HOME' ) && is_string( MDW_PBD_CODEX_HOME )
			? trim( MDW_PBD_CODEX_HOME )
			: '';

		return Runtime_Home::inspect( $configured, ABSPATH );
	}

	/**
	 * @return array{key: string, label: string, status: string, detail: string}
	 */
	private function result( string $key, string $label, string $status, string $detail ): array {
		return array(
			'key'    => $key,
			'label'  => $label,
			'status' => $status,
			'detail' => $detail,
		);
	}
}
