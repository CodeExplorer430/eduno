# SRS Functional Requirements Traceability Matrix

**Project:** Eduno LMS
**Version:** 1.0.0
**Date:** 2026-03-20
**Standard:** ISO/IEC 25010 Functional Suitability

Every Functional Requirement (FR) from `eduno_srs.md` is traced to the implementing
code artefacts: Action class, Controller method, route name, and test file.

---

## FR-001–FR-005 · User Authentication & Authorization

| FR | Description | Action / Middleware | Route | Test |
|---|---|---|---|---|
| FR-001 | Login with email and password | Laravel Breeze `AuthenticatedSessionController::store` | `login` | `tests/Feature/Auth/AuthenticationTest.php` |
| FR-002 | Password reset | Breeze `NewPasswordController`, `PasswordResetLinkController` | `password.request`, `password.reset` | `tests/Feature/Auth/PasswordResetTest.php` |
| FR-003 | Email verification | Breeze `EmailVerificationNotificationController`, `VerifyEmailController` | `verification.send`, `verification.verify` | `tests/Feature/Auth/EmailVerificationTest.php` |
| FR-004 | Role-based permissions (Student / Instructor / Admin) | `App\Enums\UserRole`, `App\Http\Middleware\*` + Policies | all protected routes | `tests/Feature/Auth/RoleBasedAccessTest.php` |
| FR-005 | Restrict unauthorized access | `auth`, `verified` middleware on all resource routes | all protected routes | `tests/Feature/Auth/RoleBasedAccessTest.php` |

---

## FR-006–FR-007 · User Profile Management

| FR | Description | Action | Route | Test |
|---|---|---|---|---|
| FR-006 | View and update basic profile | `ProfileController::update` | `profile.edit`, `profile.update` | `tests/Feature/ProfileTest.php` |
| FR-007 | Save accessibility preferences | `UpdatePreferences` (`app/Domain/Accessibility/Actions/`) | `preferences.update`, `profile.preferences` | `tests/Feature/Accessibility/PreferencesTest.php` |

---

## FR-008–FR-011 · Course Management

| FR | Description | Action | Route | Test |
|---|---|---|---|---|
| FR-008 | Instructor creates and manages courses | `CreateCourse`, `UpdateCourse` (`app/Domain/Course/Actions/`) | `courses.store`, `courses.update` | `tests/Feature/Course/CourseManagementTest.php` |
| FR-009 | Organize courses into sections | `CreateCourseSection` (`app/Domain/Course/Actions/`) | `courses.sections.store` | `tests/Feature/Course/CourseManagementTest.php` |
| FR-010 | Admin manages course status | `UpdateCourse` (status field) | `courses.update` | `tests/Feature/Course/CourseManagementTest.php` |
| FR-011 | Students view enrolled courses | `CourseController::index` | `courses.index` | `tests/Feature/Course/CourseShowTest.php` |

---

## FR-012–FR-016 · Modules, Lessons, and Resources

| FR | Description | Action | Route | Test |
|---|---|---|---|---|
| FR-012 | Instructor creates course modules | `CreateModule` (`app/Domain/Module/Actions/`) | `sections.modules.store` | `tests/Feature/Module/ModuleManagementTest.php` |
| FR-013 | Instructor creates lessons under modules | `CreateLesson` (`app/Domain/Module/Actions/`) | `modules.lessons.store` | `tests/Feature/Lesson/LessonAccessTest.php` |
| FR-014 | Upload and manage learning resources | `UploadResource`, `DeleteResource` (`app/Domain/Module/Actions/`) | `lessons.resources.store`, `resources.destroy` | `tests/Feature/Lesson/LessonAccessTest.php` |
| FR-015 | Students access available lessons and resources | `LessonController::show`, `ResourceController::show` | `modules.lessons.show`, `resources.download` | `tests/Feature/Lesson/LessonAccessTest.php` |
| FR-016 | Visibility control (publish/draft lifecycle) | `PublishModule`, `PublishLesson` (`app/Domain/Module/Actions/`) | `modules.publish`, `lessons.publish` | `tests/Feature/Module/ModuleManagementTest.php` |

---

## FR-017–FR-019 · Announcements

