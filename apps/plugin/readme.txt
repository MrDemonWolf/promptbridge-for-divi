=== PromptBridge for Divi ===
Contributors: mrdemonwolf
Tags: divi, codex, ai, diagnostics
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.3
Stable tag: 0.1.0-alpha.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Divi 5 Text field generation through an independently installed Codex runtime.

== Description ==

PromptBridge for Divi is an independent staging alpha. It adds a Generate
button to the Divi 5 Text module Body field. Requests run through an
owner-bound WordPress job, then show a separate preview with explicit Apply.
The package also validates server-owned runtime paths and requires explicit
administrator service consent.

A site administrator must accept the current PromptBridge Terms of Use and
acknowledge its Privacy Policy before configuring the plugin. That acceptance
is separate from the optional OpenAI service-access setting.

The admin screen follows Dark Mode for WP Dashboard's live palette without
adding a separate theme script or dependency.

PromptBridge does not manage Codex sign-in, install/update Codex, or generate
images. The App Server protocol is experimental. This staging alpha needs live
account, Divi Undo/Save, and target-host testing; it is not a production
compatibility claim.

Divi is a registered trademark of Elegant Themes, Inc. PromptBridge for Divi
is not affiliated with or endorsed by Elegant Themes or OpenAI.

= External service disclosure =

WordPress update checks may contact the public GitHub Releases API to retrieve
the latest version number and download URL. GitHub receives the server IP
address and normal HTTP request metadata.

GitHub privacy policy: https://docs.github.com/en/site-policy/privacy-policies/github-general-privacy-statement

GitHub terms: https://docs.github.com/en/site-policy/github-terms/github-terms-of-service

When an administrator has opted in and an editor requests generation,
PromptBridge sends that instruction and the selected Text field content to
OpenAI through the separately installed Codex runtime and its signed-in
account. Plugin activation does not contact OpenAI.

Service: OpenAI
Privacy policy: https://openai.com/policies/privacy-policy/
Terms: https://openai.com/policies/terms-of-use/

PromptBridge Terms of Use: https://promptbridge.mrdemonwolf.dev/docs/terms
PromptBridge Privacy Policy: https://promptbridge.mrdemonwolf.dev/docs/privacy

== Installation ==

1. Upload the `promptbridge-for-divi` directory to `/wp-content/plugins/`.
2. Activate PromptBridge for Divi in WordPress.
3. Ask the server owner to install and update Codex independently.
4. Define `MDW_PBD_CODEX_PATH` and `MDW_PBD_CODEX_HOME` in `wp-config.php`.
5. Sign Codex into that dedicated home. Never copy a developer workstation's
   Codex credentials.
6. Open Divi > PromptBridge, accept the current Terms of Use and acknowledge
   the Privacy Policy, then review the disclosure and configure the plugin.
7. Separately enable Codex service access only if you want to request generation
   or run consent-gated model diagnostics.

Run diagnostics is an explicit, consent-gated action. It refreshes the model
list by executing the fixed `codex debug models --bundled` command; it does not
start a background cron job. The response is capped at 2 MB, strictly
projected, and validated before only list-visible API models are stored in the
non-autoloaded `mdw_pbd_model_catalog` option. The raw 443 KB catalog is never
stored, and a failed refresh preserves the last-good cache.

The dedicated Codex home must exist outside the public WordPress tree, contain
an empty `.promptbridge-for-divi` marker, use directory mode `0700`, and be
writable by the PHP worker. Never reuse a developer workstation's Codex home.
Generation jobs rely on WordPress Cron by default. For cPanel, configure a
system cron to run `wp-cron.php` and verify the job worker can finish; exact
cPanel compatibility is unproven.

== Frequently Asked Questions ==

= Does this plugin install Codex? =

No. It never downloads, installs, updates, replaces, or removes Codex.

= Does this release generate text or images? =

It can request text for a Divi Text module on a staging setup. The result stays
in a preview until you apply it. A live subscription-backed result and cPanel
compatibility have not been proven. Image generation is not implemented.

= Can I choose a model? =

Yes. The settings page stores the selected model. The current staging catalog
has shown Astra, Sol, Terra, Luna, and 5.5; actual model availability depends on
the signed-in Codex account.

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

= 0.1.0-alpha.2 =

* Add owner-bound asynchronous text-generation jobs and a private Codex App Server client.
* Add a Divi 5 Text module Body action with separate preview and guarded Apply.
* Require editor capability, page edit access, nonce, administrator consent, and baseline matching.
* Keep cPanel, account authentication, and Divi Undo/Save compatibility unproven.

= 0.1.0-alpha.3 =

* Add public Terms of Use and Privacy Policy pages.
* Require administrators to accept the current terms and acknowledge the current privacy notice before plugin settings or generation are available.
* Keep policy acceptance separate from the optional OpenAI service opt-in.
