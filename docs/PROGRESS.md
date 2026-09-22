# Progress

Updated: 2026-09-22

## Completed

- [x] Preserved clean initial repository and created `codex/initial-build`.
- [x] Adopted the house Bun/Turbo `apps/plugin` and `apps/docs` layout.
- [x] Added safe WordPress activation, dedicated capability, setup UI, consent,
      diagnostics, bounded local version probing, deactivation, and uninstall.
- [x] Added pure runtime checks for path validation, process success, and
      timeout.
- [x] Added Fumadocs static documentation with GitHub Pages output.
- [x] Confirmed official Codex, Divi, PHP, and WordPress policy facts from
      primary sources and separated open gates from feature claims.
- [x] Added GitHub-owned CI, docs, packaging, release, update, and gated
      WordPress.org deployment configuration.
- [x] Installed locked dependencies and passed coding standards, static
      analysis, tests, documentation export, and dependency audit.
- [x] Completed the integrated threat model and release evidence review.
- [x] Built and inspected the versioned staging-alpha ZIP twice with the same
      deterministic SHA-256:
      `85372f74ebcb619564cc26b7ee066c02bb8b57ca73b215c1e819fe9a6d258e47`.
- [x] Tested the local **PromptBridge Divi Test** staging site on WordPress
      7.1.1, PHP 8.3.30, nginx 1.26.1, MySQL 8.4.0, and Xdebug-enabled PHP.
- [x] Verified licensed/current Divi 5.13.1 activation, diagnostics, and the
      dedicated **Divi > PromptBridge** submenu with its plugin-owned Divi-style
      header, tabs, field rows, toggle, top actions, and desktop layout.
      General, Diagnostics, and Advanced tabs work without claiming control
      mutation or generation.
- [x] Installed and temporarily activated Divi Booster 5.8.1 only on Local for
      comparison, confirmed it also uses a separate submenu, then deactivated
      it. No Booster code is distributed or included.
- [x] Verified the real `/opt/homebrew/bin/codex` 0.155.1 version probe with a
      dedicated `0700` Codex home and marker.
- [x] Added and staged the consent-gated model catalog cache: **Run
      diagnostics** executes fixed `codex debug models --bundled`, applies a 2
      MB cap and strict projection validation, caches only list-visible API
      models, and preserves the last-good result. It does not launch from a
      background cron job or store the raw 443 KB catalog.
- [x] Verified the local 0.155.1 result contains Astra, Sol, Terra, Luna, and
      5.5; `mdw_pbd_model_catalog` stores 288 bytes with autoload disabled, and
      Luna is the default/fallback.
- [x] Completed consent/model UI checks and deactivate/reactivate/uninstall
      cleanup checks; no PromptBridge warnings or fatals appeared in PHP/nginx
      logs.
- [x] Expanded PHPUnit evidence to 14 tests and 43 assertions; `composer check`
      passes.
- [x] Full `bun run check` passes all 8/8 workspace tasks after the UI update.
- [x] Ran Plugin Check 2.1.0; the only remaining finding is forbidden
      `proc_open`, which remains a genuine WordPress.org reviewer/policy
      blocker.
- [x] Made the repository public and configured GitHub Pages for the Actions
      workflow at https://mrdemonwolf.github.io/promptbridge-for-divi/; the site
      still awaits an approved push.

## Blocked live gates

- [ ] Pin a Codex release and generate its matching schemas.
- [ ] Authenticate a dedicated test account and verify one live text turn.
- [ ] Implement and live-test the exact Divi control mutation path, including
      preview/apply and proof that Divi-hosted AI is not also invoked.
- [ ] Test target cPanel process, filesystem, cron, and timeout behavior.
- [ ] Verify custom-client image output or record it as unsupported.
- [ ] Receive WordPress Plugin Review Team guidance on local executable use.

No approved push, release, WordPress.org submission, reviewer email, or
production deploy has been performed.
