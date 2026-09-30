# cPanel Setup

## Server-owner steps

1. Confirm PHP 8.3 or newer for both WordPress and the intended worker.
2. Confirm `proc_open` is available and the executable is permitted by
   `open_basedir` and host policy.
3. Install official Codex outside the WordPress plugin directory.
4. Create a dedicated private Codex home outside the public web root.
5. Add an empty `.promptbridge-for-divi` marker and set the directory mode so
   other OS users have no access.
6. Grant the PHP worker only the filesystem access required for that home.
7. Define `MDW_PBD_CODEX_PATH` and `MDW_PBD_CODEX_HOME` in `wp-config.php`.
8. Run PromptBridge diagnostics from WordPress.

Do not place credentials in chat, the repository, WordPress options, or a public
directory. Do not disable sandboxing or host protections to make diagnostics
pass.

## Cron gate

No worker cron is registered in M1. Before M2, choose one owner-controlled
worker model, prove locking and timeout behavior, document its cPanel setup, and
document manual cron removal. A web request must not hold a long-lived Codex
process open.
