# Privacy

## Current release

Activation and ordinary page loads send no telemetry, prompts, or page content.
Standard WordPress update checks may request public release metadata from the
GitHub API. GitHub receives the server IP address and normal HTTP request
metadata. PromptBridge caches only the release version, page URL, and package
URL for 12 hours.

- GitHub privacy:
  https://docs.github.com/en/site-policy/privacy-policies/github-general-privacy-statement
- GitHub terms:
  https://docs.github.com/en/site-policy/github-terms/github-terms-of-service

After an administrator grants consent, an explicit **Run diagnostics** action
may launch the configured local Codex executable with the fixed `--version` and
`debug models --bundled` arguments. Output is bounded, strictly projected,
validated, and redacted where displayed. The model catalog cache keeps only
list-visible API models in the non-autoloaded `mdw_pbd_model_catalog` option; it
does not store the raw 443 KB catalog. Refresh happens only from that explicit
action, never from a background cron launch, and a failed refresh preserves the
last-good cache.

## Requested text generation

After an administrator opts in and an authorized editor requests generation,
PromptBridge sends the instruction and selected Divi Text field content to
OpenAI through the server owner's signed-in Codex runtime. The private job
stores the prompt and selected content only while queued or running, then
removes them. A sanitized preview result stays in the private job for up to one
day. Plugin activation and ordinary page loads do not contact OpenAI.

- Privacy: https://openai.com/policies/privacy-policy/
- Terms: https://openai.com/policies/terms-of-use/

The current App Server protocol is experimental and not pinned by this staging
alpha. PromptBridge does not claim that content remains local or that all host
setups can use this integration.

## Logs

Process errors shown to the editor are generic and omit stderr. Private job
records are scheduled for removal after one day when WordPress Cron runs, and
are removed on plugin uninstall.

## Policy acceptance

Before settings and generation are available, a site administrator accepts the
current Terms of Use and acknowledges the current Privacy Policy. The plugin
stores both policy version identifiers, the acceptance timestamp, and that
administrator's WordPress user ID in the `mdw_pbd_settings` option. This record
is separate from the optional OpenAI service-access choice. Updating either
policy version requires fresh acceptance; the service-access choice is reset off
at that point.
