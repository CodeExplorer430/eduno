# Changelog

All notable changes to Eduno LMS are documented here.

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
