# Eduno LMS

> A web-based Learning Management System for the University of Caloocan City (UCC), built as an academic case study for CCS 123: Introduction to Human-Computer Interaction.

## Table of Contents

- [Project Overview](#project-overview)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Prerequisites](#prerequisites)
- [Local Development](#local-development)
- [Quality Gates](#quality-gates)
- [Testing](#testing)
- [Project Structure](#project-structure)
- [Functional Requirements](#functional-requirements)
- [Compliance](#compliance)
- [Branching Strategy](#branching-strategy)
- [License](#license)

---

## Project Overview

Eduno is a full-featured LMS that supports three user roles:

| Role | Responsibilities |
|---|---|
| **Admin** | Manage users, courses, reports, audit logs |
| **Instructor** | Create courses, modules, lessons, assignments; grade submissions |
| **Student** | Enroll in courses, submit assignments, view grades |

Key features include role-specific dashboards, module/lesson publishing workflows, file-upload submissions, graded assignments with feedback, real-time notifications, accessibility preference management (WCAG 2.2 AA), offline resource caching, and audit logging for all grade changes.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 |
| Frontend | Vue 3 + Inertia.js |
| Styling | Tailwind CSS v4 + Vite |
| Database | PostgreSQL |
| Cache / Queue | Redis |
| Auth | Laravel Breeze (Vue + SSR + TypeScript) |
| Queue Monitor | Laravel Horizon |
| Feature Flags | Laravel Pennant |
| Real-time (optional) | Laravel Reverb |
| Architecture | Modular Monolith, domain-organized |

---

## Architecture

**Request flow:** `Route → Controller → Action → Inertia Response`

- **Action pattern** — single-purpose, imperative-named classes (`CreateCourse`, `GradeSubmission`, `EnrollStudent`). No business logic in controllers.
- **Policies** — every model operation calls `$this->authorize()`. Role checks are never inlined in controllers.
- **Form Requests** — all validation is declared in dedicated Form Request classes.
- **DB Transactions** — grading, submission, and enrollment workflows use database transactions.
- **Domain structure** — `app/` is organized by domain (`Course`, `Submission`, `Grade`, `User`, etc.) rather than by framework layer.

---

## Prerequisites

| Dependency | Minimum version |
|---|---|
| PHP | 8.4 |
| Node.js | 24 |
| PostgreSQL | 16 |
| Redis | 7 |
| Composer | 2.x |

---

## Local Development

```bash
# 1. Copy environment file and configure DB/Redis credentials
cp .env.example .env

# 2. Install PHP dependencies
composer install

# 3. Generate application key
php artisan key:generate

# 4. Run migrations and seeders
php artisan migrate --seed

# 5. Install Node dependencies
npm ci

# 6. Start the development server
npm run dev
# In a separate terminal:
php artisan serve
```

For queue processing (notifications, etc.):

```bash
php artisan horizon
```

---

## Quality Gates

Every change must pass all gates before committing. Husky enforces `lint-staged` + `type-check` on pre-commit and `commitlint` on commit-msg automatically.

| Gate | Command | Scope |
|---|---|---|
| PHP formatting | `./vendor/bin/pint` | `app/`, `database/`, `routes/` |
| PHP static analysis | `./vendor/bin/phpstan analyse --level=5` | `app/` |
| PHP tests | `php artisan test` | `tests/Feature/`, `tests/Unit/` |
| TS type check | `npm run type-check` | `resources/js/` |
| JS/Vue linting | `npm run lint` | `resources/js/` |
| Formatting | `npm run format -- --check` | `resources/js/` |
| JS build | `npm run build` | Ensures no compilation errors |

---

## Testing

### PHP — Pest

```bash
php artisan test                   # all tests
php artisan test --filter CourseTest   # single suite
```

- Feature tests live in `tests/Feature/` and cover every route (happy path + auth failure).
- Unit tests live in `tests/Unit/` and cover every Action class.

### Vue — Vitest + vitest-axe

```bash
npm run test                       # all specs
npm run test -- --reporter=verbose
```

- Component specs use `@vue/test-utils` + `vitest-axe` for automated WCAG checks (`toHaveNoViolations()`).
- Composable unit specs cover `useFormatDate`, `useFileSize`, `useOfflineCache`, and `useA11yPrefs`.
- Page specs cover `Course/Index`, `Assignment/Index`, and `Submission/Show`.

### E2E — Playwright (planned)

Full end-to-end flows (login, enroll, submit, grade) are tracked as a future milestone.

---

## Project Structure

```
eduno/
├── app/
│   ├── Actions/          # Single-purpose action classes
│   ├── Http/
│   │   ├── Controllers/  # Thin controllers — delegate to Actions
│   │   ├── Requests/     # Form Request validation classes
│   │   └── Middleware/
│   ├── Models/           # Eloquent models with $fillable declared
│   └── Policies/         # Authorization policies
├── database/
│   ├── migrations/       # 16 migration files — full data model
│   └── seeders/
├── resources/
│   └── js/
│       ├── Components/   # Reusable Vue components
│       ├── composables/  # Vue composables (useFormatDate, useA11yPrefs, …)
│       ├── Layouts/      # AuthenticatedLayout, GuestLayout
│       ├── Pages/        # Inertia page components
│       ├── tests/        # Vitest specs (Components/, Pages/, composables/)
│       └── Types/        # Shared TypeScript model types
├── routes/
│   └── web.php           # All application routes
├── tests/
│   ├── Feature/          # Pest feature tests
│   └── Unit/             # Pest unit tests
├── eduno_srs.md          # 48 Functional Requirements
├── eduno_system_design.md # Domain names, DB schema, module responsibilities
└── CLAUDE.md             # AI governing instructions
```

---

## Functional Requirements

48 Functional Requirements (FR-001 – FR-048) are tracked in [`eduno_srs.md`](eduno_srs.md). Every feature must trace to at least one FR. Key areas include:

- **Auth & Users** (FR-001–006): registration, login, role assignment, password reset
- **Courses & Sections** (FR-007–014): CRUD, enrollment, publishing
- **Modules & Lessons** (FR-015–020): ordering, publish/unpublish, content types
- **Assignments & Submissions** (FR-021–030): deadlines, file upload, late flags, attempt limits
- **Grading** (FR-031–036): scoring, feedback, release to student, audit log
- **Notifications** (FR-037–040): deadline reminders, grade release alerts
- **Reports & Admin** (FR-041–048): course completion, grade distribution, audit log viewer

---

## Compliance

### WCAG 2.2 — Level AA

All UI components meet the following criteria at minimum:

- **1.1.1** Non-text Content — descriptive `alt` on all images
- **1.3.1** Info and Relationships — semantic HTML (`<nav>`, `<main>`, `<header>`, `<table>`)
- **1.4.3** Contrast — normal text ≥ 4.5:1 ratio
- **1.4.10** Reflow — no horizontal scroll at 320 px viewport
- **2.1.1** Keyboard — all functionality keyboard-operable
- **2.4.7** Focus Visible — focus indicators always visible; never `outline: none` without replacement
- **3.3.1** Error Identification — error messages identify the specific field
- **3.3.2** Labels or Instructions — all inputs have an associated `<label>`
- **4.1.2** Name, Role, Value — custom components expose ARIA name, role, and state
- **4.1.3** Status Messages — live regions use `role="status"` or `aria-live`

Automated WCAG checks run via `vitest-axe` (`toHaveNoViolations()`) on every component spec.

### OWASP Top 10 (2021)

| Risk | Mitigation |
|---|---|
| A01 Broken Access Control | Policies + `$this->authorize()` before every model operation |
| A02 Cryptographic Failures | `Hash::make()` for all passwords |
| A03 Injection | Eloquent ORM only — no raw SQL string concatenation |
| A05 Security Misconfiguration | `APP_DEBUG=false` in production; `.env` gitignored |
| A07 Authentication Failures | `throttle:6,1` on `/login` and `/register` |
| A09 Logging & Monitoring | `audit_logs` table + Laravel Log for all grade/admin changes |

### NIST SSDF

- **PW.5** — All inputs validated via Form Request classes; file uploads validate MIME type + max size
- **PS.1** — `.env` is gitignored; no secrets in source code
- **RV.1** — Run `composer audit` and `npm audit` before every release

### ISO/IEC 25010

| Characteristic | Implementation |
|---|---|
| Functional Suitability | Every feature traces to `eduno_srs.md` |
| Reliability | DB transactions for grading and submission workflows |
| Performance Efficiency | Redis cache for dashboard aggregates; paginated lists; queued slow operations |
| Maintainability | PSR-12, Action pattern, PHPStan level 5 |
| Security | See OWASP section above |
| Usability / Accessibility | See WCAG 2.2 AA section above |

---

## Branching Strategy

```
main       ← always deployable; requires passing CI + PR review
develop    ← integration branch; feature branches merge here first

feat/<scope>/<description>   e.g. feat/course/create-course-action
fix/<scope>/<description>    e.g. fix/submission/late-flag-timezone
a11y/<description>           e.g. a11y/focus-trap-modal
chore/<description>          e.g. chore/add-eslint-config
release/<semver>             e.g. release/1.0.0  (develop → main)
```

Rules:
- Never commit directly to `main` or `develop`
- All branches cut from `develop`; PR back to `develop`
- `develop` → `main` via release PR only
- CI must pass before merging any PR
- Squash merge preferred for `feat/*` branches

---

## License

Academic project — University of Caloocan City, CCS 123: Introduction to Human-Computer Interaction. Not licensed for production use.
