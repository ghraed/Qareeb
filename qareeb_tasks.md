# Qareeb — Sequential Task Execution

## Purpose and source
Implement or verify the 25 tasks below, one at a time, in the Qareeb repository.
Source: supplied “qareeb done features.md” inventory. It describes code inspection, not runtime verification; inspect the current repository before trusting it.
Expected stack: Laravel 12, PHP 8.2+, Blade, Tailwind CSS 4, Alpine.js, Vite, session authentication.
“Client/customer” means **Seeker** in the UI. Reuse the existing customer role internally when appropriate.
Qareeb provides provider discovery and direct contact. There are no on-site bookings or payments.

## Mandatory execution rules

### Work and verification
- Read applicable repository instructions and inspect Git status before editing.
- Preserve unrelated user changes. Record pre-existing changed/staged files before starting.
- Work on only one task at a time, including its tests and documentation. Keep original Q01–Q25 IDs even when dependency order changes.
- Inspect implemented features; repair or complete them rather than rebuilding them.
- Enforce server-side validation, role authorization, ownership, and private-data access.
- Use existing UI conventions. Make routine technical choices autonomously and document them.
- Explicit assumptions below are proposed product defaults. If repository decisions contradict them, record the conflict and block the affected work; do not invent consequential requirements.
- Run relevant existing tests/checks and focused tests for new behavior or meaningful regressions. Fix failures introduced by the task.
- Existing functionality is not proof of completion. Record commands, outcomes, and any necessary manual verification.
- Never mark a task Done when acceptance criteria, required checks, or required manual verification remain outstanding.
- Continue immediately to the next eligible task. Do not ask “shall I continue?”
- A blocked task must not prevent independent tasks progressing. Do not proceed with dependent tasks while their prerequisites are incomplete.
- Do not deploy, push, merge, reset user changes, or modify production data.

### Commit only correctly completed tasks
- The user authorizes a local Git commit after **each correctly completed task**, including verification-only tasks.
- Commit only after acceptance criteria are satisfied and relevant checks pass. A task with unresolved errors or required verification is not eligible.
- Before staging, inspect the diff for unintended changes, secrets, generated noise, and unrelated work.
- Stage explicit task-related paths/hunks only. Never use blanket staging such as git add . or git add -A.
- Preserve all pre-existing staged content: construct the task commit with an isolated Git index or another safe method. If isolation cannot be guaranteed, record a commit blocker rather than including someone else's work.
- Include the task's code, relevant tests/docs, and its completed entry in qareeb_tasks.md. A verification-only task may commit its evidence entry without artificial code changes.
- Commit message: “Qareeb Qxx: <concrete completed change>”. Use one task per commit.
- After commit, inspect its contents and record its hash in the completion log before starting the next task. To avoid self-referencing hashes, the hash log entry may be included in the next successful task commit; the final hash entry may remain as an explicitly reported documentation change.
- Do not create preparatory, partial, failing, empty, or unrelated commits. Do not amend/rewrite existing commits.
- A failed commit means Verified / Commit Blocked, not Done. Record the error and continue only if unfinished changes can be isolated safely.
- Do not carry failed task changes into another task's commit. Preserve and isolate them safely, or pause overlapping work.

### Conditional error/unfinished report
Use the exact filename **qareeb_not_finished_taks.md** (intentional spelling from the user).
- Do NOT create an empty report at startup or on an entirely clean successful run.
- Create it only when a task becomes blocked/unfinished, any execution error occurs, or the run is ending with tasks incomplete. Routine tasks still queued during normal execution are not “unfinished” for this purpose.
- An execution error includes a failed test/build/migration/commit or tool/environment failure. Record genuine errors even when later fixed; mark those entries Resolved with the fix and successful recheck.
- At interruption, budget limit, or early exit, create/update the report with every incomplete task and the exact next action.
- Include task ID/title, status, failed command or action, concise error, reproduction/evidence, attempted fixes, remaining work, dependencies, safe state of uncommitted changes, and next action.
- Do not expose secrets or sensitive data in command output copied into this report.
- Do not invent errors. If no error occurred and all tasks are done, the report must not be created.
- Preserve an existing report and its history when resuming; update resolved entries rather than deleting evidence.
- Keep unfinished changes/report out of successful task commits. A resolved-error entry may be included with the task that was subsequently completed correctly.

### Progress and external tracking
- Maintain the tracker and completion log in this file. No separate progress file is required.
- Statuses: Pending, In Progress, Verified / Commit Blocked, Blocked, Needs Verification, Done.
- Record task outcomes, checks, decisions, affected files, commit hashes, and next action.
- If exact corresponding ClickUp task IDs are available and access is authorized, update their status and add evidence. Do not guess matching tasks or claim an update that did not occur.
- If ClickUp is unavailable, continue local work and provide update text.
- Persist progress before interruption/context handoff. Resume by reading this file, the conditional report if present, and actual Git state.
- Finish with completed task IDs/commits, validation results, incomplete work/errors, and remaining uncommitted paths.

