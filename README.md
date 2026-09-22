<p align="center">
  <img src="apps/docs/app/icon.svg" alt="PromptBridge wolf and bridge icon" width="128">
</p>

# PromptBridge for Divi - Cautious Codex Integration for Divi 5

PromptBridge for Divi is an independent WordPress plugin exploring a secure
bridge between Divi 5 and an independently installed official Codex runtime. It
is for site owners who want subscription-backed generation without turning the
plugin into a runtime installer or bypassing WordPress and Divi permissions.

Divi is a registered trademark of Elegant Themes, Inc. PromptBridge for Divi is
not affiliated with or endorsed by Elegant Themes or OpenAI.

Prove the boundary before trusting it.

## Features

- **Safe activation** - Runs without Divi, Codex, or process execution.
- **Focused diagnostics** - Reports PHP, WordPress, Divi, process, path, home,
  consent, and local Codex version status.
- **Server-owned configuration** - Reads fixed executable and home paths from
  `wp-config.php`, never ordinary browser input.
- **Bounded local process** - Uses fixed argv, no shell command, a timeout, and
  escaped, redacted output.
- **Explicit consent** - Blocks Codex launch until an administrator reviews the
  service disclosure and opts in.
- **Model catalog cache** - An explicit diagnostics run discovers only
  list-visible API models, validates a small projection, and preserves the
  last-good result without storing the raw catalog.
- **Dashboard dark mode** - Follows Dark Mode for WP Dashboard's live state and
  native color tokens without a second theme script.
- **Clean lifecycle** - Removes plugin-owned options, schedules, and
  capabilities while preserving content, media, Codex, and external credentials.
- **GitHub automation** - Checks plugin and docs changes, builds ZIP artifacts,
  deploys documentation, opens dependency updates, and publishes tagged releases
  that WordPress can discover through its native update system.

This staging alpha does not authenticate, generate content, alter Divi controls,
or claim WordPress.org approval. See the
[current limitations](apps/docs/content/docs/limitations.mdx).

## Getting Started

Read the
[published documentation](https://mrdemonwolf.github.io/promptbridge-for-divi/).
Plugin architecture, compatibility, privacy, hosting, and testing guidance lives
in [docs](docs/).

1. Download a ZIP produced by CI or a tagged GitHub Release.
2. Upload and activate it in WordPress.
3. Have the server owner install Codex independently.
4. Add the two server-owned constants shown below.
5. Open **Settings > PromptBridge**, review consent, and run diagnostics.

Run diagnostics is the only model-catalog refresh trigger. After consent, it
executes the fixed `codex debug models --bundled` command; it does not launch a
background cron job. The response is capped at 2 MB, strictly projected and
validated, and stored in the non-autoloaded `mdw_pbd_model_catalog` option. Only
list-visible API models are cached. The raw 443 KB catalog is never stored, and
a failed refresh keeps the last-good cache.

The current local Codex 0.155.1 result contains five models: Astra, Sol, Terra,
Luna, and 5.5. Luna is the default and fallback model. The selector labels each
model's estimated ChatGPT token credit rate relative to Luna and links to the
current OpenAI pricing page; actual use still depends on the task and context.

## Usage

Add private server paths to `wp-config.php`:

```php
define( 'MDW_PBD_CODEX_PATH', '/absolute/path/to/codex' );
define( 'MDW_PBD_CODEX_HOME', '/private/path/to/promptbridge-codex-home' );
```

The Codex home must exist outside the public WordPress tree, contain an empty
`.promptbridge-for-divi` marker, use directory mode `0700`, and be writable by
the PHP worker. Do not reuse a workstation's global Codex home.

## Tech Stack

| Layer          | Technology                                          |
| -------------- | --------------------------------------------------- |
| Plugin         | PHP 8.3+, WordPress 6.7+                            |
| Editor target  | Divi 5                                              |
| Runtime target | Official Codex CLI over local stdio                 |
| Documentation  | Next.js, Fumadocs, TypeScript                       |
| Workspace      | Bun, Turbo                                          |
| Quality        | PHPUnit, PHPStan, PHPCS, WordPress Coding Standards |
| Automation     | GitHub Actions, Dependabot, GitHub Pages            |

## Development

### Prerequisites

- PHP 8.3 or newer
- Composer 2
- Bun 1.4.2
- Node.js 20 or newer
- `rsync` and `zip` for plugin packaging

### Setup

1. Install workspace dependencies:

   ```bash
   bun install
   ```

2. Install plugin development dependencies:

   ```bash
   cd apps/plugin
   composer install
   ```

3. Run all local checks:

   ```bash
   cd ../..
   bun run check
   ```

4. Build the installable plugin ZIP:

   ```bash
   bun run plugin:zip
   ```

### Development Scripts

- `bun run dev` - Run workspace development tasks.
- `bun run docs:dev` - Start docs at `http://localhost:3001`.
- `bun run docs:build` - Build the static docs export.
- `bun run plugin:check` - Run plugin standards, analysis, and tests.
- `bun run plugin:zip` - Build the versioned ZIP under `apps/plugin/build/`.
- `bun run check` - Run workspace lint, type-check, tests, and builds.
- `bun run format` - Format supported source and documentation files.

### Code Quality

- WordPress Coding Standards for PHP.
- PHPStan level 8 for the process boundary.
- PHPUnit tests for path validation, fixed process execution, and timeout.
- Strict TypeScript for the docs app.
- Pinned GitHub Action commits with minimum workflow permissions.
- Reproducible ZIP timestamps and explicit distribution exclusions.

## Project Structure

```text
.
├── apps/
│   ├── docs/               # Fumadocs static documentation app
│   └── plugin/             # Distributable WordPress plugin
├── docs/                   # Research, architecture, security, and release evidence
├── .github/                # CI, Pages, releases, updates, and templates
├── AGENTS.md               # Repository-specific agent rules
├── package.json            # Bun workspace commands
└── turbo.json              # Workspace task graph
```

## License

![GitHub license](https://img.shields.io/github/license/mrdemonwolf/promptbridge-for-divi.svg?style=for-the-badge&logo=github)

Original code is licensed under GPL-2.0-or-later. See [LICENSE](LICENSE) and
[dependency notices](NOTICE.md).

## Contact

- Discord: [Join my server](https://mrdwolf.net/discord)
- Issues:
  [GitHub Issues](https://github.com/MrDemonWolf/promptbridge-for-divi/issues)
- Security:
  [Report a vulnerability privately](https://github.com/MrDemonWolf/promptbridge-for-divi/security/advisories/new)

Made with love by [MrDemonWolf, Inc.](https://www.mrdemonwolf.com)
