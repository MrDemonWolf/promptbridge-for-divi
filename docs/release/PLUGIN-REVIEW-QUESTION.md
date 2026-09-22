# Plugin Review Team Question

**Status: UNSENT DRAFT — do not email or submit without Nathanial's approval.**

## Confirmed facts represented in the draft

- Codex would be installed and updated independently by the server owner or
  host.
- PromptBridge would use local process execution and structured `stdio`; it
  would not expose a public Codex listener.
- The official Codex app-server documents ChatGPT-managed browser and
  device-code authentication, but OpenAI currently labels app-server
  experimental.
- External OpenAI requests would occur only after informed opt-in and a user
  action.
- Plugin updates would use WordPress.org only.
- No Plugin Review Team decision has been received.

## Project choices represented in the draft

- No runtime downloader, installer, updater, replacement, or deletion path.
- Read-only executable/version diagnostics plus a consent-gated, bounded model
  catalog projection. It runs only from an explicit diagnostics action; there is
  no background cron launch and no raw catalog is stored.
- No API-key fallback and no automatic execution of generated PHP/JavaScript.
- Preview and explicit Apply for non-executable content suggestions.
- Clear disclosure of the external service, transmitted data, account
  requirement, and terms/privacy links.

## Questions that remain open

- Is this architecture eligible in principle for the Plugin Directory?
- Does the documented service/consent model cover a child process that makes the
  external request?
- May user-reviewed executable-code suggestions be inserted as content if the
  plugin never executes them, or must that feature remain disabled?
- What runtime, account, Divi, and hosting access must be supplied for review?

## Draft email

**To:** WordPress.org Plugin Review Team (`plugins@wordpress.org`)

**Subject:** Pre-submission question: local Codex runtime used by a Divi 5
plugin

Hello Plugin Review Team,

We are evaluating a GPL-2.0-or-later plugin, “PromptBridge for Divi,” before
submission. It would let an authorized WordPress owner request AI-assisted edits
from a Divi 5 field control, review a proposal, and explicitly apply it through
Divi's editor state.

The server owner or host would install and update the official Codex executable
independently. The plugin would never bundle, download, install, update,
replace, or delete Codex. It would only validate a configured absolute path,
perform read-only version and list-visible model diagnostics, and launch the
local executable with fixed arguments. Communication would use structured
messages over local `stdio`, not a public listener.

After informed opt-in, Codex would use its documented ChatGPT-managed sign-in.
Codex—not the plugin—would persist and refresh the account credentials. A
generation request could send the owner's prompt and selected Divi field/page
context to OpenAI. The readme and settings flow would identify the service,
trigger, data sent, account requirement, and current terms/privacy links.
Activation would make no external request. Plugin updates would come only from
WordPress.org.

Generated content would be previewed and require explicit Apply. The plugin
would not automatically run generated PHP, JavaScript, shell commands, or other
executable code. We can keep all code-insertion suggestions disabled if
guideline 8 makes even reviewed insertion ineligible.

Before we build further, could you clarify:

1. Is launching an independently installed local Codex process for this
   documented service integration eligible in principle?
2. Are the read-only executable/version checks and ChatGPT-managed
   authentication acceptable with the disclosure and opt-in described above?
3. May the plugin offer user-reviewed code suggestions for insertion when it
   never executes them, or should all executable-code suggestions remain
   disabled?
4. What setup should we provide for review: a supported Codex version, test
   account/access, Divi 5 build, host requirements, and reproducible steps?

We understand that Plugin Check and a pre-submission answer are not approval,
and that the submitted ZIP remains subject to full manual review.

Thank you,

Nathanial Henniges

MrDemonWolf, Inc.

## Official references

- [WordPress.org Detailed Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
- [WordPress.org Common Issues](https://developer.wordpress.org/plugins/wordpress-org/common-issues/)
- [OpenAI Codex App Server](https://developers.openai.com/codex/app-server/)
- [Elegant Themes Divi 5 label-button extension](https://dev.elegantthemes.com/docs/tutorials/module/advanced/customize-module-settings-output/extending-option-field-label-buttons/)
