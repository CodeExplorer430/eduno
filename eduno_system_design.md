# Eduno System Design Document

## 1. Document Control
- **Project Name:** Eduno
- **Project Type:** Web-based Learning Management System (LMS)
- **Prepared For:** CCS 123: Introduction to Human–Computer Interaction
- **Prepared By:** Miguel Harvey N. Velasco and Group
- **Version:** 1.0
- **Status:** Draft for Prototyping and Architecture Planning

---

## 2. System Overview

### 2.1 Purpose
Eduno is a dedicated, accessible, student-centered Learning Management System intended to serve as a separate academic delivery platform for the University of Caloocan City (UCC). It is designed to centralize course materials, assignments, announcements, submissions, and grading workflows that are currently fragmented across Google Classroom, Google Drive, Google Forms, and Messenger.

### 2.2 Problem Statement
UCC currently lacks its own dedicated LMS for daily academic delivery. Academic workflows are distributed across multiple third-party platforms, resulting in:
- inconsistent user experience,
- poor discoverability of learning materials,
- scattered communication channels,
- weak submission tracking,
- limited accessibility customization,
- dependency on external services.

The existing Academic Information Management System (AIMS) is intended for enrollment and administrative processes rather than day-to-day teaching and learning. Eduno is therefore proposed as a **separate LMS platform**, not a replacement for AIMS.

### 2.3 Goals
Eduno aims to:
- centralize learning content and student submissions,
- provide a consistent web experience for students and instructors,
- support accessibility-first UI/UX design,
- deliver modern and maintainable architecture,
- remain within realistic case study scope.

---

## 3. Recommended Tech Stack

## 3.1 Core Stack
- **Backend Framework:** Laravel 12
- **Frontend Framework:** Vue 3
- **Bridge Layer:** Inertia.js
- **Styling:** Tailwind CSS
- **Build Tool:** Vite
- **Database:** PostgreSQL
- **Cache / Session / Queue Backend:** Redis
- **Object/File Storage:** Local storage for development, S3-compatible storage for production/demo if available
- **Authentication:** Laravel starter auth scaffolding
- **Background Jobs:** Laravel Queue
- **Queue Monitoring:** Laravel Horizon
- **Task Scheduling:** Laravel Scheduler
- **Feature Flags:** Laravel Pennant
- **Optional Real-Time:** Laravel Reverb

## 3.2 Why This Stack Fits the Project
This VILT-based stack is recommended because:
- it keeps the backend and frontend in one consistent Laravel-centered architecture,
- it allows modern, responsive UI design with Vue,
- it avoids the overhead of managing separate backend and SPA APIs in early stages,
- it is maintainable for a single main developer,
- it supports accessibility-focused interface implementation effectively.

## 3.3 Why Not Microservices
A microservices architecture is not recommended because:
- the project is a case study with limited development time,
- only one primary engineer is responsible for implementation,
- the infrastructure overhead is unnecessary,
- deployment and debugging complexity would be too high.

The recommended architecture is a **modular monolith**.

---

## 4. Architectural Style

## 4.1 Modular Monolith
Eduno will be built as a modular monolith:
- one Laravel application,
- one PostgreSQL database,
- one Redis instance,
- one deployment unit,
- internal code organized into domain-based modules.

## 4.2 Core Modules
- Identity and Access Management
- User Profiles
- Courses
- Course Sections
- Modules and Lessons
- Resources
- Announcements
- Assignments
- Submissions
- Gradebook
- Notifications
- Accessibility Preferences
- Reports and Exports
- Audit Logs

---

## 5. High-Level Architecture

```text
[ Browser / Mobile Web Client ]
              |
              v
[ Laravel 12 Application ]
  - Routing
  - Controllers
  - Services
  - Policies
  - Jobs
  - Inertia Responses
              |
      +-------+--------+
      |                |
      v                v
[ PostgreSQL ]      [ Redis ]
  - users            - cache
  - courses          - queues
  - assignments      - sessions
  - submissions      - rate limiting
      |
      v
[ File Storage ]
  - course resources
  - assignment uploads
  - images
      |
      v
[ Email / Notification Provider ]
  - reminders
  - announcements
  - grading alerts
```

### Optional Future Layer
```text
[ Laravel Reverb ]
  - live notifications
  - real-time dashboards
```

---

## 6. User Roles and Responsibilities

## 6.1 Student
Students can:
- log in,
- view enrolled courses,
- read course materials,
- download resources,
- submit assignments,
- view grades and instructor feedback,
- receive announcements and deadline reminders,
- configure accessibility preferences.

## 6.2 Instructor
Instructors can:
- create and manage courses,
- create modules and lessons,
- upload resources,
- post announcements,
- create assignments,
- review submissions,
- provide grades and feedback,
- export course data.

## 6.3 Administrator
Administrators can:
- manage user accounts,
- manage course approvals or archives,
- inspect reports,
- access audit logs,
- manage platform-level settings.