## Initial execution order
Use this dependency-aware order unless repository inspection justifies a recorded adjustment:
Q22 → Q01 → Q02 → Q10 → Q21 → Q18 → Q19 → Q20 → Q12 → Q14 → Q16 → Q11 → Q15 → Q13 → Q24 → Q17 → Q03 → Q05 → Q04 → Q07 → Q08 → Q09 → Q06 → Q25 → Q23.
Q23 may proceed earlier if approved Terms content is available. Complete Terms acceptance before public launch.
Dependencies: seeker tasks require Q03; Q04 requires Q05; Q08 builds on Q07; Q06 integrates the seeker features; Q17 follows Q15/Q24. Q22 affects all authentication and completion rules.

## Task tracker
| ID | Task | Initial state | Execution status |
| --- | --- | --- | --- |
| Q01 | Admin login | Implemented; verify | Done |
| Q02 | Accept/reject providers | Implemented; verify | Done |
| Q03 | Seeker registration/login | New | Done |
| Q04 | Seeker completion percentage | New | Done |
| Q05 | Seeker profile editing | New | Done |
| Q06 | Seeker dashboard | New | Done |
| Q07 | Category search/provider directory | New | Done |
| Q08 | Location search | New | Done |
| Q09 | Complaint survey | New | Done |
| Q10 | Provider login | Implemented; verify | Done |
| Q11 | Provider completion percentage | Implemented; adjust/verify | Done |
| Q12 | Provider profile editing | Implemented; verify | Done |
| Q13 | Provider dashboard | Implemented; fix/verify | Done |
| Q14 | Provider location | Implemented; verify | Done |
| Q15 | Provider availability | Implemented; verify | Done |
| Q16 | Provider pictures | Implemented; verify | Done |
| Q17 | #Hiring banner | Partial; refine | Done |
| Q18 | Add Photography | Change | Done |
| Q19 | Remove three categories | Change | Done |
| Q20 | Moving → Transportation | Change | Done |
| Q21 | Provider preregistration | Existing flow; clarify/verify | Done |
| Q22 | Optional email | Change | Done |
| Q23 | Terms and Conditions | New/change | Pending |
| Q24 | No bookings on site | Scope/UI change | Done |
| Q25 | Image privacy reporting | New | Done |

## Task specifications

### Q01 — Admin login
Inspect the separate admin login and shared business login. Verify administrators reach their dashboard, invalid credentials fail safely, and non-admins cannot access admin routes.
Check session regeneration, logout, remember-me, and role redirects. Preserve provider/seeker login.
**Acceptance:** admin login/logout works; seekers/providers cannot access admin pages through either login or direct requests.

### Q02 — Accept/reject providers
Reuse pending, active, rejected, and suspended states. Verify admins approve pending providers and reject with a required reason; store responsible admin and timestamp.
Pending/rejected providers cannot access protected tools or public discovery. Preserve suspension/reactivation. Approval/reactivation starts availability as unavailable.
Align public profile and review access with active-provider visibility.
**Acceptance:** decisions persist; status/reasons display correctly; direct requests cannot bypass restrictions.

### Q03 — Seeker registration/login
Implement seeker registration, login, logout, protected routes, and role redirects using existing session authentication/customer role.
Keep provider registration separate. Do not create business profiles for seekers. Reuse Q22 identity rules.
**Acceptance:** seekers register and log in/out; reach their dashboard; cannot access provider/admin tools. A minimal working dashboard route may precede Q06.

### Q04 — Seeker completion percentage
Proposed default: completion uses name, phone, district, and city; email is excluded. Confirm no conflicting repository decision.
Centralize rules, calculate from persisted valid data, and show percentage/missing fields. Do not store a stale second percentage.
**Acceptance:** profile edits update completion accurately; all defined fields reach 100%; email is unnecessary.

### Q05 — Seeker profile editing
Provide a protected editor for name, phone, district, and city. Reuse Lebanese location definitions and validate district/city relationships.
Handle optional email according to Q22 identity rules; protect identity changes appropriately. Retain submitted values on errors; show save feedback.
Keep seeker contact/location private and enforce ownership.
**Acceptance:** valid changes persist; invalid combinations fail; other accounts cannot view/edit private data.

### Q06 — Seeker dashboard
Show real name, completion/missing fields, and working links to profile editing, discovery, and complaint submission.
Reuse responsive Qareeb components. Do not fabricate activity or add booking/payment metrics.
**Acceptance:** seeker-only access; accurate data; working links and mobile layout, including incomplete profiles.

### Q07 — Category search/provider directory
Implement a publicly readable paginated directory linked from the seeker dashboard.
Return active service providers with business profiles only. Match categories exactly according to their actual storage.
Provide category filter, query persistence, clear controls, empty states, and cards with existing identity/location/rating/availability and public-profile links.
Allow category/location filters to compose.
**Acceptance:** correct matches; no restricted providers; pagination preserves filters.

