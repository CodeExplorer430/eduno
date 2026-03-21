# Usability Evaluation — Heuristic Evaluation

**Project:** Eduno LMS
**Version:** 1.0.0
**Date:** 2026-03-20
**Method:** Nielsen's 10 Usability Heuristics
**Context:** CCS 123 — Introduction to Human–Computer Interaction

---

## Overview

This heuristic evaluation examines Eduno LMS against Nielsen's 10 Usability Heuristics.
Each heuristic is rated on a 0–4 severity scale:

| Severity | Meaning |
|---|---|
| 0 | Not a problem |
| 1 | Cosmetic issue — fix if time permits |
| 2 | Minor usability problem — low priority |
| 3 | Major usability problem — high priority |
| 4 | Usability catastrophe — must fix before release |

---

## Heuristic 1 — Visibility of System Status

> The system should always keep users informed about what is going on through appropriate
> feedback within a reasonable time.

### Findings

**✅ Pass — Submission confirmation (Severity 0)**

After a student submits an assignment, Inertia redirects to the submission detail page.
The submission timestamp, attempt number, and status ("Submitted") are immediately visible.
The confirmation uses `role="alert"` so screen readers announce it without requiring focus.

**✅ Pass — Grade status (Severity 0)**

Submissions display a status badge (`StatusBadge`) at all times: Submitted → Graded →
Returned. Students can see whether a grade exists and whether it has been released,
without needing to contact the instructor.

**✅ Pass — Deadline urgency (Severity 0)**

The "What's Next?" dashboard widget color-codes deadlines: red (≤ 24 h), amber (≤ 72 h),
green (> 72 h). The urgency label is also included in the `aria-label`, so it is not
color-only.

**✅ PASS — Upload progress indicator (Severity 2) — Resolved in v1.2.0**

When a student submits a large file (up to 25 MB), there is no progress bar or spinner.
The submit button is disabled (`processing` state via `useForm()`), but the user sees no
indication of how long to wait. On slow connections this can feel like the page has frozen.

**Resolution:** A percentage progress bar (`role="progressbar"`) and live text ("Uploading… N%") appear below the file input during upload, using Inertia's `router.post()` `onProgress` callback. The bar disappears on success or error.

---

## Heuristic 2 — Match Between System and the Real World

> The system should speak the users' language, use words, phrases, and concepts familiar
> to the user, rather than system-oriented terms.

### Findings

**✅ Pass — Domain vocabulary (Severity 0)**

Eduno uses academic vocabulary throughout: "Assignment," "Module," "Lesson," "Section,"
"Grade," "Announcement," "Deadline." These directly match the vocabulary used by UCC
faculty and students in daily academic contexts.

**✅ Pass — Status labels (Severity 0)**

Status badges use "Draft," "Published," and "Late" — terms understood by both instructors
and students without needing documentation.

**✅ Pass — Date formatting (Severity 0)**

Dates use Philippine locale format (`en-PH`): "Mon, Mar 24, 02:30 PM". This matches how
dates are communicated at UCC.

**✅ PASS — UCC block codes displayed (Severity 1) — Resolved in v1.3.0**

At UCC, students commonly refer to their "class" or "block" (e.g., BSCS-2A) rather than
"section." The term "Section" is used correctly in the academic registrar sense but may
initially confuse students who expect to see "class" or "block."

**Resolution:** Sections now display the UCC block code (e.g., 'BSCS-2A') in parentheses in all views (`Course/Show.vue`, `Assignment/Index.vue`), matching the identifiers students and instructors use daily.

---

## Heuristic 3 — User Control and Freedom

> Users often choose system functions by mistake and will need a clearly marked "emergency
> exit" to leave the unwanted state without having to go through an extended dialogue.

### Findings

**✅ Pass — Breadcrumb navigation (Severity 0)**

Every page inside a section provides a breadcrumb trail (Course → Section → Module →
Lesson). Users can navigate back to any level without using the browser back button.

**✅ Pass — Draft/Publish workflow (Severity 0)**

Instructors can toggle modules, lessons, and announcements between Draft and Published.
Publishing is not irreversible — an instructor can unpublish at any time.

**✅ PASS — Submission pre-warning for single-attempt assignments (Severity 2) — Resolved in v1.2.0**

