# Dependency Notices

PromptBridge for Divi original code is GPL-2.0-or-later.

## Runtime package

The current plugin ZIP contains no third-party PHP or JavaScript runtime
dependency. WordPress, Divi, and Codex are separate software and are not
bundled.

## Development and documentation

Development-only Composer and Bun packages keep their own licenses. Their exact
resolved versions and license metadata are recorded in `composer.lock` and
`bun.lock`. Before distribution, CI runs dependency audits and the release
review must confirm that the plugin ZIP excludes development dependencies.

Primary tools include PHPUnit, PHPStan, PHP_CodeSniffer, WordPress Coding
Standards, Next.js, React, Fumadocs, Tailwind CSS, TypeScript, and Turbo.