### Q08 — Location search
Add district/city filters to Q07 using provider service coverage and existing location definitions.
Validate supported values and dependent combinations. Combine with category filtering; preserve filters through pagination and reset.
No GPS, maps, geocoding, or distance search.
**Acceptance:** correct active-provider coverage matches; combined filters work; invalid inputs/empty results are clear.

### Q09 — Complaint survey
Proposed default: a private structured seeker complaint form, separate from public reviews.
Include type, description, optional provider reference; validate references, rate-limit, and protect duplicate submissions.
Admins review privately, use Open/In Review/Resolved, and add internal notes. Seekers see only their own submissions/status, never internal notes.
**Acceptance:** submission/status works; ownership/admin permissions hold; complaint content is never public.

### Q10 — Provider login
Verify active-provider dashboard access and pending/rejected/suspended account-status handling.
Check bad credentials, remember-me, logout, redirects, and direct-route protections. Preserve admin/seeker authentication.
**Acceptance:** correct behavior for every provider state; no access bypass.

### Q11 — Provider completion percentage
Inspect existing ten-field calculation; centralize rules and use valid persisted data.
Remove email from required completion fields and adjust the denominator consistently. Show the same percentage/missing guidance in dashboard/editor.
**Acceptance:** adding/removing fields changes completion correctly; email-less providers reach 100%; views agree.

### Q12 — Provider profile editing
Verify business name, phone, description, categories, address/postal code, districts/cities, images, previews, and feedback.
Preserve multiple categories/coverage areas and dependent location validation. Apply Q22 optional-email rules.
Only owning authorized active providers may edit.
**Acceptance:** valid edits persist/publicly render; invalid values retain form input; cross-account changes fail.

### Q13 — Provider dashboard
Verify identity, business info, completion, profile links, and availability.
Known inventory issue: dashboard uses is_available while current model uses is_available_for_hire. Inspect/fix consistently across views.
**Acceptance:** persisted data is accurate; dashboard, editor, and public profile agree.

### Q14 — Provider location
Verify address/postal code and multiple districts/cities using existing Lebanese definitions.
Check select-all and district removal, server validation, public display, and directory coverage matching.
**Acceptance:** valid coverage persists/searches correctly; invalid or orphan city selections cannot persist.

### Q15 — Provider availability
Use is_available_for_hire consistently in dashboard/toggle/public profile/directory.
Only owning active providers can change it. Handle save errors; approval/reactivation starts unavailable.
Availability means accepting enquiries, not a booking.
**Acceptance:** changes persist and every view shows the same state.

### Q16 — Provider pictures
Verify profile/brand images and six-image gallery. Confirm actual end-to-end support before advertising formats.
Check uploads/previews/replacement/deletion, size/type validation, ownership, gallery capacity including concurrent requests, and safe file cleanup.
Failed saves must preserve previous valid images.
**Acceptance:** supported uploads work; bad files/unauthorized changes fail; no more than six gallery images; cleanup is safe.

### Q17 — #Hiring banner
Refine availability indicator using existing components and is_available_for_hire.
Show clear accessible #Hiring banner/badge on public profile/directory when available, with copy meaning available for work.
Unavailable providers show no #Hiring banner. Use working contact actions; no employment-vacancy claim or booking.
**Acceptance:** responsive banner matches persisted status; contact actions work.

### Q18 — Add Photography
Add Photography consistently to category definitions, validation, editor, preview, public display, and directory.
Do not create conflicting category lists or an unrelated admin system.
**Acceptance:** providers save Photography and seekers filter it.

### Q19 — Remove Home Maintenance, Computer Repair, Mobile Repair
Inspect stored profiles; remove these exact categories from selectable options, validation, and filters.
Use a safe idempotent migration/data update to remove exact stored labels while preserving all other selections. Never silently map to unrelated categories.
Document affected counts and flag profiles left category-less for provider correction. Author/test updates locally; do not run against production.
**Acceptance:** removed labels cannot be saved/filtered; unrelated selections survive; affected profiles render safely.

### Q20 — Moving → Transportation
Rename consistently in definitions, validation, editor, previews, public display, filters.
Safely/idempotently migrate stored values; deduplicate profiles containing both. Map old Moving filter links gracefully.
**Acceptance:** existing assignments survive under Transportation; active UI no longer shows Moving.

### Q21 — Provider preregistration
Proposed interpretation: early provider account registration pending admin approval; full profile editing after approval. Existing inventory indicates this already exists.
Verify registration, automatic linked profile, restrictions, and status pages; improve copy explaining approval and next steps.
Do not create a duplicate waiting list or grant pending users additional access.
**Acceptance:** pending account/profile is created; next step is clear; no public/protected access before approval; approved users complete profiles.

### Q22 — Optional email
Proposed identity default: username/password for email-less accounts; preserve legacy email/password login.
Inspect DB constraints/auth/validation/completion/contact rendering. Add unique usernames; require one when email absent; allow username or email login with unambiguous matching.
Use consistent normalization and uniqueness rules; prevent cross-namespace username/email collisions. Do not create fake emails or unnecessarily force existing users to choose usernames.
Allow null email; supplied emails unique. Protect identity edits. Handle missing email in all views/links; exclude it from completion.
No paid SMS integration; do not imply password recovery exists when it does not.
**Acceptance:** email-less seekers/providers register/log in; legacy login works; duplicate/ambiguous identities fail; missing email never breaks UI.

