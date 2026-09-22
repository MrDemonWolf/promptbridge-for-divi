# Test Results

Run date: 2026-09-22 (America/Chicago)

## Staging site evidence

The live staging site is **PromptBridge Divi Test** at `10.1.2`:

| Component            | Observed value                                                     |
| -------------------- | ------------------------------------------------------------------ |
| WordPress            | 7.1.1                                                              |
| PHP                  | 8.3.30                                                             |
| nginx                | 1.26.1                                                             |
| MySQL                | 8.4.0                                                              |
| Xdebug               | Enabled                                                            |
| Divi                 | 5.13.1, licensed, current, active                                  |
| Codex CLI            | 0.155.1; real `--version` and model-catalog probes passed          |
| Codex executable     | `/opt/homebrew/bin/codex`                                          |
| Dedicated Codex home | `/Users/nathanialhenniges/Sites/promptbridge-divi-test/codex-home` |

The dedicated Codex home was created with directory mode `0700` and the required
PromptBridge marker. The staging smoke checks also covered missing- Divi
activation, Divi diagnostics, consent/model UI, deactivation/reactivation,
uninstall cleanup, and the browser UI under Divi. The layout was verified in the
Divi context. These checks do not authenticate Codex, generate content, or alter
Divi controls.

The PromptBridge admin is a dedicated **Divi > PromptBridge** submenu with a
plugin-owned Divi-style header, tabs, field rows, toggle, and top actions. The
desktop layout was visually verified. The General, Diagnostics, and Advanced
tabs work; the UI **Run Diagnostics** action passes the Codex 0.155.1 probe,
refreshes the five-model catalog, and leaves Luna selected. Both admin forms
redirect after processing; refreshing the result page does not rerun Codex.

Divi Booster 5.8.1 was installed and temporarily activated only on Local for
comparison. It also uses a separate submenu rather than injecting into private
Theme Options. It was deactivated after the comparison, and no Divi Booster code
is part of PromptBridge or its package.

PHP and nginx logs contained no PromptBridge warnings or fatal errors during the
staging checks.

## Model catalog evidence

With consent enabled, clicking **Run diagnostics** executed the fixed
`codex debug models --bundled` command. This is an explicit refresh action; no
background cron launch is used. The response is capped at 2 MB, strictly
projected and validated, and only list-visible API models are retained. The raw
443 KB catalog is not stored. A failed refresh preserves the last-good cache in
the non-autoloaded `mdw_pbd_model_catalog` option.

The current local Codex 0.155.1 result contains five models: Astra, Sol, Terra,
Luna, and 5.5. The stored option is 288 bytes with autoload disabled. Luna is
the default and fallback.

## Local toolchain

| Tool       | Version       |
| ---------- | ------------- |
| PHP        | 8.5.10        |
| Composer   | 2.10.3        |
| Bun        | 1.4.2         |
| Node.js    | 24.13.0       |
| Docker CLI | 29.8.1        |
| GitHub CLI | 2.101.0       |
| Codex CLI  | 0.155.1       |
| actionlint | 1.7.12        |
| WP-CLI     | Not installed |

## Completed checks

| Command                                           | Result                                                                                                                             |
| ------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------- |
| `bun install`                                     | Pass; 291 packages installed and `bun.lock` written.                                                                               |
| `composer install --no-interaction --no-progress` | Pass; 33 development packages installed from `composer.lock`.                                                                      |
| `composer check` in `apps/plugin`                 | Pass; PHPCS, PHPStan level 8, and PHPUnit completed.                                                                               |
| `composer audit --locked --no-interaction`        | Pass; no known security advisories.                                                                                                |
| `bun run check`                                   | Pass; all 8/8 workspace tasks completed, including formatting, linting, type checks, tests, docs export, and plugin package build. |
| `actionlint`                                      | Pass for every workflow in `.github/workflows`.                                                                                    |
| YAML parse check                                  | Pass for workflows and Dependabot configuration.                                                                                   |
| `git diff --check`                                | Pass.                                                                                                                              |

## WordPress staging checks

| Check                                    | Result                                                                                                                                                                 |
| ---------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Missing-Divi activation and diagnostics  | Pass; the plugin reports the missing boundary without a fatal error.                                                                                                   |
| Licensed Divi activation and diagnostics | Pass on Divi 5.13.1.                                                                                                                                                   |
| Codex runtime version probe              | Pass against `/opt/homebrew/bin/codex` (0.155.1).                                                                                                                      |
| Consent and model UI                     | Checked in the staging admin; consent is required before the model refresh. UI Run Diagnostics passes Codex 0.155.1, refreshes five models, and leaves Luna selected.  |
| Settings and diagnostics redirects       | Pass; processing runs on the early page-load hook, redirects to GET, and browser refresh does not rerun Codex.                                                         |
| Model catalog refresh                    | Pass; fixed `codex debug models --bundled`, 2 MB cap, strict projection validation, list-visible models only, and last-good preservation.                              |
| Deactivate/reactivate/uninstall cleanup  | Pass; plugin-owned cleanup completed without removing external Codex state or published content.                                                                       |
| Divi browser UI placement and layout     | Pass; dedicated Divi > PromptBridge submenu, plugin-owned Divi-style controls, desktop layout, and General/Diagnostics/Advanced tabs verified.                         |
| Dark Mode for WP Dashboard 1.3.9         | Pass; live toolbar switching applies `body.dark-mode`, and PromptBridge maps its admin palette to the plugin's native `--dm-*` tokens without JavaScript.              |
| Plugin Check 2.1.0                       | Only remaining finding is forbidden `proc_open` at `class-runtime-probe.php` line 93. This is an unresolved WordPress.org reviewer/policy blocker, not a clean report. |

PHPUnit result: 14 tests, 43 assertions, 0 failures. `composer check` passes.

## Package evidence

- Artifact: `apps/plugin/build/promptbridge-for-divi-0.1.0-alpha.1.zip`
- SHA-256: `85372f74ebcb619564cc26b7ee066c02bb8b57ca73b215c1e819fe9a6d258e47`
- Reproducibility: pass; two deterministic builds produced the same SHA-256,
  with every file and directory timestamp normalized.
- Contents: one `promptbridge-for-divi/` root, 13 distribution files, 9 PHP
  files, `LICENSE`, and `NOTICE.txt`; no path traversal, symlinks, development
  dependencies, caches, or lint failures.

## Skipped or blocked checks

| Check                                                  | Reason                                                                                                                                                                           |
| ------------------------------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Codex account authentication and live generation       | Generation is intentionally not implemented until the protocol release, generated schema, policy review, and host acceptance gates are complete.                                 |
| Divi control mutation and hosted-AI separation         | The licensed Divi 5.13.1 UI and PromptBridge submenu were verified, but control mutation, preview/apply, and proof that Divi-hosted AI is not also invoked remain unimplemented. |
| Subscription-backed image output                       | The custom-client artifact flow is not proven.                                                                                                                                   |
| cPanel process and cron behavior                       | A target cPanel account was not provided.                                                                                                                                        |
| GitHub release, Pages deploy, and WordPress.org deploy | The repository is public and Pages is configured with `build_type=workflow`, but no approved push, tag, release approval, or publication has occurred.                           |

Mocks and static checks do not count as evidence for Divi, cPanel, Codex
account, image, WordPress.org, or production deployment behavior.
