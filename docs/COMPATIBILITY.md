# Compatibility

## Declared minimums

| Component | Declared minimum | Evidence status                                                             |
| --------- | ---------------- | --------------------------------------------------------------------------- |
| PHP       | 8.3              | Local checks target 8.3-8.5                                                 |
| WordPress | 6.7              | Syntax/lifecycle only; full WP test pending                                 |
| Divi      | 5                | Text field action implemented; live preview, Undo, Save, and reload pending |
| Codex     | Not pinned       | Experimental App Server; one LocalWP request returned 401 unauthenticated  |
| cPanel    | No blanket claim | Exact host test pending                                                     |
| Multisite | Unsupported      | Lifecycle/permission tests absent                                           |

Local macOS tool availability is not evidence for shared hosting. The plugin
activates without Divi, Codex, or `proc_open` and reports the missing boundary.

## Runtime changes

The executable version can change outside WordPress. A future protocol client
must compare each worker start against its pinned compatible version and stop on
schema drift. The current admin check never updates the executable.
