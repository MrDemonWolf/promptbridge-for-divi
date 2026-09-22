# Deactivation and Uninstall

## Deactivation

- Clears the reserved PromptBridge worker schedule.
- Leaves settings available for reactivation.
- Preserves WordPress pages, posts, media, and external Codex state.

## Uninstall

- Deletes `mdw_pbd_settings` at site and network-option scope.
- Clears the reserved worker schedule.
- Removes `mdw_pbd_manage` from every role.
- Does not remove Codex, shared credentials, published content, or media.

M1 owns no database table, job row, log, temporary artifact, or authentication
state. Each later resource must add an explicit ownership marker and bounded
cleanup path before release. Never recursively delete a configured directory.