---

## 7. Functional Module Design

## 7.1 Identity and Access
### Responsibilities
- login/logout
- password reset
- email verification
- role-based authorization
- session management

### Design Notes
- Use Laravel authentication scaffolding.
- Apply Laravel policies and gates for authorization.
- Keep access rules strict for grades, submissions, and course ownership.

## 7.2 Course Management
### Responsibilities
- create courses and sections,
- assign instructors,
- manage term and academic year metadata,
- maintain course status.

## 7.3 Modules and Lessons
### Responsibilities
- organize courses into units,
- support lesson sequencing,
- enable publish/unpublish control,
- display educational materials clearly.

## 7.4 Resources
### Responsibilities
- upload PDFs, slides, links, and downloadable files,
- store file metadata,
- provide secure access.

## 7.5 Announcements
### Responsibilities
- post and schedule announcements,
- notify enrolled students,
- pin important announcements.

## 7.6 Assignments
### Responsibilities
- define instructions and due dates,
- set submission requirements,
- configure max score and late policy,
- publish and archive assignments.

## 7.7 Submissions
### Responsibilities
- accept file uploads,
- validate file type and file size,
- track submission attempts,
- record timestamps,
- detect late submissions.

## 7.8 Gradebook
### Responsibilities
- input grades,
- release feedback,
- display grades to students,
- maintain grading audit trail.

## 7.9 Notifications
### Responsibilities
- alert students about deadlines,
- alert students about announcements,
- notify instructors about new submissions,
- notify students when grades are released.

## 7.10 Accessibility Preferences
### Responsibilities
- store font-size preference,
- store high-contrast mode,
- store reduced-motion option,
- store simplified-layout mode,
- store preferred language.

## 7.11 Reports and Exports
### Responsibilities
- generate course activity summaries,
- export CSV for submissions,
- export gradebook data,
- provide basic admin analytics.

## 7.12 Audit Logs
### Responsibilities
- record grade changes,
- record publication events,
- record admin-sensitive actions.

---

## 8. Frontend Design

## 8.1 Frontend Approach
The frontend uses:
- Vue 3 for component-driven interfaces,
- Inertia.js for server-driven page transitions,
- Tailwind CSS for utility-first styling,
- Vite for fast local development and asset compilation.

## 8.2 UI Areas
- Authentication pages
- Student dashboard
- Instructor dashboard
- Course home page
- Module/lesson page
- Assignment detail page
- Submission page
- Grade and feedback page
- Admin and reporting pages
- Profile and accessibility settings

## 8.3 Accessibility Principles
The UI must support:
- keyboard navigation,
- visible focus indicators,
- semantic HTML,
- high-contrast themes,
- readable typography,
- accessible validation messages,
- screen-reader compatibility,
- captions/transcripts metadata for multimedia.

---

## 9. Backend Design

## 9.1 Application Layers
The backend should follow layered design:
- **Controllers:** accept requests and return Inertia responses,
- **Form Requests:** validate input,
- **Services / Actions:** contain business logic,
- **Policies:** enforce authorization,
- **Jobs:** handle asynchronous tasks,
- **Events / Listeners:** decouple domain reactions,
- **Models:** represent database entities.

## 9.2 Recommended Project Structure
```text
app/
  Domain/
    User/
    Course/
    Module/
    Assignment/
    Submission/
    Announcement/
    Notification/
    Report/
    Accessibility/
  Http/
    Controllers/
    Middleware/
    Requests/
  Policies/
  Jobs/
  Events/
  Listeners/
  Support/

resources/js/
  Pages/
  Components/
  Layouts/
  Composables/
  Types/
  Utils/
```

---

## 10. Database Design

## 10.1 Core Entities
### users
- id
- name
- email
- password
- role
- email_verified_at
- created_at
- updated_at

### student_profiles
- id
- user_id
- student_number
- program
- year_level
- section

### instructor_profiles
- id
- user_id
- department
- employee_number

### courses
- id
- code
- title
- description
- department
- term
- academic_year
- status
- created_by

### course_sections
- id
- course_id
- section_name
- instructor_id
- schedule_text

### enrollments
- id
- user_id
- course_section_id
- status
- enrolled_at

### modules
- id
- course_section_id
- title
- description
- order_no
- published_at

### lessons
- id
- module_id
- title
- content
- type
- order_no
- published_at

### resources
- id
- lesson_id
- title
- file_path
- mime_type
- size_bytes
- visibility

### assignments
- id
- course_section_id
- title
- instructions
- due_at
- max_score
- allow_resubmission
- published_at

### submissions
- id
- assignment_id
- student_id
- status
- submitted_at
- is_late
- attempt_no

### submission_files
- id
- submission_id
- file_path
- original_name
- mime_type
- size_bytes

### grades
- id
- submission_id
- graded_by
- score
- feedback
- released_at

