# UI/UX Design Rationale

**Project:** Eduno LMS
**Version:** 1.0.0
**Date:** 2026-03-20
**Context:** CCS 123 — Introduction to Human–Computer Interaction

This document explains the reasoning behind the visual design, interaction patterns, and
information architecture decisions made in Eduno LMS.

---

## 1. Brand Identity

### 1.1 Primary Color — Indigo 600 (`#4f46e5`)

Indigo was chosen as the primary brand color for three reasons:

1. **Academic association.** Indigo sits between blue (trust, stability) and violet (creativity,
   learning). This makes it appropriate for an educational institution without feeling corporate
   or cold.
2. **Contrast compliance.** White text on `#4f46e5` achieves a 4.73:1 contrast ratio — above
   the WCAG 2.2 AA minimum of 4.5:1 for normal text.
3. **Differentiation.** UCC's existing systems (Google Classroom, AIMS) use blue and green
   palettes. Indigo makes Eduno visually distinct while remaining in the same trusted family.

The same color appears consistently as:

- The logo background (stylized "E" in a rounded square)
- Active navigation link underline (`border-indigo-400`)
- Focus rings on all interactive elements (`ring-indigo-500`)
- Primary action buttons
- PWA theme color (status bar on mobile)

### 1.2 Typography — Figtree

Figtree is a geometric sans-serif typeface loaded from Bunny Fonts (a GDPR-compliant
alternative to Google Fonts). It was chosen because:

- **Legibility at small sizes.** Form labels and data tables often render at 12–14px.
  Figtree's open apertures and generous x-height maintain legibility at these sizes.
- **Academic register.** Geometric fonts read as modern and professional without being
  aggressive.
- **Three weights available (400/500/600).** This provides sufficient hierarchy
  (body / medium / semibold) without requiring additional requests.
- **Dyslexia fallback.** When a user enables the dyslexia font preference, OpenDyslexic
  replaces Figtree entirely via the `.dyslexia-font` CSS class, which uses `!important`
  to guarantee the override.

### 1.3 Background and Surface Colors

| Surface | Color | Rationale |
|---|---|---|
| Page background | `gray-100` | Creates visible depth between the page and cards |
| Card / panel | `white` | Primary reading surface; maximum contrast for text |
| Muted text | `gray-500` | Secondary information without competing with primary text |
| Disabled / placeholder | `gray-400` | Visually distinct from active content |

---

## 2. Layout and Information Architecture

### 2.1 Three-Level Hierarchy

Eduno's content is organized into three levels that map directly to academic structure:

```
Course
 └── Section (e.g., "BSCS-2A, MWF 9:00")
      ├── Modules
      │    └── Lessons
      │         └── Resources
      ├── Assignments
      │    └── Submissions → Grades
      └── Announcements
```

This hierarchy is surfaced through:
- **Breadcrumb navigation** — shows the path from Course → Section → current page
- **URL structure** — `/courses/{id}/sections/{id}/modules/{id}/lessons/{id}`
- **Breadcrumb ARIA** — `aria-current="page"` on the final crumb; separator `/` is `aria-hidden`

### 2.2 Max-Width Container (`max-w-7xl`)

