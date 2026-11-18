<?php
/** App-server protocol boundary test. */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge\Tests;

use MrDemonWolf\PromptBridge\App_Server;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class AppServerTest extends TestCase {
	#[TestWith( [ true ] )]
	#[TestWith( [ false ] )]
	public function test_text_turn_requires_confirmed_restricted_profile( bool $confirm_profile ): void {
		if ( ! function_exists( 'proc_open' ) || '\\' === DIRECTORY_SEPARATOR ) {
			self::markTestSkipped( 'Unix proc_open required.' );
		}
		$fixture = tempnam( sys_get_temp_dir(), 'mdw-pbd-app-' );
		self::assertIsString( $fixture );
		file_put_contents(
			$fixture,
			str_replace(
				'PROFILE_CONFIRMATION',
				$confirm_profile ? '$profile' : 'unconfirmed',
				<<<'SH'
#!/bin/sh
[ "$1" = app-server ] && [ "$2" = --listen ] && [ "$3" = 'stdio://' ] && [ "$4" = '-c' ] || exit 2
case "$5" in 'default_permissions="mdw_promptbridge_'*'"') ;; *) exit 2;; esac
profile=${5#default_permissions=\"}
profile=${profile%\"}
[ "$6" = '-c' ] && [ "$7" = "permissions.$profile.filesystem={\":minimal\"=\"read\",\"$0\"=\"read\"}" ] || exit 2
[ "$8" = '-c' ] && [ "$9" = "permissions.$profile.network.enabled=false" ] || exit 2
IFS= read -r line
case "$line" in *'"method":"initialize"'*'"experimentalApi":true'*) ;; *) exit 3;; esac
printf '%s\n' '{"id":1,"result":{"userAgent":"test"}}'
IFS= read -r line
case "$line" in *'"method":"initialized"'*) ;; *) exit 4;; esac
IFS= read -r line
case "$line" in *'"method":"thread/start"'*'"approvalPolicy":"never"'*'"permissions":"'"$profile"'"'*'"ephemeral":true'*) ;; *) exit 5;; esac
printf '{"id":2,"result":{"thread":{"id":"thread_1"},"activePermissionProfile":{"id":"%s","extends":null},"approvalPolicy":"never","sandbox":{"type":"readOnly","networkAccess":false}}}\n' "PROFILE_CONFIRMATION"
IFS= read -r line
case "$line" in *'"sandboxPolicy"'*) exit 6;; *'"method":"turn/start"'*) ;; *) exit 6;; esac
printf '%s\n' '{"id":3,"result":{"turn":{"id":"turn_1"}}}'
printf '%s\n' '{"method":"item/completed","params":{"item":{"type":"agentMessage","text":"Safe suggestion"}}}'
printf '%s\n' '{"method":"turn/completed","params":{"threadId":"thread_1","turn":{"id":"turn_1","status":"completed"}}}'
SH
			)
		);
		chmod( $fixture, 0700 );
		try {
			$result = ( new App_Server( 2.0 ) )->generate( $fixture, sys_get_temp_dir(), 'Suggest a headline', 'gpt-6-sol' );
			$expected = $confirm_profile
				? array( 'ok' => true, 'code' => 'ready', 'text' => 'Safe suggestion' )
				: array( 'ok' => false, 'code' => 'sandbox_unavailable', 'text' => '' );
			self::assertSame( $expected, $result );
		} finally {
			unlink( $fixture );
		}
	}
}