| FR | Description | Action | Route | Test |
|---|---|---|---|---|
| FR-017 | Instructor posts announcements | `CreateAnnouncement`, `PublishAnnouncement` (`app/Domain/Announcement/Actions/`) | `sections.announcements.store`, `announcements.publish` | `tests/Feature/Announcement/AnnouncementManagementTest.php` |
| FR-018 | Recent announcements on student dashboard | `DashboardController::index` | `dashboard` | `tests/Feature/Dashboard/DashboardTest.php` |
| FR-019 | Email notification on announcement publish | `PublishAnnouncement` → queued notification via Horizon | `announcements.publish` | `tests/Feature/Announcement/AnnouncementManagementTest.php` |

---

## FR-020–FR-023 · Assignments

| FR | Description | Action | Route | Test |
|---|---|---|---|---|
| FR-020 | Instructor creates assignments with due dates | `CreateAssignment` (`app/Domain/Assignment/Actions/`) | `sections.assignments.store` | `tests/Feature/Assignment/AssignmentManagementTest.php` |
| FR-021 | Specify accepted file types and submission rules | `CreateAssignment` (allow_resubmission, max_score) | `sections.assignments.store` | `tests/Feature/Assignment/AssignmentManagementTest.php` |
| FR-022 | Students view active assignments | `AssignmentController::index` | `sections.assignments.index` | `tests/Feature/Assignment/AssignmentAccessTest.php` |
| FR-023 | Indicate due dates and status clearly | `AssignmentController::show`, `GetUpcomingDeadlines` | `sections.assignments.show`, `dashboard` | `tests/Feature/Assignment/AssignmentAccessTest.php` |

---

## FR-024–FR-029 · Submissions

| FR | Description | Action | Route | Test |
|---|---|---|---|---|
| FR-024 | Student uploads files for submission | `SubmitAssignment` (`app/Domain/Submission/Actions/`) | `assignments.submissions.store` | `tests/Feature/Submission/SubmissionTest.php` |
| FR-025 | Validate file type and size | `StoreSubmissionRequest` (Form Request MIME + size validation) | `assignments.submissions.store` | `tests/Feature/Submission/SubmissionTest.php` |
| FR-026 | Record submission timestamp and attempt number | `SubmitAssignment` (submitted_at, attempt_no) | `assignments.submissions.store` | `tests/Feature/Submission/SubmissionTest.php` |
| FR-027 | Identify late submissions | `SubmitAssignment` (is_late flag vs due_at) | `assignments.submissions.store` | `tests/Feature/Submission/SubmissionTest.php` |
| FR-028 | Instructor views submitted files | `SubmissionController::index`, `SubmissionController::show` | `assignments.submissions.index`, `submissions.show` | `tests/Feature/Submission/SubmissionTest.php` |
| FR-029 | Submission confirmation shown to student | `SubmissionController::show` (Inertia response) | `submissions.show` | `tests/Feature/Submission/SubmissionTest.php` |

---

## FR-030–FR-033 · Grading and Feedback

| FR | Description | Action | Route | Test |
|---|---|---|---|---|
| FR-030 | Instructor assigns grades | `GradeSubmission` (`app/Domain/Submission/Actions/`) | `submissions.grade.store` | `tests/Feature/Submission/GradingTest.php` |
| FR-031 | Instructor provides written feedback | `GradeSubmission` (feedback field) | `submissions.grade.store` | `tests/Feature/Submission/GradingTest.php` |
| FR-032 | Student views released grades and feedback | `GradeController::release` → `SubmissionController::show` | `grades.release`, `submissions.show` | `tests/Feature/Submission/GradingTest.php` |
| FR-033 | Grade release timestamps recorded | `ReleaseGrade` (released_at + audit_logs) | `grades.release` | `tests/Feature/Submission/GradingTest.php` |

---

## FR-034–FR-037 · Notifications