Once a student submits an assignment, they cannot delete or replace their submission
unless `allow_resubmission` is enabled by the instructor. The default is `false`.
Students who accidentally upload the wrong file are stuck unless the instructor
manually enables resubmission.

**Resolution:** An amber warning banner ("You have 1 attempt. Make sure your file is correct before submitting.") is displayed above the file upload form whenever `allow_resubmission` is `false`, setting expectations before the action.

**✅ Pass — Destructive action confirmation (Severity 0)**

Delete operations (courses, modules, lessons, assignments) use `DangerButton.vue` with
a distinct red appearance, clearly differentiating destructive actions from primary ones.

---

## Heuristic 4 — Consistency and Standards

> Users should not have to wonder whether different words, situations, or actions mean
> the same thing. Follow platform conventions.

### Findings

**✅ Pass — Component consistency (Severity 0)**

All forms use the same component set: `InputLabel`, `TextInput`, `InputError`,
`PrimaryButton` / `DangerButton`. This produces a visually and behaviorally consistent
form pattern across all 20+ forms in the application.

**✅ Pass — Navigation consistency (Severity 0)**

The same navigation bar (Dashboard, Courses, Reports for Admin) appears on every
authenticated page. The active link always shows the indigo-400 bottom-border indicator.

**✅ Pass — Card pattern (Severity 0)**

Every content section uses the same `bg-white shadow-sm sm:rounded-lg` card pattern.
Users learn the card = content association once and apply it everywhere.

**✅ PASS — Preference routes consolidated (Severity 1) — Resolved in v1.3.0**

Accessibility preferences can be updated via both `profile.preferences` (PATCH) and
`preferences.update` (PUT). Both point to the same action class, but the dual routes
may cause inconsistency if future UI changes reference the wrong one.

**Resolution:** Both `/profile/preferences` (PATCH) and `/preferences` (PUT) now route through a single `UpdatePreferences::handle()` action backed by `PatchPreferencesRequest`. The redundant `UpdateUserPreferences` action was deleted.

---

## Heuristic 5 — Error Prevention

> Even better than good error messages is a careful design which prevents a problem
> from occurring in the first place.

### Findings

**✅ Pass — File validation (Severity 0)**

File type and size are validated server-side via Laravel Form Requests before any upload
is accepted. Rejected uploads return a field-specific error message immediately.

**✅ Pass — Late detection (Severity 0)**

The `is_late` flag is set server-side, comparing `submitted_at` to `due_at`. There is
no way for a student to manipulate their device clock to avoid the flag.

**✅ Pass — Enrollment duplicate guard (Severity 0)**

The `EnrollStudent` action throws a validation exception if a student is already enrolled
in a section, preventing duplicate enrollment records.

**✅ Pass — Grade immutability post-release (Severity 0)**

Once a grade is released (`released_at` is set), students can see it. The `audit_log`
records every `grade.created`, `grade.updated`, and `grade.released` event — instructors
cannot quietly change a released grade without a traceable record.

**✅ PASS — Past-due-date warning on assignment creation (Severity 2) — Resolved in v1.2.0**

When an instructor creates an assignment with a due date in the past, the form accepts it
without a warning. Students would immediately see the assignment as past-due with no
opportunity to submit.

**Resolution:** Both `Assignment/Create.vue` and `Assignment/Edit.vue` now include a `computed` `isPastDue` check that renders an amber `role="alert"` warning beneath the due-date field whenever the selected date is in the past.

---

## Heuristic 6 — Recognition Rather Than Recall

> Minimize the user's memory load by making objects, actions, and options visible.
> The user should not have to remember information from one part of the interface
> to another.

### Findings

**✅ Pass — Breadcrumbs eliminate context switching (Severity 0)**

The breadcrumb on the Lesson page shows: Course Title → Section Name → Module Title →
Lesson Title. Users never need to remember where they are in the hierarchy.

**✅ Pass — Assignment instructions always visible (Severity 0)**

On the submission form, the original assignment instructions are displayed above the
upload area. Students do not need to open a separate page to recall what they need
to submit.

**✅ Pass — "What's Next?" reduces recall burden (Severity 0)**

