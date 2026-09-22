<?php
/**
 * Fixed-argument Codex runtime probes.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

/**
 * Runs bounded, shell-free local diagnostics.
 */
final class Runtime_Probe {
	private const MAX_OUTPUT_BYTES       = 4096;
	private const MAX_MODEL_OUTPUT_BYTES = 2097152;
	private const READ_CHUNK_BYTES       = 8192;

	/**
	 * @param float $timeout_seconds Maximum process duration.
	 */
	public function __construct( private readonly float $timeout_seconds = 3.0 ) {
	}

	/**
	 * Run `<absolute codex path> --version` with an isolated home.
	 *
	 * @param string $executable Canonical executable path.
	 * @param string $codex_home Canonical dedicated Codex home.
	 * @return array{ok: bool, code: string, message: string, version: string}
	 */
	public function version( string $executable, string $codex_home ): array {
		$run = $this->run( $executable, $codex_home, array( '--version' ), self::MAX_OUTPUT_BYTES );
		if ( ! $run['ok'] ) {
			return array(
				'ok'      => false,
				'code'    => $run['code'],
				'message' => $run['message'],
				'version' => '',
			);
		}

		$version = $this->safe_output( $run['output'], $executable, $codex_home );
		if ( '' === $version ) {
			return $this->failure( 'empty_output', 'Codex returned no version information.' );
		}

		return array(
			'ok'      => true,
			'code'    => 'ready',
			'message' => 'Codex returned a local version successfully.',
			'version' => $version,
		);
	}

	/**
	 * Run the fixed local bundled-model command.
	 *
	 * @param string $executable Canonical executable path.
	 * @param string $codex_home Canonical dedicated Codex home.
	 * @return array{ok: bool, code: string, message: string, output: string}
	 */
	public function models( string $executable, string $codex_home ): array {
		return $this->run(
			$executable,
			$codex_home,
			array( 'debug', 'models', '--bundled' ),
			self::MAX_MODEL_OUTPUT_BYTES
		);
	}

