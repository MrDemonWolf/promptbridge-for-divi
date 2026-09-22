# Testing

## Automated checks

```bash
bun install --frozen-lockfile
bun run format:check
bun run --filter @mrdemonwolf/promptbridge-docs typecheck
bun run --filter @mrdemonwolf/promptbridge-docs build
cd apps/plugin
composer validate --strict
composer audit --no-interaction
composer lint
composer analyse
composer test
bin/build-zip.sh
```

## Current automated coverage

- [x] Relative executable paths fail closed.
- [x] Canonical executable paths pass.
- [x] Dedicated homes require an empty marker and directory mode `0700`.
- [x] The fixed `--version` argv returns bounded output without a shell.
- [x] The fixed `debug models --bundled` argv returns a bounded, strictly
      projected catalog without a shell.
- [x] Model catalog projection enforces a 2 MB response cap, stores only
      list-visible API models, preserves the last-good cache, and uses a
      non-autoloaded option.
- [x] Each pipe drain has a fixed read budget so the deadline is rechecked.
- [x] A stalled process reaches the timeout path.
- [x] PHP coding standards and focused static analysis.
- [x] Docs strict type-check and static export.
- [x] Installable ZIP construction from distribution files only.

## Required WordPress checks

- [ ] Safe activation without Divi, Codex, `proc_open`, or configured paths.
- [ ] Dedicated capability and nonce enforcement.
- [ ] Consent withdrawal prevents all Codex launches.
- [ ] Diagnostics escape process output and leak no path or credential.
- [ ] Deactivation preserves content and removes schedules.
- [ ] Uninstall removes only plugin-owned settings and capabilities.
- [x] Plugin Check against the built ZIP; only forbidden `proc_open` remains at
      `class-runtime-probe.php` line 93.

## Required M2/M3 live checks

- [ ] Exact Codex version and generated schema compatibility.
- [ ] ChatGPT sign-in, refresh, disconnect, cancellation, and allowance display.
- [ ] Framing errors, malformed events, crashes, timeout, and uncertain
      completion.
- [ ] Duplicate jobs, locks, owner isolation, cancellation, and cleanup.
- [ ] One real subscription-backed text result.
- [ ] Exact licensed Divi control behavior without double-triggering hosted AI.
- [ ] Preview, baseline conflict rejection, apply, undo, save, and reload.
- [ ] Same-client image feasibility and real-file validation.
- [ ] Target cPanel process, filesystem, and cron behavior.

Mocks are useful for isolated logic only. They are not evidence for Divi,
cPanel, account, image, or WordPress.org behavior.