The deadline widget on the Dashboard surfaces the most urgent upcoming assignments
without requiring students to navigate to each course individually.

**✅ PASS — Submission status on assignment list (Severity 2) — Resolved in v1.3.0**

On the student-facing assignment page, students cannot see their previous submission
status or attempt number. They must navigate to the submission detail page separately.

**Resolution:** Students can see Submitted / Graded status and submission date directly on the assignment list (`Assignment/Index.vue`), eliminating the need to click into each assignment.

---

## Heuristic 7 — Flexibility and Efficiency of Use

> Accelerators — unseen by the novice user — may often speed up the interaction for the
> expert user so that the system can cater to both inexperienced and experienced users.

### Findings

**✅ Pass — Keyboard navigation (Severity 0)**

All interactive elements are reachable via Tab/Shift+Tab. The skip-to-content link
allows keyboard users to bypass the navigation bar entirely. Focus order follows DOM
order (no `tabindex` hacks).

**✅ Pass — Pagination for large lists (Severity 0)**

Submission, course, and lesson lists are paginated — expert users working through a
large cohort can move through pages efficiently.

**✅ PASS — Sequential "Next Submission →" navigation in grading (Severity 2) — Resolved in v1.2.0**

Instructors grade one submission at a time. For a class of 30 students, grading requires
30 individual form submissions. There is no "next submission" button or batch input.

**Resolution:** `SubmissionController::show()` now computes `prevSubmissionId` and `nextSubmissionId` from all submissions for the same assignment ordered by `submitted_at`. `Submission/Show.vue` renders accessible `← Previous` / `Next →` `<Link>` controls at the top, letting instructors move through the queue without returning to the list.

**⚠ Minor — No keyboard shortcut for publish (Severity 1)**

Publishing a module or lesson requires clicking through to the edit page and pressing the
"Publish" button. There is no shortcut to publish directly from the list view.

**Recommendation (future):** Add a quick-action publish toggle to the module/lesson list
row for instructor power users.

---

## Heuristic 8 — Aesthetic and Minimalist Design

> Dialogues should not contain irrelevant or rarely needed information. Every extra unit
> of information in a dialogue competes with the relevant units of information and
> diminishes their relative visibility.

### Findings

**✅ Pass — Clean card layout (Severity 0)**

Each page uses white cards on a gray-100 background, with generous padding (p-6) and
clear typographic hierarchy. Secondary information (course code, section name) is rendered
in `text-xs gray-500` so it does not compete with primary content.

**✅ Pass — Empty states instead of hidden elements (Severity 0)**

When a list has no items, a dashed-border `EmptyState` card appears rather than a blank
region. This signals intentionality — the page has loaded correctly and the list is
genuinely empty.

**✅ Pass — Minimal navigation items (Severity 0)**

The navigation bar shows only three items for most users (Dashboard, Courses, Reports for
Admin). There is no navigation bloat.

**✅ PASS — Resolved in v1.1.0**

The dashboard now uses role-differentiated template branches: the student view
(`v-if="upcoming !== undefined"`) and instructor view (`v-if="sections !== undefined"`)
are mutually exclusive, ensuring each role sees only relevant content.
"Recent Grades" is never rendered for instructors.

---

## Heuristic 9 — Help Users Recognize, Diagnose, and Recover from Errors

> Error messages should be expressed in plain language (no codes), precisely indicate
> the problem, and constructively suggest a solution.

### Findings

**✅ Pass — Field-specific error messages (Severity 0)**