### announcements
- id
- course_section_id
- title
- body
- published_at
- created_by

### user_preferences
- id
- user_id
- font_size
- high_contrast
- reduced_motion
- simplified_layout
- language

### audit_logs
- id
- actor_id
- action
- entity_type
- entity_id
- metadata
- created_at

## 10.2 Key Relationships
- one user may have one student profile or one instructor profile,
- one course has many sections,
- one section has many modules,
- one module has many lessons,
- one lesson has many resources,
- one assignment belongs to one section,
- one student can have many submissions,
- one submission can have many files,
- one submission can have one grade record,
- one course section has many announcements.

---

## 11. File Storage Design

## 11.1 File Categories
- `course-resources/`
- `assignment-submissions/`
- `avatars/`
- `announcement-attachments/`

## 11.2 File Handling Rules
- validate MIME type,
- validate maximum file size,
- generate unique filenames,
- store metadata in the database,
- enforce access rules for protected files,
- never rely on the original filename as the storage key.

---

## 12. Queue and Scheduled Job Design

## 12.1 Queue Use Cases
Use queues for:
- sending email notifications,
- deadline reminders,
- grade release notifications,
- file metadata processing,
- export generation.

## 12.2 Scheduled Tasks
Recommended scheduled tasks:
- daily deadline reminders,
- nightly stale-upload cleanup,
- daily pending grading summary for instructors,
- nightly activity summary generation.

---

## 13. Security Design

## 13.1 Security Requirements
- password hashing,
- CSRF protection,
- route middleware,
- request validation,
- authorization policies,
- rate limiting,
- audit logging,
- secure file access,
- session timeout after inactivity.

## 13.2 Sensitive Data Areas
- grades,
- submissions,
- user identity and profiles,
- instructor-only management features,
- reports and analytics.

---

## 14. Non-Functional Design Goals

## 14.1 Performance
- responsive page loads,
- efficient course page rendering,
- pagination for large lists,
- caching for dashboard summaries,
- asynchronous handling of slow tasks.

## 14.2 Scalability
- modular domain structure,
- Redis-backed queue and cache,
- clean separation between synchronous and asynchronous work,
- future-ready feature flagging.

## 14.3 Maintainability
- domain-based code organization,
- clear service and policy layers,
- version-controlled migrations,
- reusable Vue components.

## 14.4 Usability
- mobile-first layouts,
- clean navigation,
- low cognitive load,
- consistent UI language.

## 14.5 Accessibility
- WCAG-conscious design,
- adjustable text size,
- high contrast,
- keyboard-only usability,
- reduced motion support.

---

## 15. Main User Flows

## 15.1 Student Submits Assignment
1. Student logs in.
2. Student opens course.
3. Student navigates to assignment.
4. Student uploads file.
5. System validates submission.
6. System stores file and submission record.
7. Student sees confirmation.
8. Instructor receives notification.

## 15.2 Instructor Grades Submission
1. Instructor logs in.
2. Instructor opens course submissions.
3. Instructor reviews attached files.
4. Instructor inputs score and feedback.
5. System saves grade.
6. System notifies student.

## 15.3 Admin Generates Report
1. Admin opens reporting page.
2. Admin filters target dataset.
3. System generates summarized view.
4. Admin exports CSV.

---

## 16. Development Roadmap

## Phase 1: Foundation
- authentication
- roles and policies
- layout system
- accessibility preferences
- database foundation

## Phase 2: Courses and Content
- courses
- sections
- modules
- lessons
- resources
- announcements

## Phase 3: Assessments
- assignments
- submissions
- gradebook
- notifications

## Phase 4: Reporting and Refinement
- exports
- audit logs
- performance tuning
- QA and accessibility improvements

---

## 17. Risks and Mitigation

## 17.1 Scope Creep
**Risk:** LMS features can expand too quickly.
**Mitigation:** Keep MVP focused on course delivery, submissions, and grading only.

## 17.2 Single-Engineer Bottleneck
**Risk:** Implementation depends heavily on one developer.
**Mitigation:** Maintain strong documentation, keep architecture simple, and prioritize reusable modules.

## 17.3 Asset and Data Availability
**Risk:** Real course data may not be available.
**Mitigation:** Use realistic sample data during prototyping and demo stages.

## 17.4 Time Constraints
**Risk:** Academic deadlines reduce development time.
**Mitigation:** Deliver core LMS functionality first, then add polish and secondary features.

---

## 18. Final Recommendation
Eduno should be implemented as a **Laravel 12 + Inertia + Vue 3 + Tailwind + Vite** modular monolith backed by **PostgreSQL** and **Redis**. This architecture provides the best balance of:
- maintainability,
- speed of development,
- UI quality,
- accessibility support,
- realistic project scope.

It is strong enough to look professional, modern enough to impress in presentation, and practical enough for one main developer to execute successfully.