### Q23 — Terms and Conditions
Use approved repository Terms if present. Otherwise provide a clearly identified draft for owner/legal review and mark the content approval requirement outstanding; do not call an unapproved draft final.
Add a readable Terms page linked from registration/footer, unchecked server-validated acceptance for new seeker/provider registrations, and acceptance timestamp/version.
Do not fabricate existing users’ historical consent. Accurately describe discovery/contact, approval, no bookings/payments.
**Acceptance:** registrations require acceptance; version/time persist; page works; required Terms content review is complete. If content approval remains, report Needs Verification and do not create a “completed task” commit.

### Q24 — No bookings on site
Audit active UI/navigation/buttons/modals/copy. Remove Hire Now coming-soon placeholder; replace with existing working contacts where available.
Preserve availability/#Hiring. Handle missing contact gracefully. Preserve unrelated behavior/data.
**Acceptance:** no active on-site booking promise/placeholder; discovery and direct contacts work.

### Q25 — Image privacy reporting
Add private report actions for public provider profile/brand/gallery images.
Proposed default: visitors can report reason/description with optional contact email.
Resolve image/provider server-side, never trust arbitrary file paths/URLs. Retain stable reference/metadata if the original image changes; avoid unnecessary duplicate image storage.
Rate-limit/prevent duplicate submissions. Admin queue: Open/In Review/Resolved; authorized image removal with recorded reason. Never auto-delete solely due to a report.
**Acceptance:** all image types can be reported; admins review/act; private reports stay private; unrelated files/images cannot be removed.

## Completion log
For each task append:
- Task ID/title and status
- Implementation or verification outcome; files changed
- Checks: commands, results, relevant manual verification
- Decisions/assumptions and blockers
- Commit hash (after successful commit)
- Next eligible task

## Resume checkpoint
- Current task: Q23
- Last completed task/commit: Q25 (hash below after commit)
- Next action: Inspect and execute Q23 following task specification; preserve user edits and use isolated commit index.


## Initial repository evidence
- Working tracker copied from `/home/raed/Downloads/qareeb_tasks.md`; source retained unchanged outside writable workspace.
- No applicable AGENTS.md found in repository or ancestor directories.
- Pre-existing unstaged user changes: `app/Http/Controllers/BusinessAuthController.php`, `tests/Feature/BusinessAuthTest.php`. No staged files. Patches preserved in `/tmp/qareeb-user-{controller,tests}.patch`.
- All database verification uses `DB_CONNECTION=sqlite DB_DATABASE=:memory:`; no application database queried or changed.
- No exact ClickUp IDs supplied; local tracking only.

### Q22 — Optional email — Done
- Added nullable unique usernames, nullable emails, normalized identity validation/authentication, namespace separation (usernames cannot contain @), and legacy case-insensitive email login. Existing accounts need no username. Identity edits stay unavailable/read-only for providers. Shared rules verified with email-less customer accounts; seeker registration UI is delivered under Q03 as specified.
- Updated provider/admin authentication forms, optional contact rendering, missing-email private views, and nine-field completion excluding email. No fake emails or recovery promises.
- Files: AccountIdentity, User, identity migration, registration/login requests/controllers, identity-related Blade views, OptionalIdentityTest.
- Checks: SQLite `php artisan test --filter='OptionalIdentityTest|BusinessAuthTest'`: 20 passed / 98 assertions; `npm run build`: passed; targeted Pint: passed; `git diff --check`: passed. HTTP-rendered email-less public profile/editor/dashboard verified by tests. Baseline unrelated profile failures remain assigned to Q12/Q14/Q24.
- Decisions: disjoint email/username syntax avoids cross-namespace ambiguity; unique normalized new writes; destructive rollback refused pending reviewed data conversion. Database migrated only in test memory.
- Commit: `f4560742796bbe52876f0ee2d489d5672fb26bd0`.
- Next: Q01.

### Q01 — Admin login — Done
- Both portals now share authorized admin sign-in; fixed unsafe intended redirects, guaranteed administrator dashboard destination, remember token and session rotation. Provider/seeker direct admin requests denied, logout invalidates session.
- Files: AdminAuthController, task-only BusinessAuthController hunk, AdminAuthenticationTest. Existing user controller/test changes remain uncommitted and preserved.
- Checks: SQLite focused AdminAuthenticationTest|BusinessAuthTest|OptionalIdentityTest: 23 tests / 138 assertions passed; Pint on changed PHP passed. Isolated commit snapshot rechecked before commit without pre-existing user edits.
- Decision: admin destination fixed rather than following potentially stale/provider/external intended URL.
- Commit: `7213c6102ca7b95413ab0929b4e763ef60df62df`.
- Next: Q02.
- Q01 isolated committed snapshot: 21 tests / 125 assertions passed (two pre-existing user tests deliberately absent).

