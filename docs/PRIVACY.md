# Privacy

## Current release

Activation and ordinary page loads send no telemetry, prompts, page content, or
release checks. After an administrator grants consent, an explicit **Run
diagnostics** action may launch the configured local Codex executable with the
fixed `--version` and `debug models --bundled` arguments. Output is bounded,
strictly projected, validated, and redacted where displayed. The model catalog
cache keeps only list-visible API models in the non-autoloaded
`mdw_pbd_model_catalog` option; it does not store the raw 443 KB catalog.
Refresh happens only from that explicit action, never from a background cron
launch, and a failed refresh preserves the last-good cache.

## Future service use

Only after an administrator opts in and a user requests generation may Codex
send data to OpenAI. Expected data includes the prompt, selected Divi content,
account/runtime metadata, and any explicitly attached supported input.

- Privacy: https://openai.com/policies/privacy-policy/
- Terms: https://openai.com/policies/terms-of-use/

The exact destination and fields must be revalidated against the pinned Codex
release before M2. PromptBridge must not claim that content remains local.

## Logs

Future logs must exclude credentials, cookies, bearer tokens, full prompts by
default, and unbounded process output. Owners need a clear retention and delete
control before job logging ships.
