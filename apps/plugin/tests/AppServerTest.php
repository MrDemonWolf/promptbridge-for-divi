<?php
/** App-server protocol boundary test. */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge\Tests;

use MrDemonWolf\PromptBridge\App_Server;
use PHPUnit\Framework\TestCase;

final class AppServerTest extends TestCase {
	public function test_one_read_only_text_turn_uses_fixed_stdio_protocol(): void {
		if ( ! function_exists( 'proc_open' ) || '\\' === DIRECTORY_SEPARATOR ) {
			self::markTestSkipped( 'Unix proc_open required.' );
		}
		$fixture = tempnam( sys_get_temp_dir(), 'mdw-pbd-app-' );
		self::assertIsString( $fixture );
		file_put_contents(
			$fixture,
			<<<'SH'
#!/bin/sh
[ "$1" = app-server ] && [ "$2" = --listen ] && [ "$3" = 'stdio://' ] || exit 2
IFS= read -r line
case "$line" in *'"method":"initialize"'*) ;; *) exit 3;; esac
printf '%s\n' '{"id":1,"result":{"userAgent":"test"}}'
IFS= read -r line
case "$line" in *'"method":"initialized"'*) ;; *) exit 4;; esac
IFS= read -r line
case "$line" in *'"method":"thread/start"'*'"sandbox":"read-only"'*) ;; *) exit 5;; esac
printf '%s\n' '{"id":2,"result":{"thread":{"id":"thread_1"}}}'
IFS= read -r line
case "$line" in *'"method":"turn/start"'*'"type":"readOnly"'*'"approvalPolicy":"never"'*) ;; *) exit 6;; esac
printf '%s\n' '{"id":3,"result":{"turn":{"id":"turn_1"}}}'
printf '%s\n' '{"method":"item/completed","params":{"item":{"type":"agentMessage","text":"Safe suggestion"}}}'
printf '%s\n' '{"method":"turn/completed","params":{"threadId":"thread_1","turn":{"id":"turn_1","status":"completed"}}}'
SH
		);
		chmod( $fixture, 0700 );
		try {
			$result = ( new App_Server( 2.0 ) )->generate( $fixture, sys_get_temp_dir(), 'Suggest a headline', 'gpt-6-sol' );
			self::assertSame( array( 'ok' => true, 'code' => 'ready', 'text' => 'Safe suggestion' ), $result );
		} finally {
			unlink( $fixture );
		}
	}
}