### Q02 — Accept/reject providers — Done
- Made review index/create/update/delete consistently require active provider with business profile; restricted/missing/non-provider direct requests return 404. Approval/reactivation status and availability reset now save transactionally. Preserved existing suspension/reactivation decisions and responsible admin/timestamps/reasons.
- Files: AdminProviderAccountController, EnsureProviderIsPublic, bootstrap middleware aliases, review route guards, ProviderModerationTest.
- Checks: SQLite focused ProviderModerationTest|review: 9 tests / 80 assertions passed; Pint passed; diff check passed; admin dashboard/account-status HTTP assertions verify rejection reasons.
- Decisions: existing admin state transitions preserved; availability reset on approval/reactivation.
- Commit: `4eea6c35ea1bcd547cc79ec7717009b00449d151`.
- Next: Q10.

### Q10 — Provider login — Done
- Provider login now clears stale/external intended URLs and chooses dashboard only for active accounts, account-status for every restricted state.
- Files: BusinessAuthController task-only redirect hunk; ProviderAuthenticationTest.
- Checks: focused authentication tests: 24 tests / 231 assertions passed; direct mutation/upload/delete restrictions tested for pending/rejected/suspended; remember tokens, session rotation, bad passwords (existing tests), logout and guest redirects verified. Diff check passed.
- Existing admin/customer login behavior preserved; pre-existing user changes excluded from commits.
- Commit: `df8ea4e07e39a665036aecfb7ea31df45d560664`.
- Next: Q21.

### Q21 — Provider preregistration — Done
- Confirmed early provider registration is the existing pending-account flow; creates linked empty profile atomically, rejects role/status spoofing, stays private, and unlocks editor after admin approval without another account.
- Clarified registration and account-status copy, added working status refresh link and next steps. Files: provider registration/status Blade views, ProviderPreregistrationTest.
- Checks: SQLite preregistration/auth/moderation group: 19 tests / 230 assertions passed; diff check passed. HTTP lifecycle covers registration → blocked/public-hidden → approval → edit → public render.
- Proposed interpretation matches repository; no separate waiting list.
- Commit: `d1f4e76a5e931dcd29c2cbe43ebfc7eff55b0a8c`.
- Next: Q18.

### Q18 — Add Photography — Done
- Added Photography to the single BusinessProfile WORK_CATEGORIES definition used by validation, editor options, preview and public display; no duplicate list. Directory integration uses this same definition in Q07.
- Files: BusinessProfile, CategoryChangesTest. Checks: SQLite CategoryChangesTest: 1 test / 7 assertions passed; editor/public HTTP rendering and mixed-category persistence verified; diff check passed.
- Dependency clarification: category definition tasks establish accepted filter labels; directory UI/filter integration follows Q03/Q07, avoiding premature seeker implementation.
- Commit: `e27f88904ab799be2af01e1b06907ed14ece805a`.
- Next: Q19.

### Q19 — Remove three retired categories — Done
- Removed exact Home Maintenance, Computer Repair and Mobile Repair from central options/validation. Added idempotent data migration preserving all other labels; logs affected count and category-less profile IDs. Editor/admin visibly flag category-less profiles; public empty profiles render safely.
- Files: category model/fixtures, retired-category migration, provider editor/admin dashboard, CategoryChangesTest.
- Checks: SQLite CategoryChangesTest|ProviderPreregistrationTest: 3 tests / 46 assertions passed; migration run twice on fixtures (3 affected, 1 category-less first run; 0 affected on rerun); exact similar labels preserved; retired labels rejected. Pint/diff check passed.
- Stored data inspection is limited to local fixtures; actual deployment counts will be logged by migration. No application/production database read or migrated. Irreversible removal down intentionally does not fabricate past assignments.
- Commit: `54e211e3fbc1485651711c06835d9cc0622659a6`.
- Next: Q20.

### Q20 — Moving → Transportation — Done
- Renamed the central category to Transportation; idempotent migration preserves old assignments and deduplicates profiles containing both labels without changing other categories. Shared filter normalization maps legacy Moving links to Transportation for Q07.
- Files: BusinessProfile, rename migration, CategoryChangesTest. Checks: SQLite category tests: 3 tests / 35 assertions passed; rename run twice and exact persistence/public render verified; old label rejected on profile save; Pint/diff check passed.
- No production data touched; migration logs deployment affected counts. Rollback cannot reliably distinguish pre-existing Transportation from migrated assignments and preserves data.
- Commit: `2c041f69b9eb3dbb44d1f8d352bbe69a7279ab6b`.
- Next: Q12.

