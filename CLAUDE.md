# CLAUDE.md — Eduno LMS

Governing AI instructions for Claude Code on the Eduno project.

---

## Project Identity

**Eduno** is a web-based Learning Management System for the University of Caloocan City (UCC), built as an academic case study for CCS 123: Introduction to Human-Computer Interaction.

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

## Architecture Principles

- **Controllers → Actions → Inertia responses.** No business logic in controllers.
- **Action pattern:** Single-purpose, imperative-named classes (`CreateCourse`, `GradeSubmission`, `EnrollStudent`).
- **Always authorize:** Call `$this->authorize()` before every model operation. Use Policies — never inline role checks in controllers.
- **Form Requests for all validation.** Never validate in controllers directly.
- **Config via `config()` helper only.** Never call `env()` in application code outside of config files.
- **DB transactions for multi-table writes** (grading, submissions, enrollments).

---

## Standards & Compliance

### ISO/IEC 25010 Quality Characteristics

| Characteristic | Project Requirement |
|---|---|
| Functional Suitability | Every feature must trace to a Functional Requirement in `eduno_srs.md` |
| Reliability | Use DB transactions for grading and submission workflows |
| Performance Efficiency | Cache dashboard aggregates (Redis), paginate all lists, queue slow operations |
| Maintainability | PSR-12 coding standard, Action pattern, short focused methods |
| Security | See OWASP section below |
| Usability / Accessibility | See WCAG 2.2 section below |

### OWASP Top 10 (2021) Mitigations

| Risk | Laravel Mitigation |
|---|---|
| A01 Broken Access Control | Policies + `$this->authorize()` before every model operation |
| A02 Cryptographic Failures | `Hash::make()` for all passwords; never store plaintext |
| A03 Injection | Eloquent ORM only; never concatenate raw SQL strings |
| A05 Security Misconfiguration | Never commit `.env`; set `APP_DEBUG=false` in production |
| A07 Authentication Failures | Rate-limit `/login` and `/register` with `throttle:6,1` middleware |
| A09 Logging & Monitoring Failures | `audit_logs` table + Laravel Log for all grade changes and admin actions |

### NIST SSDF (Secure Software Development Framework)

- **PW.5 — Input Validation:** All inputs validated via Form Request classes. File uploads must validate MIME type and max size.
- **PS.1 — Protect Secrets:** `.env` is gitignored; no secrets or credentials in source code.
- **RV.1 — Vulnerability Identification:** Run `composer audit` and `npm audit` before every release.

### WCAG 2.2 — Level AA (Minimum)

| Criterion | Requirement |
|---|---|
| 1.1.1 Non-text Content | All `<img>` elements must have a descriptive `alt` attribute |
| 1.3.1 Info and Relationships | Use semantic HTML: `<nav>`, `<main>`, `<header>`, `<button>`, `<table>`, etc. |
| 1.4.3 Contrast (Minimum) | Normal text: minimum 4.5:1 contrast ratio |
| 1.4.10 Reflow | No horizontal scroll at 320px viewport width |
| 2.1.1 Keyboard | All functionality must be keyboard-operable |
| 2.4.7 Focus Visible | Focus indicators must always be visible — never `outline: none` without a replacement |
| 3.3.1 Error Identification | Error messages must identify the specific field that caused the error |
| 3.3.2 Labels or Instructions | All form inputs must have an associated `<label>` element |
| 4.1.2 Name, Role, Value | Custom interactive components must expose ARIA name, role, and state |
| 4.1.3 Status Messages | Status messages must use `role="status"` or `aria-live` to be announced without focus |

---

## PHP Guidelines

- **PSR-12** coding standard everywhere.
- **`declare(strict_types=1)`** at the top of every PHP file.
- Run Pint before committing: `./vendor/bin/pint`
- PHPStan level 5 minimum: `./vendor/bin/phpstan analyse`
- All properties, parameters, and return types must be explicitly typed.
- **Models:** always declare `$fillable`; never use `$guarded = []`.
- **Migrations:** always implement `down()`. Add foreign key constraints and indexes.
- **File uploads:** validate MIME type + max size, generate UUID filenames, store in private disk, serve via signed URLs.
- **Never call `env()` in application code** — only in `config/` files.

---

## Frontend Guidelines

- **Vue 3 Composition API only** (`<script setup lang="ts">`). Options API is forbidden.
- **TypeScript strict mode.** Use `defineProps<{}>()` and `defineEmits<{}>()`. No `any` types.
- Shared model types live in `resources/js/Types/models.ts`.
- **Accessibility:**
  - Semantic HTML first.
  - Icon-only buttons must have `aria-label`.
  - Error messages must use `aria-describedby` linking to the input.
  - Dynamic messages must use `role="alert"` or `aria-live`.
  - Modals must trap focus.
- **Never use `outline: none`** without a visible replacement focus style.
- **Tailwind v4:** use `@import "tailwindcss"` in `resources/css/app.css`.
- **Inertia forms:** always use `useForm()` — never raw `fetch` or `axios` for form submissions.

---

## Code Quality Scripts

Add to `package.json` scripts:

```json
{
  "dev": "vite",
  "build": "vite build && vite build --ssr",
  "type-check": "vue-tsc --noEmit",
  "lint": "eslint resources/js --ext .ts,.vue",
  "format": "prettier --write resources/js",
  "test": "vitest"
}
```

---

## Git Conventions (Conventional Commits)

**Format:** `<type>(<scope>): <description>`

**Types:** `feat`, `fix`, `docs`, `refactor`, `test`, `chore`, `a11y`, `sec`, `perf`

**Scopes:** `auth`, `course`, `module`, `assignment`, `submission`, `grade`, `announcement`, `notification`, `report`, `accessibility`, `admin`, `config`, `migration`

**Branch naming examples:**
- `feat/submission-file-upload`
- `fix/grade-release-timestamp`
- `a11y/focus-trap-modal`

**Rules:**
- `main` must always be deployable.
- Never force-push to `main`.

---

## Testing Strategy

- **PHP:** Pest PHP — feature tests in `tests/Feature/`, unit tests in `tests/Unit/`.
- Every Action class must have a corresponding unit test.
- Every route must have a feature test covering the happy path and auth failure.
- **Vue:** Vitest + `@vue/test-utils` for component tests.
- Use `vitest-axe` for automated WCAG checks on Vue components.

---

## Critical Reference Files

| File | Purpose |
|---|---|
| `eduno_srs.md` | 48 Functional Requirements — every feature must trace back here |
| `eduno_system_design.md` | Authoritative domain names, DB schema, module responsibilities |
| `CLAUDE.md` | This file — governing AI instructions |
| `.gitignore` | Must exist and be correct before first commit |
| `database/migrations/` | 16 migration files defining the full data model |
