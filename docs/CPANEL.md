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
8. Sign the independently installed Codex runtime into this dedicated home.
9. Run PromptBridge diagnostics from WordPress.

Do not place credentials in chat, the repository, WordPress options, or a public
directory. Do not disable sandboxing or host protections to make diagnostics
pass.

## Generation worker

The staging alpha queues owner-bound jobs with WordPress Cron. The editor's
request returns after queueing; Codex runs in the separate cron request. A host
with `DISABLE_WP_CRON` or blocked loopback requests needs a cPanel cron that
visits `https://example.com/wp-cron.php?doing_wp_cron` on a short interval.
Replace the example domain with the site URL and follow the host's cron
instructions. Remove that cron before uninstalling the plugin.

The worker uses a database lock and a 30-second Codex App Server timeout. Check
cPanel's PHP-FPM and CLI limits separately; this does not prove the host can
finish jobs. LocalWP's test home returned `401 Missing bearer or basic
authentication`, so sign-in and one real text result remain unverified.
