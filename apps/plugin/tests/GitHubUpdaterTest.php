<?php
/**
 * GitHub updater tests.
 */

declare(strict_types=1);

namespace MrDemonWolf\PromptBridge\Tests;

use MrDemonWolf\PromptBridge\GitHub_Updater;
use PHPUnit\Framework\TestCase;

final class GitHubUpdaterTest extends TestCase {
	public function test_parser_accepts_only_the_versioned_github_zip(): void {
		$release = array(
			'tag_name' => 'v0.2.0',
			'assets'   => array(
				array(
					'name'                 => 'promptbridge-for-divi-0.2.0.zip',
					'browser_download_url' => 'https://github.com/MrDemonWolf/promptbridge-for-divi/releases/download/v0.2.0/promptbridge-for-divi-0.2.0.zip',
				),
			),
		);

		self::assertSame(
			array(
				'version' => '0.2.0',
				'url'     => 'https://github.com/MrDemonWolf/promptbridge-for-divi/releases/tag/v0.2.0',
				'package' => 'https://github.com/MrDemonWolf/promptbridge-for-divi/releases/download/v0.2.0/promptbridge-for-divi-0.2.0.zip',
			),
			GitHub_Updater::parse_release( $release )
		);

		$release['assets'][0]['browser_download_url'] = 'https://example.com/plugin.zip';
		self::assertNull( GitHub_Updater::parse_release( $release ) );
	}
}
