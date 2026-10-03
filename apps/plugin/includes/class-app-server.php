<?php
/**
 * Bounded, local Codex app-server text request.
 *
 * @package MrDemonWolf\PromptBridge
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge;

/** One process and one read-only turn per request. */
final class App_Server {
	private const MAX_PROMPT_BYTES = 8192;
	private const MAX_TEXT_BYTES   = 65536;
	private const MAX_WIRE_BYTES   = 262144;
	private const MAX_LINE_BYTES   = 65536;

	public function __construct( private readonly float $timeout_seconds = 30.0 ) {
	}

	/**
	 * @return array{ok: bool, code: string, text: string}
	 */
	public function generate( string $executable, string $codex_home, string $prompt, string $model ): array {
		if ( '' === trim( $prompt ) || strlen( $prompt ) > self::MAX_PROMPT_BYTES || 1 !== preg_match( '//u', $prompt ) ) {
			return $this->failure( 'invalid_prompt' );
		}
		if ( ! preg_match( '/\A[a-zA-Z0-9][a-zA-Z0-9._-]{0,99}\z/', $model ) ) {
			return $this->failure( 'invalid_model' );
		}
		if ( ! is_file( $executable ) || ! is_executable( $executable ) || ! is_dir( $codex_home ) ) {
			return $this->failure( 'runtime_unavailable' );
		}

		$process = @proc_open( // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.PHP.DiscouragedPHPFunctions.system_calls_proc_open -- Fixed argv, no shell; failure is returned safely.
			array( $executable, 'app-server', '--listen', 'stdio://' ),
			array(
				0 => array( 'pipe', 'r' ),
				1 => array( 'pipe', 'w' ),
				2 => array( 'pipe', 'w' ),
			),
			$pipes,
			$codex_home,
			array(
				'CODEX_HOME' => $codex_home,
				'HOME'       => $codex_home,
				'LANG'       => 'C.UTF-8',
				'PATH'       => '/usr/local/bin:/usr/bin:/bin',
			),
			array( 'bypass_shell' => true )
		);
		if ( ! is_resource( $process ) ) {
			return $this->failure( 'start_failed' );
		}

		stream_set_blocking( $pipes[1], false );
		stream_set_blocking( $pipes[2], false );
		$deadline = microtime( true ) + $this->timeout_seconds;
		$buffer   = '';
		$bytes    = 0;
		$text     = '';
		$thread   = '';
		$turn     = '';

		try {
			$initialize = array(
				'id'     => 1,
				'method' => 'initialize',
				'params' => array(
					'clientInfo' => array(
						'name'    => 'mdw_promptbridge',
						'title'   => 'PromptBridge for Divi',
						'version' => '0.1.0',
					),
				),
			);
			if ( ! $this->send( $pipes[0], $initialize ) ) {
				return $this->failure( 'write_failed' );
			}
			while ( microtime( true ) < $deadline ) {
					$read   = array( $pipes[1], $pipes[2] );
					$write  = null;
					$except = null;
					$ready  = @stream_select( $read, $write, $except, 0, 100000 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Broken pipe becomes a safe error.
				if ( false === $ready ) {
					return $this->failure( 'read_failed' );
				}
				foreach ( $read as $stream ) {
					$chunk = stream_get_contents( $stream, 8192 );
					if ( false === $chunk || '' === $chunk ) {
						continue;
					}
					$bytes += strlen( $chunk );
					if ( $bytes > self::MAX_WIRE_BYTES ) {
						return $this->failure( 'output_limit' );
					}
					if ( $stream === $pipes[2] ) {
						continue; // Never surface stderr: it may contain secrets or untrusted data.
					}
					$buffer .= $chunk;
					$end     = strpos( $buffer, "\n" );
					while ( false !== $end ) {
						if ( $end > self::MAX_LINE_BYTES ) {
							return $this->failure( 'output_limit' );
						}
						$line   = substr( $buffer, 0, $end );
						$buffer = substr( $buffer, $end + 1 );
						try {
							$message = json_decode( $line, true, 32, JSON_THROW_ON_ERROR );
						} catch ( \JsonException ) {
							return $this->failure( 'protocol_error' );
						}
						if ( ! is_array( $message ) || isset( $message['error'] ) ) {
							return $this->failure( 'protocol_error' );
						}
						if ( 1 === ( $message['id'] ?? null ) ) {
							$start_thread = array(
								'id'     => 2,
								'method' => 'thread/start',
								'params' => array(
									'model'          => $model,
									'cwd'            => $codex_home,
									'approvalPolicy' => 'never',
									'sandbox'        => 'read-only',
								),
							);
							$initialized  = array(
								'method' => 'initialized',
								'params' => new \stdClass(),
							);
							if ( ! isset( $message['result'] ) || ! $this->send( $pipes[0], $initialized ) || ! $this->send( $pipes[0], $start_thread ) ) {
								return $this->failure( 'protocol_error' );
							}
						} elseif ( 2 === ( $message['id'] ?? null ) ) {
							$thread     = $message['result']['thread']['id'] ?? '';
							$start_turn = array(
								'id'     => 3,
								'method' => 'turn/start',
								'params' => array(
									'threadId'       => $thread,
									'input'          => array(
										array(
											'type' => 'text',
											'text' => $prompt,
										),
									),
									'sandboxPolicy'  => array(
										'type'   => 'readOnly',
										'access' => array(
											'type' => 'restricted',
											'includePlatformDefaults' => true,
											'readableRoots' => array(),
										),
									),
									'approvalPolicy' => 'never',
									'model'          => $model,
								),
							);
							if ( ! is_string( $thread ) || '' === $thread || ! $this->send( $pipes[0], $start_turn ) ) {
								return $this->failure( 'protocol_error' );
							}
						} elseif ( 3 === ( $message['id'] ?? null ) ) {
							$turn = $message['result']['turn']['id'] ?? '';
							if ( ! is_string( $turn ) || '' === $turn ) {
								return $this->failure( 'protocol_error' );
							}
						} elseif ( 'item/completed' === ( $message['method'] ?? null ) ) {
							$item = $message['params']['item'] ?? null;
							if ( is_array( $item ) && 'agentMessage' === ( $item['type'] ?? null ) && is_string( $item['text'] ?? null ) ) {
								$text = $item['text'];
								if ( strlen( $text ) > self::MAX_TEXT_BYTES ) {
									return $this->failure( 'output_limit' );
								}
							}
						} elseif ( 'turn/completed' === ( $message['method'] ?? null ) ) {
							$completed = $message['params']['turn'] ?? null;
							if ( ! is_array( $completed ) || ( $completed['id'] ?? null ) !== $turn || ( $message['params']['threadId'] ?? null ) !== $thread ) {
								return $this->failure( 'protocol_error' );
							}
							return 'completed' === ( $completed['status'] ?? null ) && '' !== trim( $text )
								? array(
									'ok'   => true,
									'code' => 'ready',
									'text' => $text,
								)
								: $this->failure( 'turn_failed' );
						} elseif ( isset( $message['id'] ) ) {
							return $this->failure( 'protocol_error' ); // No server-initiated requests may be approved.
						}
						$end = strpos( $buffer, "\n" );
					}
					if ( strlen( $buffer ) > self::MAX_LINE_BYTES ) {
						return $this->failure( 'output_limit' );
					}
				}
				if ( ! proc_get_status( $process )['running'] && feof( $pipes[1] ) ) {
					return $this->failure( 'protocol_error' );
				}
			}
			return $this->failure( 'timeout' );
		} finally {
			$status = proc_get_status( $process );
			if ( $status['running'] ) {
				proc_terminate( $process );
				usleep( 100000 );
				if ( proc_get_status( $process )['running'] ) {
					proc_terminate( $process, 9 );
				}
			}
			foreach ( $pipes as $pipe ) {
				fclose( $pipe ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Process pipe.
			}
			proc_close( $process );
		}
	}

	/**
	 * @param resource             $pipe Process input pipe.
	 * @param array<string, mixed> $message JSON-RPC message.
	 */
	private function send( mixed $pipe, array $message ): bool {
		$json = json_encode( $message, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- No WordPress functions in process protocol client.
		return is_string( $json ) && strlen( $json ) + 1 === fwrite( $pipe, $json . "\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Process pipe.
	}

	/** @return array{ok: bool, code: string, text: string} */
	private function failure( string $code ): array {
		return array(
			'ok'   => false,
			'code' => $code,
			'text' => '',
		);
	}
}
