# PromptBridge for Divi Project Brief

## Objective

Research, build, test, and package a WordPress.org-first plugin that can
eventually route supported Divi 5 generation actions through an independently
installed official Codex runtime using ChatGPT-managed authentication.

The first acceptable end-to-end target is one supported text field: request,
subscription-backed generation, preview, explicit apply through Divi editor
state, undo, save, reload, and proof that Divi-hosted AI was not also invoked.

## Identity

- Name: PromptBridge for Divi
- Author: MrDemonWolf
- Slug and text domain: `promptbridge-for-divi`
- Main file: `promptbridge-for-divi.php`
- PHP namespace: `MrDemonWolf\PromptBridge`
- Unique prefix: `mdw_pbd_`
- REST namespace: `mdw-promptbridge/v1`
- Original-code license: GPL-2.0-or-later
- Target: WordPress, Divi 5, PHP/browser code, and existing cPanel hosting

The name is a working identity, not trademark or WordPress.org slug clearance.
This is not an official OpenAI or Elegant Themes product.

## Required architecture

```text
Supported existing Divi control
  -> authenticated WordPress request
  -> owned PHP job and worker
  -> independently installed Codex over local stdio
  -> validated structured result
  -> preview
  -> explicit apply through Divi editor state
```

Codex must use a server-owner-configured absolute executable path, fixed
arguments, structured stdin, a dedicated home, minimum tools, and no public
app-server listener.

## Distribution boundaries

- The server owner installs and updates Codex independently.
- The plugin does not bundle, fetch, install, update, replace, repair, or delete
  Codex, SDKs, JavaScript, CSS, Composer packages, or adapters at runtime.
- WordPress.org is the intended production plugin update channel.
- GitHub builds, tests, docs, ZIPs, and release evidence; it is not a hidden
  production updater inside WordPress.
- Activation makes no telemetry, release metadata, or service request.
- External-service use needs informed opt-in and documented destinations, data,
  triggers, accounts, terms, and privacy links.

## Security and UX

- Capabilities, owner binding, per-object checks, nonces, prepared SQL,
  validation, sanitization, and late escaping are mandatory.
- Prompts, page content, images, filenames, process output, and model output are
  untrusted.
- Generated PHP or JavaScript is never executed for preview.
- One clear next action, keyboard support, visible text status, real job states,
  and advanced-detail disclosure are required.
- No activation redirect, nag, fabricated progress, or marketing dashboard.

## Divi constraints

- Do not patch Divi files, spoof subscriptions, unlock hosted AI, monkey-patch
  global requests, or copy proprietary UI.
- A label-button extension or separate dialog does not prove takeover of the
  existing Divi AI action.
- Apply through documented editor state, never DOM text or direct `post_content`
  mutation behind an open builder.
- Preserve module identity, unsaved edits, rich text, responsive values, and
  interaction state. Reject stale/conflicting results.

## Image gate

After real text authentication succeeds, test the same supported custom client
for a real image artifact. Do not substitute the paid Image API, browser
scraping, screenshots, SVG placeholders, or fabricated bytes.

## Lifecycle and cleanup

- Deactivation restores controls and preserves content.
- Uninstall removes only plugin-owned settings, jobs, metadata, schedules,
  capabilities, logs, temporary files, owner bindings, and dedicated state.
- Preserve external Codex installs and credentials, published pages, and media.
- Generated-content deletion requires separate confirmation.
- Do not claim multisite support before lifecycle and permissions tests.

## Milestones

- [x] M0: repository inspection, parallel primary-source research, architecture,
      policy questions, shared contracts, and initial threat modeling.
- [x] M1: safe plugin skeleton, diagnostics, local version status, capability,
      consent, setup UI, docs, CI/CD, and clean uninstall.
- [ ] M2: pinned protocol client, managed authentication, reliable owned jobs,
      and one live subscription-backed text result.
- [ ] M3: one verified existing-control Divi rewrite, preview/apply/undo,
      save/reload, stale protection, and early image feasibility result.
- [ ] M4: independent integrated security review, accessibility, upgrade/runtime
      drift, privacy, failures, and uninstall acceptance.
- [ ] M5: reproducible staging-alpha ZIP, Plugin Check, licensed integration
      evidence, and release handoff.

## Stop conditions

Do not implement guessed app-server fields or guessed Divi behavior. Pin an
exact release, generate its schemas, and test the licensed target first. Missing
credentials or licensed access blocks only the related live test.