### Q12 — Provider profile editing — Done
- Verified owner-only active-provider editing, multi-category/location persistence, public rendering, identity immutability, upload endpoints/previews and save feedback. Added phone validation and explicit request authorization. Fixed standalone gallery component error bag and public postal-code display. Updated stale save-redirect tests to current editor destination.
- Files: UpdateBusinessProfileRequest, gallery component, public location partial, profile tests, ProviderProfileEditingTest.
- Checks: SQLite focused editor/profile/upload group: 11 tests / 74 assertions passed; saved/input/error/public HTTP rendering tested; other account IDs ignored, non-provider/admin writes forbidden, nonexistent cross-account URL 404; Pint/diff check passed.
- Image concurrency/cleanup depth follows Q16; location select-all/removal depth follows Q14. Existing provider email stays read-only; supplied spoofed email/username never updates account.
- Commit: `ec0f5902c6afd33deb4aeab7262135f60c52e059`.
- Next: Q14.

### Q14 — Provider location — Done
- Verified multiple Lebanese districts/cities, address/postal display, server rejection of invalid/orphan city selections, empty coverage, select-all expansion, and persisted valid edits. Corrected outdated district validation assertion.
- Files: profile feature/editor tests plus tests/js/provider-location.test.mjs.
- Checks: SQLite location/profile group: 7 tests / 59 assertions passed; node --test tests/js/provider-location.test.mjs: passed, exercises actual editor JS district removal and select-all; diff check passed.
- Coverage storage uses existing decoded arrays; directory matching integration follows Q07/Q08. No maps/GPS or geocoding.
- Commit: `0e877891630888929ca8a64b8faba18e20df0e1b`.
- Next: Q16.

### Q16 — Provider pictures — Done
- Hardened uploads/replacement/deletion with fresh row locks, transactional capacity checks, MIME-derived filenames, failed-save cleanup and visible save errors. Gallery form sends stable image fingerprint to reject stale-index deletion. Cleanup only touches owning managed paths and refuses files referenced by another profile.
- Advertise/accept only verified JPG/PNG/WEBP: profile/brand 10 MB, gallery 5 MB each, maximum six. Unverified HEIC/HEIF/AVIF claims removed.
- Files: BusinessPhotoService/controller/upload requests, image/gallery components, ProviderImageSafetyTest, standalone concurrency verification.
- Checks: SQLite image/profile/auth group: 18 tests / 212 assertions passed; python3 tests/integration/image-concurrency.py: two overlapping temporary-SQLite requests, one saved/one blocked, six references and no orphan files. Supported formats, preview URLs, oversize/SVG rejection, stale models, failed save recovery, cross-owner/arbitrary/shared file safety verified. Pint and diff check passed.
- Legacy gallery delete requests without fingerprint retained; rendered forms use fingerprint. Storage and database concurrency verification uses disposable /tmp only.
- Commit: `e18cd12dfe6ebdb2a072e6ed810398bbf2751f2b`.
- Next: Q11.

### Q11 — Provider completion percentage — Done
- Centralized nine completion fields and shared business-edit validation rules in BusinessProfile. Completion counts valid persisted field values, rejects stale retired categories/orphan cities/invalid phones and invalid image references, excludes email; no stored percentage.
- Shared accessible completion component displays percent and missing guidance consistently in dashboard/editor. Files: BusinessProfile/request, completion component, dashboard/editor, completion and identity tests.
- Checks: SQLite focused completion/identity/profile/category group: 14 tests / 158 assertions passed; empty=0, email-less complete=100, removed phone=89, invalid phone/category/city=67; both views verified. Pint/diff check passed.
- Stored image paths are validated as owning managed references, without filesystem reads on completion rendering. Q16 approval timeout resolved by successful retry (e18cd12); Done status restored in tracker.
- Commit: `0c7380ed47609d75db157a67f179d094ff42146e`.
- Next: Q15.

### Q15 — Provider availability — Done
- Fixed dashboard to use is_available_for_hire; added working enquiry-availability toggle and inline errors. Ordinary profile saves now preserve availability unless explicitly submitted; editor toggle copy explains accepting work enquiries. Save failures return feedback without falsely claiming success.
- Files: BusinessProfileController, dashboard/editor views, ProviderAvailabilityTest.
- Checks: SQLite availability/auth/moderation group: 6 tests / 182 assertions passed; available/unavailable roundtrip and ordinary-edit persistence verified in dashboard/editor/public views; rejected invalid values and injected save failure; role/status denial and approval/reactivation unavailable covered by earlier tests. Diff check passed.
- Directory availability display integration follows Q07; enquiry status is not a booking.
- Commit: `ab36d04aa4429b461ce09ed3481629d7894030ab`.
- Next: Q13.

### Q13 — Provider dashboard — Done
- Replaced fictitious upcoming tools with persisted business summary; fixed misleading empty/active wording. Dashboard/editor load current profile state and safely recover a missing legacy active-provider profile with unavailable default. Q15 already corrected the is_available mismatch.
- Files: dashboard, dashboard/profile controllers, ProviderDashboardTest.
- Checks: SQLite dashboard/completion/availability/auth group: 6 tests / 160 assertions passed; identity/details/location/category/phone, completion, working links and availability agree across views; empty legacy profile recovers once. Diff check passed.
- No fabricated metrics, bookings or payment activity; profile creation checked only in isolated test databases.
- Commit: `62d5dcea29c486e84b71b9e65db202727cf32d29`.
- Next: Q24.

