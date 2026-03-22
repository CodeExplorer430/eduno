# Changelog

All notable changes to Eduno LMS are documented here.

## [2.1.0] — 2026-03-22

### Demo Infrastructure

- **DemoSeeder** (`database/seeders/DemoSeeder.php`): Added a fully-populated,
  idempotent seeder for academic evaluation. Creates 9 demo accounts (1 admin,
  2 instructors, 6 students), 3 published courses with 6 sections, 24 lessons,
  9 assignments, varied submissions/grades, 18 audit log entries, and 11
  announcements. All accounts use password `password`.

### In-App Notification Center (FR-034–037)

- **Database notification channel** added to all four notification classes
  (`AnnouncementPublishedNotification`, `GradeReleasedNotification`,
  `NewSubmissionNotification`, `DeadlineReminderNotification`). All notifications
  now persist in the `notifications` table in addition to email delivery.
- **`notifications` migration** (`2026_03_22_100000_create_notifications_table.php`):
  Standard Laravel UUID notifications table.
- **`NotificationController`** with four routes:
  `GET /notifications`, `GET /notifications/{id}` (mark-read + redirect),
  `POST /notifications/{id}/read`, `POST /notifications/read-all`.
- **Three Action classes**: `GetUserNotifications`, `MarkNotificationRead`,
  `MarkAllNotificationsRead` (`app/Domain/Notification/Actions/`).
- **`NotificationBell.vue`** component: bell icon with unread-count badge in the
  global nav; WCAG-compliant `aria-label`, visible focus ring, badge hidden at 0.
- **`Notifications/Index.vue`** page: paginated list with read/unread dot indicator,
  per-item "Mark as read", "Mark all as read" header button, and empty state with
  `role="status"`.
- **Shared prop** `auth.unread_notifications_count` added to `HandleInertiaRequests`
  so the badge count is available on every page without an extra request.
- **DemoSeeder extended**: 7 pre-seeded notifications (3 unread for
  `juan@eduno.test`) so the bell badge is populated immediately on demo login.

### Tests

- PHP: **491** (6 new Pest feature tests in `tests/Feature/Notification/NotificationTest.php`
  covering index auth, unauthenticated redirect, show-redirect-mark-read, cross-user
  isolation, mark-all-read, and pagination)
- Vitest specs: **517** (12 new: NotificationBell × 7, Notifications/Index × 5)

---

## [1.4.0] — 2026-03-21

### Usability Fix (Cosmetic — Nielsen Heuristic 2)

- **H2-1 cosmetic — Match Between System and Real World** (`Course/Show.vue`):
  Added a subtitle paragraph beneath the "Sections (classes / blocks)" heading —
  "Sections correspond to your enrolled class block (e.g., BSCS-2A)." — so students
  immediately understand the UCC block-code convention without having to guess.

### Tests

- Vitest specs: **184** (3 new specs in `Course/Show.spec.ts` covering section name +
  block_code rendering, helper text content, and axe WCAG compliance)

---

## [1.3.0] — 2026-03-21

### Usability Fixes (Severity-1 Findings — Nielsen Heuristics)

- **H6-1 — Recognition over Recall** (`AssignmentController`, `Assignment/Index.vue`):
  Students can now see their submission status (Submitted / Graded) and date directly
  on the assignment list, eliminating the need to click into each assignment.
- **H9-1 — Help Users Recognize, Diagnose, and Recover** (`Assignment/Show.vue`):
  After selecting files, a live summary ("2 file(s) selected · 1.4 MB") appears below
  the file input so students can confirm their selection before submitting.
- **H2-1 — Match Between System and Real World** (`CourseSection`, `Course/Show.vue`,
  `Assignment/Index.vue`): Sections now display UCC block codes (e.g., "Section A
  (BSCS-2A)") in all views, matching the identifiers students and instructors use.
- **H4-1 — Consistency and Standards** (`AccessibilityController`,
  `PatchPreferencesRequest`): Consolidated two divergent A11y preference routes onto
  a single `UpdatePreferences::handle()` action with a shared `PatchPreferencesRequest`;
  deleted the redundant `UpdateUserPreferences` action class.

### Tests

- PHP: **297 → 297** (3 renamed/updated Pest specs for `UpdatePreferences`)
- Vitest specs: **181** (7 new specs covering H6-1 × 4 and H9-1 × 2; 1 block_code
  fixture update propagated across `Announcement/Index`, `Lesson/Show`, `Module/Show`)

---

## [1.2.0] — 2026-03-21

### Usability Fixes (Severity-2 Findings — Nielsen Heuristics)

- **H1-1 — Visibility of System Status** (`Assignment/Show.vue`): Added upload
  progress bar so students see real-time feedback during file submission.
- **H3-1 — User Control and Freedom** (`Dashboard.vue`): Welcome banner is now
  dismissible; state persisted so it doesn't reappear on subsequent visits.
- **H5-1 — Error Prevention** (`Assignment/Show.vue`): Students are shown a
  prominent 1-attempt warning before submitting when only one attempt is allowed.
- **H7-1 — Flexibility and Efficiency of Use**: Dismiss button for the welcome
  banner is fully keyboard-accessible (Enter/Space activates, focus ring visible).
- **H10-1 — Help and Documentation** (`Assignment/Show.vue`): Accepted file types
  and maximum file size are displayed as a hint adjacent to the upload input.

### Tests

- Vitest specs: **168 → 175** (7 new specs covering H1-1, H3-1, H5-1, H7-1, H10-1
  in `Assignment/Show.spec.ts` and `Dashboard.spec.ts`)
- PHP: `SendDeadlineRemindersTest` unit test added (`tests/Unit/`)

---

## [1.0.0] - 2026-03-20

### Features (48 Functional Requirements)

- **Auth** (FR-001–007): Role-based auth (Student / Instructor / Admin),
  registration, email verification, login, profile management
- **Courses** (FR-008–011): Course CRUD, section management,
  enrollment and unenrollment workflows
- **Modules & Lessons** (FR-012–016): Module and lesson CRUD,
  publish/draft lifecycle, resource file uploads (signed URLs)
- **Assignments** (FR-017–022): Assignment CRUD, publish, file submission,
  automatic late detection, student resubmission
- **Grading** (FR-023–027): Grade submissions, release grades to students,
  immutable audit log for every grade change, student grade view
- **Announcements** (FR-028–033): Announcement CRUD, publish/draft,
  breadcrumb navigation
- **Dashboards** (FR-018, §3.1): Role-specific dashboards (Student /
  Instructor / Admin), What's Next deadline widget
- **Admin** (FR-034–040): User management, system-wide reports,
  audit log viewer
- **Notifications** (FR-041–044): In-app notifications, queue-backed
  delivery via Laravel Horizon
- **Accessibility** (FR-045–048): WCAG 2.2 Level AA, user a11y preferences
  (reduced motion, high contrast), offline caching via service worker

### Quality Gates (all passing at release)

- PHP: PSR-12, PHPStan level 5, **295 Pest tests** (feature + unit)
- Vue: TypeScript strict mode, **168 Vitest specs** with vitest-axe
  WCAG assertions on every page component
- WCAG 2.2 Level AA verified on all pages via axe-core
- OWASP Top 10 (2021) mitigations in place (Policies, Eloquent ORM,
  rate limiting, signed URLs, audit logging)
- NIST SSDF PW.5, PS.1, RV.1 controls applied
