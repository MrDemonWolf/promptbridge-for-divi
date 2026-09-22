# Divi 5 Integration Research

- Status: documented extension points identified; existing-AI-control takeover
  not proven
- Accessed: 2026-09-21
- Licensed Divi build inspected: Divi 5.13.1 on the PromptBridge Divi Test
  staging site; browser UI placement and layout verified

## Confirmed facts

1. **Divi 5 exposes a field-label button filter.** The official tutorial
   documents `divi.module.modal.field.labelButtons` for the Visual Builder
   module settings modal. It does not cover the inspector, page settings, or
   every `FieldWrapper` consumer.

2. **The hook can add a custom control.** The callback receives `defaultButtons`
   and a read-only `context`, then returns React output. The documented example
   keeps `defaultButtons` and appends a custom button.

3. **The default row can contain Divi AI.** Divi documents the default controls
   as including reset, description, responsive, dynamic-content, and Divi AI
   controls where applicable. The context includes values such as `moduleId`,
   `moduleName`, `attrName`, `subName`, `appBreakpoint`, and `appState`.

4. **The hook does not prove takeover of the existing AI action.** The official
   page documents adding, wrapping, or rendering label-row React nodes. It does
   not document an API for identifying, replacing, suppressing, or rerouting
   only Divi's existing AI button or its click handler. Seeing Divi AI inside
   `defaultButtons` is not a supported interception contract.

5. **`editModuleAttribute` is the documented editor-state update path.** Elegant
   Themes' field-sync tutorial gets `editModuleAttribute` from
   `useDispatch('divi/edit-post')` and uses it to update a module attribute by
   module id and attribute path. The same example reads current state through
   `select('divi/edit-post').getModuleAttr(...)`.

6. **The documented sync example has limits.** Elegant Themes explicitly notes
   that its example does not support dynamic content correctly. Its
   desktop-value object shape is an example, not proof that every text,
   rich-text, responsive, hover, or sticky field shares the same shape.

7. **PromptBridge uses a dedicated Divi submenu.** The verified staging UI is
   under **Divi > PromptBridge** with a plugin-owned Divi-style header, tabs,
   field rows, toggle, and top actions. General, Diagnostics, and Advanced tabs
   work, and the desktop layout is visually verified. Divi Booster 5.8.1 was
   installed and temporarily activated only on Local for comparison; it also
   uses a separate submenu rather than injecting into private Theme Options. It
   was deactivated after comparison. No Booster code is used or distributed by
   PromptBridge.

## Project choices

- Do not patch Divi files, spoof licensing/subscription state, monkey-patch all
  network requests, mutate visible DOM text, or write directly to `post_content`
  while the builder is open.
- Use the documented label-button filter only for supported module/field
  contexts and preserve Divi's default controls.
- A PromptBridge-added button is a fallback integration and must be labeled as
  PromptBridge. It is not evidence that the requirement to take over an existing
  Divi AI action has been completed.
- Apply accepted content through the `divi/edit-post` data store and
  `editModuleAttribute`, using the actual `moduleId`, attribute path, sub-field,
  breakpoint, and state shape captured from the target field.
- Capture the selected field's baseline through the editor store before
  generation. Re-read it before Apply and reject a stale proposal if the module,
  field, state, or content changed.
- Keep generation separate from Apply. The user sees a preview and explicitly
  applies the result.
- Version-gate the integration against Divi builds that have passed live tests.
  Unknown builds leave the action unavailable with an accurate diagnostic.

## Open gates

- [x] Inspect a licensed, exact Divi 5 build and record its version/build
      identifier: Divi 5.13.1 on the PromptBridge Divi Test staging site.
- [ ] Determine whether Divi exposes a supported API for intercepting the
      existing AI control without triggering Divi-hosted AI. The label-button
      filter alone does not establish this.
- [ ] Verify whether the existing AI control renders when the site lacks a Divi
      AI subscription.
- [ ] Map supported text and rich-text field value shapes, including responsive,
      hover, sticky, dynamic-content, and nested `subName` cases.
- [ ] Verify that `editModuleAttribute` creates the expected undo/redo history
      entry for the chosen field and does not collapse unrelated unsaved edits.
- [ ] Verify Apply, Undo, Save, builder close/reopen, and front-end reload in
      the exact Divi build.
- [ ] Confirm the mapped action does not also invoke Elegant Themes' hosted AI
      service.
- [ ] Test keyboard operation, focus return, accessible naming, and status
      announcements inside the Visual Builder frame.

## Minimum live acceptance path

1. Select one supported text field in a supported core module.
2. Invoke the verified integration control.
3. Confirm only PromptBridge starts a request.
4. Generate a text proposal and preserve the original field value.
5. Edit the field while generation is pending and confirm stale Apply is
   rejected.
6. Generate again, preview, and Apply through `editModuleAttribute`.
7. Undo and redo the change.
8. Save, reload the Visual Builder, and reload the public page.
9. Confirm no Divi-hosted AI request was made.

Until every step passes against a recorded Divi build, status is **BLOCKED** for
“existing AI-control takeover” and at most **IMPLEMENTED/MOCK-TESTED** for a
separately labeled custom-control fallback.

## Sources

- [Elegant Themes: Extending Option Field Label Buttons](https://dev.elegantthemes.com/docs/tutorials/module/advanced/customize-module-settings-output/extending-option-field-label-buttons/)
  — filter scope, `defaultButtons`, context, dependencies, and custom-control
  example.
- [Elegant Themes: Syncing Fields Bidirectionally](https://dev.elegantthemes.com/docs/tutorials/module/advanced/customize-module-settings-output/syncing-fields-bidirectionally/)
  — `divi/edit-post`, `getModuleAttr`, `editModuleAttribute`, and documented
  dynamic-content limitation.