All content is constrained to 1280px (Tailwind's `7xl`) with responsive horizontal padding
(`px-4 sm:px-6 lg:px-8`). This decision:

- Prevents line lengths from becoming uncomfortably long on widescreen monitors
- Keeps the layout usable on tablets (768px) and phones (360px–430px) without reflow
- Aligns with WCAG 1.4.10 (Reflow) — no horizontal scrolling at 320px viewport width

### 2.3 Card Pattern

All content sections are presented as white cards with `shadow-sm` and `sm:rounded-lg`.
Cards only round on `sm:` breakpoint (640px+) — on mobile they extend edge-to-edge to
maximize reading area on small screens.

### 2.4 Responsive Navigation

The navigation bar uses a desktop/mobile split:

| Breakpoint | Pattern |
|---|---|
| Mobile (`< 640px`) | Hamburger menu; full-width stacked links below nav bar |
| Desktop (`≥ 640px`) | Horizontal nav links with bottom-border active indicator |

The hamburger button meets the 44×44px minimum touch target requirement (WCAG 2.5.5). Its
ARIA attributes (`aria-expanded`, `aria-controls`, `aria-label`) are updated reactively so
screen readers announce the open/closed state.

---

## 3. Interaction Patterns

### 3.1 Status Feedback

Every meaningful action has an immediate visual response:

| Action | Feedback |
|---|---|
| File submitted | Submission status page (Inertia redirect) with `role="alert"` confirmation |
| Grade released | Status changes to "Returned"; student receives notification |
| Assignment published | Status badge changes from "Draft" to "Published" |
| Module / Lesson published | Published state reflected immediately in breadcrumb and list |
| Late submission | `is_late` flag set server-side and surfaced as a red "Late" badge |

The `StatusBadge` component encodes three semantic states through both color and text:
- **Draft** — yellow pill: work in progress, not visible to students
- **Published** — green pill: live and accessible
- **Late** — red pill: submitted after due date

Color is never the *only* differentiator (WCAG 1.4.1 — Use of Color) — the text label is
always present alongside the color.

### 3.2 Urgency-Coded Deadlines

The `DeadlineItem` component on the Dashboard uses a three-tier urgency system based on hours
until due:

| Hours until due | Border + background | Urgency label |
|---|---|---|
| ≤ 24 hours | `red-400` / `red-50` | "Due very soon" |
| ≤ 72 hours | `amber-400` / `amber-50` | "Due soon" |
| > 72 hours | `green-400` / `green-50` | "Upcoming" |

The full context is encoded in `aria-label`:
> "Assignment Title — CCS 123, due Mon, Mar 24, 02:30 PM. Due very soon."

This ensures screen reader users receive the same urgency signal as sighted users who see
the red border.

### 3.3 Empty States

Every list has an explicit empty state rendered via `EmptyState.vue` — a dashed-border card
with `role="status"` and a human-readable message (e.g., "No upcoming deadlines in the
next 7 days. Great work!"). This prevents users from wondering if the page failed to load.

### 3.4 Dropdown Menus

The `Dropdown` component implements three keyboard behaviors required for usable menus:
1. **Click to open** — standard pointer interaction
2. **Escape to close** — keyboard dismiss, focus returns to trigger button via `nextTick`
3. **Click-outside to close** — a full-screen transparent overlay (z-40) captures outside
   clicks without blocking other page content

### 3.5 Pagination

`Pagination.vue` renders Laravel's paginator links with:
- `<nav aria-label="Pagination">` wrapping element
- `aria-current="page"` on the active page link
- Active link: indigo-600 background, white text (high contrast)
- Disabled links (first/last when at boundary): rendered as `<span>`, not `<a>` (not focusable)

---

## 4. Form Design

All forms follow a consistent pattern derived from WCAG 3.3 (Input Assistance):

```
[InputLabel]    ← always visible, linked by for/id
[TextInput]     ← aria-describedby pointing to InputError
[InputError]    ← role="alert", only rendered when error exists
```

### 4.1 No Placeholder-Only Labels

Placeholders disappear on focus, leaving users who forget the field purpose with no hint.
Every input has a persistent `<label>` above it. Placeholders, if used, are supplementary.

### 4.2 Error Messages

Errors are:
- **Field-specific** — never generic "something went wrong"
- **Announced immediately** via `role="alert"` on `InputError.vue`
- **Linked via `aria-describedby`** so screen readers announce the error when the field
  receives focus
- **Sourced from Laravel Form Requests** — validation is server-authoritative, never
  duplicated in the frontend

### 4.3 `useForm()` for All Submissions

All forms use Inertia's `useForm()` composable — never raw `fetch` or `axios`. This
provides:
- Automatic CSRF token handling
- Reactive `processing` state (disables submit button while in-flight)
- Automatic error binding from Inertia's shared error bag

---

## 5. Accessibility Design

### 5.1 Skip-to-Content Link

Both `AuthenticatedLayout` and `GuestLayout` begin with a visually hidden skip link:

```html
<a href="#main-content" class="sr-only focus:not-sr-only ...">
  Skip to main content
</a>
```

On focus (Tab key), it becomes visible as an indigo-600 button in the top-left corner.
The target `id="main-content"` is on the `<main>` element.

### 5.2 Focus Ring Design

Focus rings use `focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2`
throughout. `focus-visible` ensures rings appear only for keyboard navigation (not mouse
clicks), which is less visually disruptive for pointer users while still fully accessible
for keyboard users.

`outline: none` is never used without a replacement focus style.

### 5.3 User Accessibility Preferences

Users can enable four preferences from their profile settings page, stored in
`user_preferences` and applied via `useA11yPrefs()` on every page load:

| Preference | Mechanism | CSS effect |
|---|---|---|
| Dyslexia font | `.dyslexia-font` class on `<html>` | OpenDyslexic font stack (`!important`) |
| Reduced motion | `.reduced-motion` class on `<html>` | All `transition` and `animation` removed |
| High contrast | `.high-contrast` class on `<html>` | `filter: contrast(1.5)` applied |
| Font size | `data-fontSize` attribute on `<html>` | small / medium / large / extra-large |

### 5.4 Minimum Touch Targets

All interactive elements meet the 44×44px minimum:
- Navigation hamburger: `min-h-[44px] min-w-[44px]`
- User settings dropdown trigger: `min-h-[44px]`
- Pagination links: `px-3 py-1` (32px height — acceptable for secondary controls)

---

## 6. Progressive Web App (PWA)

Eduno ships as an installable PWA to address the connectivity and device constraints of
UCC students (many are on mobile data; some have low-end Android phones).

### 6.1 Offline Caching Strategy

| Resource type | Strategy | Expiry |
|---|---|---|
| JS, CSS, fonts (Bunny Fonts) | Cache First | 1 year (immutable hashes) |
| Course resources (PDF, DOCX, PPTX) | Cache First | 7 days, max 50 items |
| API calls | Network First | 5s timeout, then cache fallback |
| HTML navigation | Not intercepted (`navigateFallback: null`) | Inertia SSR controls routing |

The `navigateFallback: null` setting is critical — intercepting HTML navigations would
break Inertia's server-side rendering model, causing incorrect page renders.

### 6.2 App Manifest

The PWA manifest defines:
- **Theme color**: `#4f46e5` — colors the Android status bar to match the brand
- **Display**: `standalone` — hides browser chrome for an app-like experience
- **Orientation**: `portrait` — optimal for reading and form completion on phones
- **Scope**: `/` — the entire application is installable as a single PWA

---

## 7. Design Decisions Summary

| Decision | Rationale |
|---|---|
| Indigo-600 as brand color | Academic trust + WCAG AA contrast + differentiation from existing UCC tools |
| Figtree typeface | Legibility at small sizes; supports 3 weights for hierarchy |
| Card pattern with `shadow-sm` | Creates depth without heavy borders; clean academic aesthetic |
| `max-w-7xl` container | Prevents overlong lines on wide screens; reflows correctly at 320px |
| Server-side `is_late` flag | Immutable, tamper-proof late detection; avoids client clock manipulation |
| `released_at` grade holding | Instructor controls when students see grades — prevents premature access |
| `role="alert"` on errors | Immediate announcement on submission errors without requiring focus move |
| `sr-only` skip link | Keyboard users bypass nav without disrupting pointer users' view |
| PWA with `navigateFallback: null` | Offline access without breaking Inertia SSR routing |
| `@tailwindcss/forms` plugin | Normalizes cross-browser form appearance without custom CSS |
