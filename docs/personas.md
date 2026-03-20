# User Personas

**Project:** Eduno LMS
**Version:** 1.0.0
**Date:** 2026-03-20
**Context:** CCS 123 — Introduction to Human–Computer Interaction

These personas represent the three primary user roles of Eduno LMS at the University of
Caloocan City (UCC). Each persona is grounded in the academic environment of UCC and the
specific workflows implemented in the system.

---

## Persona 1 — The Student

### Alyssa Mendoza
**Role:** Student (BS Computer Science, 2nd Year)
**Age:** 19
**Location:** Caloocan City, Metro Manila

---

### Background

Alyssa commutes to UCC from Valenzuela every day, spending about 1.5 hours each way on
jeepney and UV Express. She is enrolled in five courses this semester and juggles part-time
tutoring on weekends. She primarily uses her Android phone for checking messages and school
updates, and uses her shared home laptop for submitting assignments.

---

### Goals

- Know exactly what assignments are due and when, without checking multiple platforms
- Submit files confidently and get a clear confirmation that the upload went through
- View released grades and instructor feedback as soon as they are available
- Access course materials (modules, lessons, resources) even with an unstable connection

---

### Frustrations

- "I have no idea if my submission actually went through — the platform just reloads."
- "Due dates are everywhere — Messenger, Google Classroom, email — I always miss one."
- "I can't access lecture notes when I'm on mobile data and the connection cuts out."
- "I submitted the wrong file once and couldn't replace it. The instructor never knew."

---

### Behaviors

| Behavior | Detail |
|---|---|
| Device | Primarily Android mobile; laptop only for file uploads |
| Session timing | Checks notifications after class (5–7 PM), submits Sunday nights |
| Platform habit | Expects WhatsApp/Messenger-like read receipts for submissions |
| Connectivity | Frequently on mobile data (sometimes 3G), intermittent Wi-Fi at school |

---

### Tech Proficiency

```
Low  ───────────────────────────── High
                         ●
                         (Comfortable with apps, less so with web platforms)
```

---

### Needs from Eduno

| Need | Implemented Feature |
|---|---|
| See upcoming deadlines at a glance | "What's Next?" dashboard widget (color-coded urgency) |
| Receive a clear submission confirmation | Submission status page with `role="alert"` confirmation |
| Access materials offline | Service worker caches PDFs, DOCX, PPTX for 7 days |
| Be notified when grades are released | `GradeReleasedNotification` via Laravel Horizon queue |
| Navigate with one hand on mobile | Responsive nav, 44px minimum touch targets |

---

### Quote

> "I just want to know: did my file go through? And when is my next deadline? That's it."

---

---

## Persona 2 — The Instructor

### Mr. Rafael Bautista
**Role:** Instructor (Full-time Faculty, CCS Department)
**Age:** 34
**Location:** Quezon City (commutes to UCC Caloocan campus)

---

### Background

Sir Rafael teaches three subjects this semester with a combined enrolment of around 120
students across six sections. He prepares lecture slides in PowerPoint and typed instructions
in Word. He grades on weekends and during free periods. He is active on Messenger and uses
it daily to send announcements to class group chats — a process he finds exhausting to manage
across dozens of groups.

---

### Goals

- Post announcements once and have all enrolled students notified automatically
- Grade submissions in a structured queue, not scattered email attachments
- Track which students are late or have not yet submitted for each assignment
- Release grades to students only after reviewing all submissions

---

### Frustrations

- "I post the same announcement in 6 different Messenger groups. It's exhausting."
- "Students email me files directly — I lose track of who submitted what."
- "Some students claim they submitted on time, but I have no timestamp to verify."
- "I want to hold grades until I've finished marking everything before students see them."

---

### Behaviors

| Behavior | Detail |
|---|---|
| Device | Laptop (Windows) as primary; phone for quick checks |
| Session timing | Grades on Saturday mornings; checks submissions during free periods |
| Communication | Prefers announcements over individual emails |
| Grading style | Batch-grades all submissions before releasing any |