| FR | Description | Action | Mechanism | Test |
|---|---|---|---|---|
| FR-034 | Notify students about new announcements | `PublishAnnouncement` → `AnnouncementPublishedNotification` | Laravel Horizon queued job | `tests/Feature/Announcement/AnnouncementManagementTest.php` |
| FR-035 | Notify instructors about new submissions | `SubmitAssignment` → `NewSubmissionNotification` | Laravel Horizon queued job | `tests/Feature/Submission/SubmissionTest.php` |
| FR-036 | Notify students when grades released | `ReleaseGrade` → `GradeReleasedNotification` | Laravel Horizon queued job | `tests/Feature/Submission/GradingTest.php` |
| FR-037 | Deadline reminder notifications | `SendDeadlineReminders` console command (scheduled) | `app/Console/Commands/SendDeadlineReminders.php` | — |

---

## FR-038–FR-040 · Reporting and Export

| FR | Description | Action | Route | Test |
|---|---|---|---|---|
| FR-038 | Export submission records as CSV | `ExportSubmissions` (`app/Domain/Report/Actions/`) | `assignments.submissions.export` | `tests/Feature/Admin/ReportTest.php` |
| FR-039 | Admin views summary reports | `GetAdminReport` (`app/Domain/Report/Actions/`) | `admin.reports.index` | `tests/Feature/Admin/ReportTest.php` |
| FR-040 | Basic activity metrics | `GetAdminReport` (submission counts, late flag aggregates) | `admin.reports.index` | `tests/Feature/Admin/ReportTest.php` |

---

## FR-041–FR-045 · Accessibility Support

| FR | Description | Implementation | Route | Test |
|---|---|---|---|---|
| FR-041 | Adjustable font sizes | `UserPreference.font_size` → CSS custom property | `preferences.update` | `tests/Feature/Accessibility/PreferencesTest.php` |
| FR-042 | High-contrast mode | `UserPreference.high_contrast` → `data-high-contrast` attribute | `preferences.update` | `tests/Feature/Accessibility/PreferencesTest.php` |
| FR-043 | Keyboard-only navigation | Semantic HTML, visible focus rings, skip-link in `AuthenticatedLayout` | all pages | `resources/js/tests/Pages/**/*.spec.ts` (vitest-axe) |
| FR-044 | Readable validation and error messages | `aria-describedby` on all form inputs, `role="alert"` on errors | all form routes | `resources/js/tests/Pages/Auth/Register.spec.ts` |
| FR-045 | Store accessibility preferences for future sessions | `UpdatePreferences`, `UserPreference` model | `preferences.update` | `tests/Feature/Accessibility/PreferencesTest.php` |

---

## FR-046–FR-048 · Audit and Logging

| FR | Description | Implementation | Trigger |
|---|---|---|---|
| FR-046 | Log grade modification events | `GradeSubmission` → `audit_logs` (`grade.created`, `grade.updated`) | Every grade create/update |
| FR-047 | Log administrative actions on course visibility / user roles | `UpdateCourse` (status change) → `audit_logs` | Course status changes |
| FR-048 | Maintain audit records for sensitive actions | `ReleaseGrade`, `EnrollStudent`, `UnenrollStudent` → `audit_logs` | Grade release, enrollment changes |

---

## Coverage Summary

| Domain | FRs | Routes Implemented | Actions Implemented | Tests |
|---|---|---|---|---|
| Auth | FR-001–005 | 8 (Breeze) | Breeze built-in | 5 test classes |
| Profile | FR-006–007 | 3 | 1 custom Action | 2 test classes |
| Courses | FR-008–011 | 14 | 5 Actions | 2 test classes |
| Modules/Lessons | FR-012–016 | 18 | 7 Actions | 2 test classes |
| Announcements | FR-017–019 | 7 | 3 Actions | 1 test class |
| Assignments | FR-020–023 | 7 | 4 Actions | 2 test classes |
| Submissions | FR-024–029 | 5 | 1 Action | 1 test class |
| Grading | FR-030–033 | 3 | 2 Actions | 1 test class |
| Notifications | FR-034–037 | — | Queued Notifications | Covered via action tests |
| Reports | FR-038–040 | 2 | 2 Actions | 1 test class |
| Accessibility | FR-041–045 | 2 | 1 Action | 1 PHP + 34 Vue specs |
| Audit Logs | FR-046–048 | — | Inline in Actions | Covered via grading tests |
| **Total** | **48** | **69** | **27** | **297 PHP + 181 Vitest** |