Laravel Form Request validation returns field-keyed errors (e.g., "The file field is
required." for `file`, "The score must be between 0 and 100." for `score`). Inertia
maps these to the correct `InputError` component for each field.

**✅ Pass — `role="alert"` on error fields (Severity 0)**

`InputError.vue` uses `role="alert"`, causing screen readers to immediately announce
the error text when it appears without requiring the user to focus the error element.

**✅ Pass — Rate limit messaging (Severity 0)**

After 5 failed login attempts, the system returns a throttling error with a clear
message rather than a generic HTTP 429.

**✅ PASS — Inline file-selection feedback and size guard (Severity 1) — Resolved in v1.3.0**

If a user selects a file that is too large (> 25 MB), they only discover this after
the form is submitted and the server rejects it. There is no client-side file size
check before upload begins.

**Resolution:** A live summary ('2 file(s) selected · 1.4 MB') appears below the file input on selection. Files exceeding 25 MB trigger an immediate `role='alert'` error and disable the submit button before any upload attempt.

---

## Heuristic 10 — Help and Documentation

> Even though it is better if the system can be used without documentation, it may
> be necessary to provide help and the system should make it easy for users to search
> for relevant information.

### Findings

**✅ Pass — Empty state messages as inline help (Severity 0)**

Empty states contain human-readable explanations, functioning as micro-documentation:
"No upcoming deadlines in the next 7 days. Great work!" tells students what the section
is for and why it is empty.

**✅ Pass — Submission instructions always displayed (Severity 0)**

The original assignment instructions are shown above the file upload input. Students do
not need to navigate away to read the task description.

**✅ PASS — Dismissible first-login welcome banner (Severity 2) — Resolved in v1.2.0**

First-time users land on the Dashboard with no introduction to Eduno's workflow. A new
student who has just been enrolled sees the "What's Next?" widget but no guidance on
how to navigate to a course or submit an assignment.

**Resolution:** `Dashboard.vue` now renders a `role="status" aria-live="polite"` welcome banner for users who have not yet dismissed it. Dismissal is persisted via `localStorage` (key `eduno_welcome_dismissed`) and the banner is hidden immediately on click via a `showWelcome` ref.

**✅ PASS — "Accepted file types + size" hint (Severity 1) — Resolved in v1.2.0**

The file upload area on the submission form does not indicate accepted file types or the
25 MB size limit until the user makes an error.

**Resolution:** A hint line ("Accepted: PDF, DOCX, PPTX, ZIP — Maximum 25 MB per file")
is displayed directly below the file input in `Assignment/Show.vue`, giving students the
constraint information before they attempt an upload.

---

## Summary of Findings

| Heuristic | Rating | Severity Issues |
|---|---|---|
| 1. Visibility of System Status | Good | ✅ All issues resolved |
| 2. Match System and Real World | Good | ✅ All issues resolved |
| 3. User Control and Freedom | Good | ✅ All issues resolved |
| 4. Consistency and Standards | Excellent | ✅ All issues resolved |
| 5. Error Prevention | Excellent | ✅ All issues resolved |
| 6. Recognition Rather Than Recall | Good | ✅ All issues resolved |
| 7. Flexibility and Efficiency | Good | 1 minor open — no quick-publish shortcut on module/lesson lists |
| 8. Aesthetic and Minimalist Design | Excellent | ✅ All issues resolved |
| 9. Recognize, Diagnose, Recover | Good | ✅ All issues resolved |
| 10. Help and Documentation | Acceptable | ✅ All issues resolved |

---

## Priority Recommendations

### High Priority (Severity 2–3) — ✅ All resolved in v1.2.0

1. ✅ **File upload progress indicator** — Percentage progress bar added to submission form (Heuristic 1).

2. ✅ **Submission pre-warning ("1 attempt, verify your file")** — Amber warning banner shown before file upload for single-attempt assignments (Heuristic 3).

3. ✅ **Past-due-date warning on assignment creation** — Client-side alert rendered on Create and Edit forms when due date is in the past (Heuristic 5).

4. ✅ **"Next Submission →" navigation in grading view** — Previous/Next links on `Submission/Show` page allow sequential grading (Heuristic 7).

5. ✅ **First-login welcome banner** — Dismissible banner on Dashboard for first-time users, persisted via localStorage (Heuristic 10).

### Low Priority (Severity 1) — ✅ All resolved in v1.2.0 / v1.3.0

6. ✅ **"Accepted file types + max size" hint** — Resolved in v1.2.0 (H10-1).
7. ✅ **Submission status on assignment page** — Resolved in v1.3.0 (H6-1).
8. ✅ **UCC block codes displayed** — Resolved in v1.3.0 (H2-1).

---

## Evaluator Note

This evaluation was conducted through code review and automated WCAG testing
(`vitest-axe`, 184 specs). A follow-up evaluation with 5 representative UCC users
(2 students, 2 instructors, 1 admin) performing structured tasks would yield
additional severity ratings grounded in observed behavior.
