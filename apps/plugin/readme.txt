=== PromptBridge for Divi ===
Contributors: mrdemonwolf
Tags: divi, codex, ai, diagnostics
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.3
Stable tag: 0.1.0-alpha.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Server diagnostics for a proposed Divi 5 bridge to an independently installed Codex runtime.

== Description ==

PromptBridge for Divi is an independent staging alpha. The current package
activates safely, grants a dedicated administrator capability, shows local
compatibility diagnostics, validates server-owned runtime paths, and requires
explicit service consent.

The admin screen follows Dark Mode for WP Dashboard's live palette without
adding a separate theme script or dependency.

This release does not authenticate with Codex, generate content, alter Divi
controls, or install/update Codex. Those milestones remain gated on exact
Codex and Divi versions, live tests, and host support.

Divi is a registered trademark of Elegant Themes, Inc. PromptBridge for Divi
is not affiliated with or endorsed by Elegant Themes or OpenAI.

= External service disclosure =

WordPress update checks may contact the public GitHub Releases API to retrieve
the latest version number and download URL. GitHub receives the server IP
address and normal HTTP request metadata.

GitHub privacy policy: https://docs.github.com/en/site-policy/privacy-policies/github-general-privacy-statement

GitHub terms: https://docs.github.com/en/site-policy/github-terms/github-terms-of-service

Future generation requests would use an independently installed official Codex
runtime. When an administrator has opted in and explicitly requests a
generation, that runtime may send the prompt, selected Divi content, account
data, and runtime metadata to OpenAI. Plugin activation does not contact OpenAI.

Service: OpenAI
Privacy policy: https://openai.com/policies/privacy-policy/
Terms: https://openai.com/policies/terms-of-use/

== Installation ==

1. Upload the `promptbridge-for-divi` directory to `/wp-content/plugins/`.
2. Activate PromptBridge for Divi in WordPress.
3. Ask the server owner to install and update Codex independently.
4. Define `MDW_PBD_CODEX_PATH` and `MDW_PBD_CODEX_HOME` in `wp-config.php`.
5. Open Settings > PromptBridge, review the disclosure, choose a supported
   model, and run diagnostics.

Run diagnostics is an explicit, consent-gated action. It refreshes the model
list by executing the fixed `codex debug models --bundled` command; it does not
start a background cron job. The response is capped at 2 MB, strictly
projected, and validated before only list-visible API models are stored in the
non-autoloaded `mdw_pbd_model_catalog` option. The raw 443 KB catalog is never
stored, and a failed refresh preserves the last-good cache.

The dedicated Codex home must exist outside the public WordPress tree, contain
an empty `.promptbridge-for-divi` marker, use directory mode `0700`, and be
writable by the PHP worker. Never reuse a developer workstation's Codex home.

== Frequently Asked Questions ==

= Does this plugin install Codex? =

No. It never downloads, installs, updates, replaces, or removes Codex.

= Does this release generate text or images? =

No. Text and image generation remain unproven in this integration.

= Can I choose a model? =

Yes. The staging Codex 0.155.1 result exposes five list-visible models:
Astra, Sol, Terra, Luna, and 5.5. The default and fallback is Luna. The
settings page stores the selected preference for future requests; generation is
not enabled in this release.

= Does diagnostics refresh models in the background? =

No. The cache refreshes only when an administrator with consent clicks **Run
diagnostics**. There is no background cron launch.

= How are plugin updates delivered? =

Tagged GitHub Releases are exposed through WordPress's native update system.
Automatic installation remains under the site owner's WordPress settings.

== Changelog ==

= 0.1.0-alpha.1 =

* Add safe activation, owner capability, local diagnostics, consent, and clean uninstall.
* Add bounded shell-free Codex version probing behind explicit administrator action.
* Add consent-gated, bounded model-catalog discovery with last-good cache preservation.
* Add native Dark Mode for WP Dashboard palette support.
* Add cached GitHub Release discovery through WordPress's native Update URI hook.