	/**
	 * Run one fixed-argument local process without a shell.
	 *
	 * @param list<string> $arguments
	 * @return array{ok: bool, code: string, message: string, output: string}
	 */
	private function run( string $executable, string $codex_home, array $arguments, int $max_output_bytes ): array {
		$descriptors = array(
			0 => array( 'pipe', 'r' ),
			1 => array( 'pipe', 'w' ),
			2 => array( 'pipe', 'w' ),
		);
		$environment = array(
			'CODEX_HOME' => $codex_home,
			'HOME'       => $codex_home,
			'LANG'       => 'C',
			'LC_ALL'     => 'C',
			'PATH'       => '/usr/local/bin:/usr/bin:/bin',
		);

		$process = @proc_open( // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.PHP.DiscouragedPHPFunctions.system_calls_proc_open -- Fixed argv local process; failure becomes a safe diagnostic.
			array_merge( array( $executable ), $arguments ),
			$descriptors,
			$pipes,
			$codex_home,
			$environment,
			array( 'bypass_shell' => true )
		);

		if ( ! is_resource( $process ) ) {
			return $this->run_failure( 'start_failed', 'Codex could not be started.' );
		}

		fclose( $pipes[0] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Process pipe, not WordPress content.
		stream_set_blocking( $pipes[1], false );
		stream_set_blocking( $pipes[2], false );

		$stdout    = '';
		$stderr    = '';
		$started   = microtime( true );
		$timed_out = false;
		$truncated = false;
		$status    = proc_get_status( $process );

		while ( $status['running'] ) {
			$this->drain_stream( $pipes[1], $stdout, $max_output_bytes, $truncated );
			$this->drain_stream( $pipes[2], $stderr, self::MAX_OUTPUT_BYTES );

			if ( microtime( true ) - $started >= $this->timeout_seconds ) {
				$timed_out = true;
				proc_terminate( $process );
				usleep( 100000 );
				$status = proc_get_status( $process );
				if ( $status['running'] ) {
					proc_terminate( $process, 9 );
				}
				break;
			}

			usleep( 10000 );
			$status = proc_get_status( $process );
		}

		if ( ! $timed_out ) {
			stream_set_blocking( $pipes[1], true );
			stream_set_blocking( $pipes[2], true );
			$this->drain_remainder( $pipes[1], $stdout, $max_output_bytes, $truncated );
			$this->drain_remainder( $pipes[2], $stderr, self::MAX_OUTPUT_BYTES );
		}
		fclose( $pipes[1] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Process pipe, not WordPress content.
		fclose( $pipes[2] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Process pipe, not WordPress content.

		$close_code = proc_close( $process );
		$exit_code  = $status['exitcode'] >= 0
			? (int) $status['exitcode']
			: $close_code;

		if ( $timed_out ) {
			return $this->run_failure( 'timeout', 'Codex did not return a result before the timeout.' );
		}

		if ( 0 !== $exit_code ) {
			$detail = $this->safe_output( $stderr, $executable, $codex_home );
			return $this->run_failure(
				'nonzero_exit',
				'' !== $detail ? 'Codex local command failed: ' . $detail : 'Codex local command failed.'
			);
		}

		if ( $truncated ) {
			return $this->run_failure( 'output_limit', 'Codex returned more output than the diagnostics limit.' );
		}

		return array(
			'ok'      => true,
			'code'    => 'ready',
			'message' => 'Codex returned a local result successfully.',
			'output'  => $stdout,
		);
	}

	/**
	 * Bound and redact process output before it reaches an administrator.
	 */
	private function safe_output( string $output, string $executable, string $codex_home ): string {
		$output = str_replace( array( $executable, $codex_home ), array( '[codex]', '[codex-home]' ), $output );
		$output = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $output ) ?? '';
		$line   = strtok( trim( $output ), "\r\n" );

		return false === $line ? '' : trim( $line );
	}

	/**
	 * Read one bounded chunk so the outer deadline is rechecked between reads.
	 *
	 * @param resource $stream Process pipe.
	 */
	private function drain_stream( mixed $stream, string &$output, int $max_output_bytes = self::MAX_OUTPUT_BYTES, ?bool &$truncated = null ): void {
		$chunk = stream_get_contents( $stream, self::READ_CHUNK_BYTES );
		if ( false === $chunk || '' === $chunk ) {
			return;
		}

		$remaining = $max_output_bytes - strlen( $output );
		if ( $remaining <= 0 ) {
			if ( null !== $truncated ) {
				$truncated = true;
			}
			return;
		}

		$output .= substr( $chunk, 0, $remaining );
		if ( strlen( $chunk ) > $remaining && null !== $truncated ) {
			$truncated = true;
		}
	}

	/**
	 * Read the bytes left in a closed process pipe without exceeding the cap.
	 *
	 * @param resource $stream Process pipe.
	 */
	private function drain_remainder( mixed $stream, string &$output, int $max_output_bytes, ?bool &$truncated = null ): void {
		$remaining = max( 0, $max_output_bytes - strlen( $output ) );
		$chunk     = stream_get_contents( $stream, $remaining + 1 );
		if ( false === $chunk || '' === $chunk ) {
			return;
		}

		$output .= substr( $chunk, 0, $remaining );
		if ( strlen( $chunk ) > $remaining && null !== $truncated ) {
			$truncated = true;
		}
	}

	/**
	 * @return array{ok: bool, code: string, message: string, output: string}
	 */
	private function run_failure( string $code, string $message ): array {
		return array(
			'ok'      => false,
			'code'    => $code,
			'message' => $message,
			'output'  => '',
		);
	}

	/**
	 * @return array{ok: bool, code: string, message: string, version: string}
	 */
	private function failure( string $code, string $message ): array {
		return array(
			'ok'      => false,
			'code'    => $code,
			'message' => $message,
			'version' => '',
		);
	}
}
