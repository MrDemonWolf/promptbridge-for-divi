# PromptBridge for Divi Agent Guide

## Scope

PromptBridge for Divi is an independent WordPress plugin for Divi 5. Keep the
development agents, the installed Codex runtime, and the WordPress plugin as
three separate systems.

## Repository layout

- `apps/plugin` contains the distributable WordPress plugin.
- `apps/docs` contains the static-export Fumadocs site.
- `docs` contains engineering, policy, security, and release evidence.
- `.github` contains GitHub-owned validation and deployment automation.

Do not add a `packages` workspace until two real consumers need shared code.

## Non-negotiable boundaries

- Never download, install, update, replace, or delete Codex from the plugin.
- Never add a GitHub replacement updater to the WordPress plugin.
- Never accept an executable path, shell fragment, or process argument from a
  normal browser request.
- Never expose Codex app-server on a public TCP or WebSocket listener.
- Never reuse developer-machine Codex credentials or configuration.
- Never execute generated PHP or JavaScript.
- Never patch Divi theme files, spoof licensing, or claim interception of an
  existing Divi AI action without a source-backed, live acceptance test.
- Never claim WordPress.org approval, cPanel compatibility, image output, or
  production app-server stability without direct evidence.

## Implementation rules

- PHP namespace: `MrDemonWolf\PromptBridge`.
- Global prefix: `mdw_pbd_` / `MDW_PBD_`.
- Text domain and slug: `promptbridge-for-divi`.
- REST namespace, when justified: `mdw-promptbridge/v1`.
- Use custom capabilities plus per-object ownership checks for every mutation.
- Treat prompts, content, filenames, process output, and model output as
  untrusted.
- Escape late, sanitize input, prepare SQL, bind jobs to owners, and use nonces
  only as CSRF protection, never as authorization.
- Keep browser assets local. Activation must make no external request.
- Preserve published pages and media during deactivation and uninstall.

## Required checks

Run before packaging:

```bash
bun install --frozen-lockfile
bun run check
cd apps/plugin && composer check
apps/plugin/bin/build-zip.sh
```

Record skipped live tests and why. Mocks do not prove Divi, Codex account,
cPanel, image, or WordPress.org behavior.

## Documentation honesty

Keep implemented behavior, proposed design, hypotheses, and external policy
questions visibly separate. Update the public plugin documentation and changelog
when behavior changes; do not commit agent notes, scan dumps, or progress logs.