### Q24 — No bookings on site — Done
- Audited active Blade/controller UI; removed public Hire Now/coming-soon request modal and future-workflow promises. Added reusable working tel/mailto contact actions with missing-contact state, preserving enquiry availability and public discovery/reviews. Dashboard fictitious booking tools were removed in Q13.
- Files: PublicServiceProviderController, hiring card, provider-contact component, public profile test assertions, DirectProviderContactTest.
- Checks: complete SQLite suite: 78 tests / 638 assertions passed; case-insensitive source audit for booking/payment/Hire Now/coming soon/request workflow found none in active views/controllers; diff check passed. Both availability states retain working contacts; missing email/phone yields no empty action.
- Baseline seven failures are now resolved through Q12/Q14/Q24; no booking/payment system or data changes.
- Commit: `adb8ff4c9b5e1d7cf4ed88c24490f2755af97fbf`.
- Next: Q17.

### Q17 — #Hiring banner — Done
- Added clear accessible #Hiring badge only for available providers through existing availability component; replaced oversized purple decorative avatar ribbon with subtle brand-colored outline. Direct enquiry/contact copy and tel/mailto actions preserved. Shared badge is reusable by directory Q07.
- Files: availability-badge/provider-avatar, HiringBadgeTest, isolated UI fixture renderer.
- Checks: SQLite hiring/availability/contact group: 4 tests / 55 assertions passed; npm run build passed; headless Chrome at 390×844 available and 1280×900 unavailable: no horizontal overflow, badge correctly present/absent. Screenshots manually inspected at /tmp/qareeb-ui-q17/mobile.png and desktop.png; legible wrapped title/badge and correct brand layout.
- Browser launch sandbox error resolved by disposable-profile escalation; fixture probe/assets corrected and rechecked. No public deployment or production database access. Directory reuse tested in Q07.
- Commit: `34e9e78d7467d803620d49a0563363438cece57a`.
- Next: Q03.

### Q03 — Seeker registration/login — Done
- Added separate seeker registration/login/logout and protected minimal dashboard using existing customer role, now labeled Seeker. Reused normalized optional identities; no business profile created; spoofed roles ignored. Role-aware guest/auth redirects preserve admin/provider portals; seeker account cannot access provider/admin routes.
- Files: seeker auth controller/middleware/views/routes, User/UserRole, shared layout/welcome navigation, SeekerAuthenticationTest.
- Checks: complete SQLite suite: 82 tests / 694 assertions passed; registration/login/logout/remember/session rotation, role isolation, safe redirects, duplicate identities and password confirmation verified. Pint/diff check passed.
- Minimal dashboard is intentionally functional before Q06; signup/sign-in rate-limited at 10 requests/minute. Q22 email-less seeker UI integration verified.
- Commit: `c064a49272154894cbe8929a5a25429a4892cb84`.
- Next: Q05.

### Q05 — Seeker profile editing — Done
- Added owner-only private Seeker editor for name/phone/district/city using Lebanese definitions and dependent server validation; retains values/errors and save feedback. Contact/location fields excluded from User serialization/public discovery/reviews.
- Optional username/email edits reuse normalized unique identity rules ignoring only current owner; require current password, clear email verification on change, preserve legacy no-username accounts, and never flash password values. Failed saves show feedback without changing persisted profile.
- Files: private profile migration, User/AccountIdentity, SeekerProfileRequest/controller/view/routes/dashboard link, SeekerProfileTest.
- Checks: focused seeker/identity SQLite tests: 13 tests / 137 assertions passed; ownership/role/status, invalid combinations, private review exposure, identity changes/password/duplicates and injected-save failure verified. Pint/diff check passed. No public seeker profile route.
- Commit: `575a1e15cd24a23a397c07bf7fd4a2fb65498d8c`.
- Next: Q04.

### Q04 — Seeker completion percentage — Done
- Confirmed no conflicting completion decision; centralized name/phone/district/city validation and completion in User. Shared completion component shows calculated percent/missing fields on seeker editor/dashboard; email excluded and no duplicate stored percentage.
- Files: User, SeekerProfileRequest, shared completion component, seeker views, SeekerCompletionTest.
- Checks: SQLite seeker completion/profile/provider completion group: 6 tests / 84 assertions passed; edits progress from 25 to 100 without email, removing phone yields 75, invalid persisted city yields 50; both views agree. Pint/diff check passed.
- Rules use valid persisted profile fields; proposed four-field default matches repository.
- Commit: `e733d5d2e7a2010364cee619492de66fc90499d0`.
- Next: Q07.

