# WordPress.org Policy Research

- Status: pre-submission policy baseline; no reviewer approval has been
  requested or received
- Accessed: 2026-09-21

## Confirmed facts

| Area                  | Current documented requirement                                                                                                                                                                               | PromptBridge implication                                                                                                                                                                                 |
| --------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| External service      | WordPress.org permits a plugin to act as an interface to a substantive third-party service, including a paid service, when the service is clearly documented and linked to applicable terms.                 | OpenAI/Codex use is not automatically disqualifying, but it must be disclosed accurately.                                                                                                                |
| Consent and privacy   | Plugins may not contact external servers without explicit, authorized consent. The readme must explain the service, when data is sent, and link to the service and its terms/privacy information.            | No OpenAI call, release-metadata check, or child-process-triggered external request may occur on activation or before informed opt-in.                                                                   |
| Human-readable source | Deployed code must be mostly human readable. Public, maintained access to source and build tools must be provided by including them or linking to a public development location.                             | This repository is now public, and the docs source/build material is available there. That improves source availability but is not WordPress.org approval.                                               |
| Executable code       | Guideline 8 prohibits sending executable code through third-party systems and gives remote updates/installation as prohibited examples when not part of a permitted service.                                 | Automatically receiving and executing generated PHP or JavaScript is not acceptable. Whether a user-reviewed code suggestion may be inserted but not executed by the plugin remains a reviewer question. |
| Updates               | WordPress.org distributes the stable plugin version. The review team's common-issues guidance says directory plugins must remove alternate update checkers and must not interfere with the built-in updater. | Plugin updates must come through WordPress.org. A GitHub replacement updater is out of scope.                                                                                                            |
| Included dependencies | Developers are responsible for all plugin contents, dependency licensing, third-party terms, and security. Non-service JavaScript/CSS must be local.                                                         | Ship required PHP/browser dependencies in the plugin ZIP; do not download missing libraries at runtime.                                                                                                  |
| Naming                | Slugs and names must respect trademarks and must not misleadingly imply affiliation.                                                                                                                         | “PromptBridge for Divi” is a working name, not confirmed clearance or a reserved directory slug. The readme must state that the plugin is independent of OpenAI and Elegant Themes.                      |
| Automated checks      | Plugin Check is a non-perfect compliance aid. Manual review controls the actual directory decision.                                                                                                          | A clean Plugin Check report is evidence, not approval.                                                                                                                                                   |

## Conservative project choices

- Codex is an independently installed host dependency. PromptBridge will not
  bundle, download, install, update, replace, or delete it, directly or through
  a shell script, package manager, agent tool, or hidden installer.
- Runtime diagnostics are read-only: validate the configured absolute path, run
  fixed `--version` and `debug models --bundled` arguments only after consent,
  strictly project the bounded catalog, and provide host-side update
  instructions.
- PromptBridge will use normal WordPress.org plugin updates only. It will not
  ship a GitHub updater or modify WordPress update behavior.
- Setup will require an explicit owner opt-in before any service use. Disclosure
  will name the destination, data sent, triggering action, account requirement,
  and current terms/privacy links. It will also explain that the independently
  installed Codex child process contacts OpenAI.
- Activation performs no telemetry, OpenAI request, Codex login, or remote
  version check. Model metadata refreshes only when an administrator with
  consent clicks **Run diagnostics**; no periodic or background cron check is
  enabled.
- Generated PHP, JavaScript, shell commands, or other executable code will not
  be executed, installed, or previewed through execution. Code insertion stays
  disabled until the Plugin Review Team answers the policy question. Plain
  content suggestions require preview and explicit Apply.
- The submission ZIP will contain readable source and build instructions needed
  to review bundled assets. The now-public repository and Pages site provide
  maintained source/build context, but the submission package remains subject to
  reviewer requirements.
- Plugin Check will run against the final ZIP, followed by manual security,
  privacy, source, dependency, and package review.

## Required external-service disclosure

The directory readme must disclose, in plain language:

- The plugin starts an independently installed official Codex runtime on the
  same server.
- Codex communicates with OpenAI after the connected owner explicitly requests
  sign-in or generation.
- Inputs may include the owner's prompt, selected Divi field content, nearby
  page/module context, configuration needed for the request, and supported
  source images when the user explicitly selects them.
- Outputs and protocol diagnostics return through the local Codex process.
- A ChatGPT account and eligible Codex access are required; usage and feature
  availability depend on the account/workspace.
- No API-key fallback, automatic credit purchase, or guarantee of
  unlimited/no-additional-cost use exists.
- Applicable links:
  [OpenAI Terms of Use](https://openai.com/policies/terms-of-use/) and
  [OpenAI Privacy Policy](https://openai.com/policies/privacy-policy/).

The exact disclosure must be reviewed again immediately before submission
because service behavior, terms, and endpoints can change.

## Open policy gates

- [ ] Ask the Plugin Review Team whether launching an independently installed
      Codex process over local `stdio` is eligible in principle when the plugin
      never installs or updates that process.
- [ ] Ask whether official ChatGPT-managed authentication and the resulting
      child-process network calls are adequately treated as a documented service
      integration.
- [ ] Ask whether explicit, user-reviewed AI code suggestions may be inserted as
      content without automatic execution, or whether guideline 8 requires all
      executable-code suggestions to remain disabled.
- [ ] Confirm the required reviewer setup: supported Codex version, host process
      requirements, test account/access, Divi version, and reproducible test
      steps.
- [ ] Recheck name/slug availability and trademark presentation at submission
      time.
- [x] Provide source/build materials in the ZIP or another genuinely public
      location. The repository is public and GitHub Pages is configured for the
      Actions workflow at https://mrdemonwolf.github.io/promptbridge-for-divi/;
      no approved push has been performed yet.
- [ ] Resolve the Plugin Check 2.1.0 `proc_open` finding and complete manual
      review. The finding is a genuine WordPress.org reviewer/policy blocker; it
      must not be recorded as approval.

## Current eligibility statement

**UNRESOLVED.** The architecture is designed conservatively around the published
rules, but those rules do not explicitly decide this combination of local native
process execution, managed ChatGPT authentication, AI suggestions, and Divi
editor integration. Only the Plugin Review Team can resolve directory
eligibility.

## Sources

- [WordPress.org Detailed Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
  — guidelines 2–8, source availability, services, consent, executable code,
  updates, and naming.
- [WordPress.org Common Issues](https://developer.wordpress.org/plugins/wordpress-org/common-issues/)
  — alternate update checkers, third-party service disclosures, source/build
  files, and manual-review context.
- [WordPress.org Plugin Developer FAQ](https://developer.wordpress.org/plugins/wordpress-org/plugin-developer-faq/)
  — directory process and developer guidance.
- [WordPress.org Plugin Check](https://wordpress.org/plugins/plugin-check/) —
  automated-check scope and limitations.
- [OpenAI Terms of Use](https://openai.com/policies/terms-of-use/) and
  [OpenAI Privacy Policy](https://openai.com/policies/privacy-policy/) — service
  links required in disclosure; applicable regional/business terms must be
  confirmed for the connected account.