---

### Tech Proficiency

```
Low  ───────────────────────────── High
             ●
             (Comfortable with Office apps; less experienced with web platforms)
```

---

### Needs from Eduno

| Need | Implemented Feature |
|---|---|
| Post announcements to all enrolled students at once | `CreateAnnouncement` + `PublishAnnouncement` action; queued notifications |
| See submissions in a structured list with timestamps | `SubmissionController::index` — table with `submitted_at`, `is_late` flag |
| Verify late submissions objectively | `is_late` computed on server from `due_at` vs `submitted_at` |
| Hold grades until ready to release | Grades created with `released_at = null`; explicit `ReleaseGrade` action |
| Export submission records | CSV export via `ExportSubmissions` action |

---

### Quote

> "I want one place to post, grade, and release. Not six Messenger groups and an inbox full of attachments."

---

---

## Persona 3 — The Administrator

### Ms. Cynthia Reyes
**Role:** Academic Administrator (IT Services / Registrar's Office)
**Age:** 41
**Location:** Caloocan City

---

### Background

Ma'am Cynthia oversees the IT systems used by the CCS department and coordinates with
faculty and department heads. She is not a developer but manages user accounts, monitors
system activity, and prepares reports for department meetings. She is meticulous about
record-keeping and is the first person called when "something is wrong with the system."

---

### Goals

- Manage user accounts (create, deactivate) without needing a developer
- View system-wide reports — how many submissions, how many late, per course
- Audit any grade changes or administrative actions for accountability
- Ensure the system is accessible to all students, including those with disabilities

---

### Frustrations

- "I need IT to run a report for me. I should be able to do it myself."
- "If a grade is disputed, I have no way to verify when it was changed or by whom."
- "Students with disabilities ask for accommodations but the system has no settings for them."
- "I can't tell which courses are active and which have no activity this semester."

---

### Behaviors

| Behavior | Detail |
|---|---|
| Device | Desktop workstation (Windows, Chrome browser) |
| Session timing | Business hours, Mon–Fri |
| Reporting style | Exports to CSV, pastes into Excel for department reports |
| Risk tolerance | Very low — prefers reversible, confirmable actions |

---

### Tech Proficiency

```
Low  ───────────────────────────── High
          ●
          (Comfortable with spreadsheets; cautious with unfamiliar web platforms)
```

---

### Needs from Eduno

| Need | Implemented Feature |
|---|---|
| View system-wide submission and activity reports | `GetAdminReport` action → `Admin/Reports/Index` page |
| Export data for Excel | `ExportSubmissions` CSV download |
| Audit grade changes | `audit_logs` table — every `grade.created`, `grade.updated`, `grade.released` event logged with timestamp and actor |
| Manage user roles | Admin user management routes (FR-034–040) |
| Verify system accessibility | WCAG 2.2 AA compliance; user accessibility preferences stored per account |

---

### Quote

> "I need to know who changed that grade, when, and what it was before and after. That's just accountability."

---

---

## Persona Comparison Summary

| Attribute | Alyssa (Student) | Sir Rafael (Instructor) | Ma'am Cynthia (Admin) |
|---|---|---|---|
| Primary device | Android mobile + laptop | Windows laptop | Desktop (Chrome) |
| Key workflow | Submit → Check grade | Create → Grade → Release | Monitor → Report → Audit |
| Biggest pain point | Submission uncertainty | Fragmented communication | No audit trail |
| Tech proficiency | Medium | Low–Medium | Low–Medium |
| Connectivity | Intermittent mobile | Reliable office Wi-Fi | Reliable office LAN |
| Accessibility need | Large text (studies late) | N/A | High contrast (fluorescent lighting) |
| Session pattern | Evenings + weekends | Weekends + free periods | Business hours |