### Q07 — Category search/provider directory — Done
- Added public paginated provider directory (12/page), exact category membership for JSON arrays and legacy single labels, active service-provider/business-profile restriction, query-preserving links/reset/empty states, cards with identity/location/rating/availability/public links. Linked seeker dashboard and welcome navigation.
- Integrates Photography filtering, rejected retired labels, legacy Moving→Transportation links, shared #Hiring badge and unavailable state. Files: directory request/controller/view/routes, BusinessProfile selection scope, navigation, ProviderDirectoryTest.
- Checks: SQLite directory/category/hiring group: 6 tests / 76 assertions passed; exact/legacy matches, all restricted states/non-provider/missing-profile exclusion, 13-result pagination and empty/invalid filters. Pint/diff check passed.
- Database membership uses bound values with guarded JSON parsing; repository MySQL deployment and SQLite verification supported. JSON expression compiler error resolved. Location composition follows Q08.
- Commit: `f45424f25acf514ca46dd46da4242838d7611bfd`.
- Next: Q08.

### Q08 — Location search — Done
- Added composable district/city service-coverage filters using existing Lebanese definitions and exact JSON/legacy storage matching. City requires supported district and valid relationship; invalid query redirects to filter form with field errors/submitted input. Native selects disable unrelated city groups and district changes clear selected city.
- Files: directory request/controller/view, ProviderDirectoryTest.
- Checks: SQLite directory/profile-location group: 6 tests / 99 assertions passed; multi-district/city and legacy coverage, category composition, mismatches/invalid arrays/missing district, combined pagination query persistence and reset/empty copy verified. Pint/diff check passed.
- Matching uses selected service coverage only; does not infer entire-district coverage from missing cities. No maps/GPS/geocoding/distance search.
- Commit: `444b855bc930acd7789cdf4a7dc512d7d72423de`.
- Next: Q09.

### Q09 — Complaint survey — Done
- Added private structured complaint survey (type/description/optional listed provider), seeker-owned list/detail/status pages, and administrator-only review queue with Open/In Review/Resolved, internal notes and reviewer/timestamp. Complaints never enter public reviews or discovery; notes/private metadata hidden from serialization.
- Duplicate submissions reuse owner-scoped UUID tokens; identical owner/type/provider/content within 10 minutes is deduplicated under owner-row lock. Submission limit 5/minute; references accept active listed providers only.
- Files: Complaint/ReportStatus, migration, complaint request/controllers/seeker/admin views/routes/navigation, PrivateComplaintTest.
- Checks: whole SQLite suite: 93 tests / 863 assertions passed; private status transitions, ownership/role/guest checks, forged user/status/notes ignored, inactive/non-provider references rejected, token/content duplicates and 429 limit verified. Pint/diff check passed.
- Proposed private-survey interpretation matches repository; no public complaint content or administrator notes.
- Commit: `55417a9548be6c4fcd9b282a174bdea24365e3c0`.
- Next: Q06.

### Q06 — Seeker dashboard — Done
- Built responsive Seeker dashboard with real name, private profile percentage/missing fields, working profile/discovery/complaint/status links and logout. Shows incomplete/complete profiles without fabricated activity or booking/payment metrics. Styled accessible native completion progress consistently with Qareeb colors.
- Files: seeker dashboard, shared completion component/CSS, SeekerDashboardTest and isolated UI fixture renderer.
- Checks: focused dashboard/seeker/provider completion/auth SQLite group: 6 tests / 103 assertions passed; all destination routes return successful owned pages; non-seeker access forbidden. npm run build passed. Headless Chrome 390×1000 incomplete and 1280×1100 complete: no horizontal overflow; screenshots manually inspected at /tmp/qareeb-ui-q06/mobile.png and desktop.png. Diff check passed.
- A browser screenshot paint artifact was resolved by compositor-complete capture; profile/contact privacy and established UI conventions preserved.
- Commit: `36d368af0748e3dc5b47ca894b2e3df4c7c643e0`.
- Next: Q25.

### Q25 — Image privacy reporting — Done
- Added visitor report actions/forms for profile/cover/gallery images, server-resolved type/hash targets, private reason/description/optional contact, metadata retained after replacement without image copies, duplicate token/content keys and 5/minute limit. No report content/contact appears publicly; no automatic removal.
- Administrator-only queue uses Open/In Review/Resolved, internal notes/reviewer timestamps and explicit removal reason. Audit persists before removal; service locks fresh profile, matches original path/hash/type, refuses cross-owner/shared/arbitrary paths and never removes replacement/unrelated images. Failed audit performs no deletion; failed removal stays in review.
- Files: ImagePrivacyReport/PublicProviderImage, migration, report/admin controllers/routes/views/action component, hero/gallery/admin navigation, BusinessPhotoService, ImagePrivacyReportTest.
- Checks: full SQLite suite: 100 tests / 981 assertions passed; all image types, public/private role guards, forged path/reference inputs, duplicates, validation/429, exact removal and replacement metadata, failure-audit safety verified. python3 tests/integration/image-concurrency.py passed again; Pint/diff check passed.
- No raw IP retained: anonymous actor keyed by session HMAC, authenticated actor by user HMAC. Original filename/size/MIME retained where available; concurrent missing file metadata handled safely. No duplicate image storage.
- Commit: pending recording immediately after commit.
- Next: Q23.
