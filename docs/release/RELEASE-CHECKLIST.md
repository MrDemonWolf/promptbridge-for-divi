# Release Checklist

## Code and evidence

- [x] Version matches main plugin header and `readme.txt` stable tag.
- [x] `composer validate`, audit, standards, analysis, and tests pass.
- [x] Docs type-check, format check, and static build pass.
- [x] ZIP builds twice with the same checksum from the same tree:
      `85372f74ebcb619564cc26b7ee066c02bb8b57ca73b215c1e819fe9a6d258e47`.
- [ ] ZIP contains no tests, dev dependencies, secrets, logs, or build cache.
- [x] Dependency licenses and readable source/build instructions are current.
- [x] Threat model and policy matrix match implemented behavior.

## WordPress staging

- [x] Activation, deactivation, reactivation, and uninstall cleanup pass on the
      PromptBridge Divi Test staging site.
- [x] Missing Divi produces diagnostics, not fatal errors.
- [ ] No unsolicited external request on activation or ordinary admin load.
- [x] Capability, nonce, escaping, consent, and cleanup checks pass in the
      available staging evidence.
- [ ] Plugin Check runs against the built ZIP with no unresolved findings;
      Plugin Check 2.1.0 was run and its only remaining finding is forbidden
      `proc_open` at `class-runtime-probe.php` line 93, a genuine WordPress.org
      reviewer/policy blocker.

The staging model-catalog check passed with consent and an explicit **Run
diagnostics** action: fixed `codex debug models --bundled`, 2 MB cap, strict
projection validation, list-visible models only, last-good preservation, and no
background cron launch. The stored `mdw_pbd_model_catalog` option is
non-autoloaded and 288 bytes; the raw 443 KB catalog is not stored.

## External gates

- [ ] WordPress.org slug and trademark position confirmed.
- [ ] Plugin Review Team response recorded for local Codex execution.
- [ ] Exact Codex protocol release/schema and Divi integration path pinned and
      live-tested. The staging probe passed with Codex CLI 0.155.1 and Divi
      5.13.1, but authentication, generation, and control mutation remain
      blocked.
- [ ] Target cPanel host verified.
- [ ] GitHub `release` environment protections reviewed.
- [ ] WordPress.org SVN secrets exist only when deployment is authorized.

GitHub Pages is configured with `build_type=workflow` at
https://mrdemonwolf.github.io/promptbridge-for-divi/, but the site awaits an
approved push. No release or production deployment has occurred.

Do not publish a GitHub Release, submit to WordPress.org, email reviewers, or
deploy to production without explicit approval.
