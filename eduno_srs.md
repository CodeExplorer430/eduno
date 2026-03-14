# Eduno Software Requirements Specification (SRS)

## 1. Introduction

### 1.1 Purpose
This Software Requirements Specification defines the functional and non-functional requirements for **Eduno**, a web-based Learning Management System for students, instructors, and administrators. The document serves as the primary reference for analysis, design, development, testing, and evaluation of the system.

### 1.2 Intended Audience
This document is intended for:
- project proponents,
- developers,
- testers,
- documentation team members,
- instructors and evaluators,
- future maintainers.

### 1.3 Product Scope
Eduno is a dedicated LMS intended to centralize academic delivery workflows such as:
- course materials,
- lessons and modules,
- announcements,
- assignment submissions,
- grading and feedback,
- basic learning notifications.

Eduno will function as a **separate platform** from UCC AIMS. It is not intended to replace enrollment, finance, registrar, or payment processing systems.

### 1.4 Definitions and Acronyms
- **LMS** — Learning Management System
- **AIMS** — Academic Information Management System
- **UI** — User Interface
- **UX** — User Experience
- **SRS** — Software Requirements Specification
- **MVP** — Minimum Viable Product
- **RBAC** — Role-Based Access Control

---

## 2. Overall Description

### 2.1 Product Perspective
Eduno is an independent web-based system that complements existing university platforms by handling course delivery and learning interactions. It will be deployed as a browser-accessible application optimized for desktops and mobile devices.

### 2.2 Product Functions
At a high level, the system shall:
- authenticate users,
- manage courses and course sections,
- deliver modules, lessons, and resources,
- support announcements,
- support assignment creation and submission,
- support grading and feedback,
- notify users of relevant academic events,
- store accessibility preferences,
- provide exportable reports.

### 2.3 User Classes and Characteristics
#### Students
- Use mobile phones and laptops.
- Need simple and fast access to learning materials.
- Need clear deadline and submission tracking.

#### Instructors
- Need efficient content management and grading tools.
- Need dashboards with minimal administrative friction.

#### Administrators
- Need user management, reporting, and platform oversight.

### 2.4 Operating Environment
- Modern desktop and mobile web browsers
- Linux-based application server
- PostgreSQL database server
- Redis cache/queue server

### 2.5 Design and Implementation Constraints
- Must be a web application.
- Must not depend on direct AIMS integration in the MVP.
- Must support accessibility-first UI/UX design.
- Must remain feasible for single main-developer implementation.
- Must use the approved VILT-based architecture.

### 2.6 Assumptions and Dependencies
- Users will have internet access through mobile data or Wi-Fi.
- Demo data may be used if official institutional data is unavailable.
- Email service availability is assumed for notifications.
- The system will be developed using Laravel, Vue, Inertia, Tailwind, and Vite.

---

## 3. External Interface Requirements

### 3.1 User Interfaces
The system shall provide interfaces for:
- login and registration,
- student dashboard,
- instructor dashboard,
- course browsing,
- course module pages,
- lesson and resource viewing,
- assignment submission,
- grading and feedback,
- admin reporting,
- accessibility settings.

### 3.2 Hardware Interfaces
The system does not require dedicated hardware. Users access it through standard desktop or mobile devices.

### 3.3 Software Interfaces
The system interfaces with:
- PostgreSQL for structured data,
- Redis for cache, queue, and session support,
- local or object storage for file uploads,
- email provider for notifications.

### 3.4 Communications Interfaces
The system uses HTTPS over the web and SMTP/API-based notification channels for emails.

---

## 4. System Features and Functional Requirements

## 4.1 User Authentication and Authorization
### Description
The system shall authenticate users and enforce role-based access control.

### Functional Requirements
- **FR-001** The system shall allow users to log in using email and password.
- **FR-002** The system shall allow users to reset forgotten passwords.
- **FR-003** The system shall support email verification.
- **FR-004** The system shall assign role-based permissions for students, instructors, and administrators.
- **FR-005** The system shall restrict unauthorized access to protected resources.

## 4.2 User Profile Management
- **FR-006** The system shall allow users to view and update their basic profile information.
- **FR-007** The system shall allow users to save accessibility preferences.

## 4.3 Course Management
- **FR-008** The system shall allow instructors to create and manage courses.
- **FR-009** The system shall allow instructors to organize courses into sections.
- **FR-010** The system shall allow administrators to manage course status.
- **FR-011** The system shall allow students to view their enrolled courses.

## 4.4 Modules, Lessons, and Resources
- **FR-012** The system shall allow instructors to create course modules.
- **FR-013** The system shall allow instructors to create lessons under modules.
- **FR-014** The system shall allow instructors to upload and manage learning resources.
- **FR-015** The system shall allow students to access available lessons and resources.
- **FR-016** The system shall support visibility control for modules, lessons, and resources.

## 4.5 Announcements
- **FR-017** The system shall allow instructors to post announcements to enrolled students.
- **FR-018** The system shall display recent announcements on the student dashboard.
- **FR-019** The system shall optionally notify students by email when announcements are published.

## 4.6 Assignments
- **FR-020** The system shall allow instructors to create assignments with instructions and due dates.
- **FR-021** The system shall allow instructors to specify accepted file types and submission rules.
- **FR-022** The system shall allow students to view active assignments.
- **FR-023** The system shall indicate assignment due dates and status clearly.

## 4.7 Submissions
- **FR-024** The system shall allow students to upload one or more files for assignment submissions.
- **FR-025** The system shall validate file type and size before accepting submissions.
- **FR-026** The system shall record submission timestamp and attempt number.
- **FR-027** The system shall identify late submissions based on due date and time.
- **FR-028** The system shall allow instructors to view submitted files.
- **FR-029** The system shall show submission confirmation to students.

## 4.8 Grading and Feedback
- **FR-030** The system shall allow instructors to assign grades to submissions.
- **FR-031** The system shall allow instructors to provide written feedback.
- **FR-032** The system shall allow students to view released grades and feedback.
- **FR-033** The system shall record grade release timestamps.

## 4.9 Notifications
- **FR-034** The system shall notify students about new announcements.
- **FR-035** The system shall notify instructors about new submissions.
- **FR-036** The system shall notify students when grades are released.
- **FR-037** The system shall support deadline reminder notifications.

## 4.10 Reporting and Export
- **FR-038** The system shall allow instructors to export submission records in CSV format.
- **FR-039** The system shall allow administrators to view summary reports.
- **FR-040** The system shall provide basic activity metrics such as submission counts and late submissions.

## 4.11 Accessibility Support
- **FR-041** The system shall provide adjustable font sizes.
- **FR-042** The system shall provide a high-contrast mode.
- **FR-043** The system shall support keyboard-only navigation for core workflows.
- **FR-044** The system shall provide readable validation and error messages.
- **FR-045** The system shall store user accessibility preferences for future sessions.

## 4.12 Audit and Logging
- **FR-046** The system shall log grade modification events.
- **FR-047** The system shall log administrative actions affecting course visibility or user roles.
- **FR-048** The system shall maintain audit records for sensitive actions.

---

## 5. Non-Functional Requirements

## 5.1 Performance Requirements
- **NFR-001** The system should load major pages within acceptable response times under normal academic usage.
- **NFR-002** File uploads shall provide clear progress and error states.
- **NFR-003** Slow operations such as exports and bulk notifications shall be handled asynchronously.

## 5.2 Reliability Requirements
- **NFR-004** The system shall preserve submission records once confirmed.
- **NFR-005** The system shall provide graceful error handling and recovery messaging.
- **NFR-006** Critical actions such as grading and submission storage shall be transaction-safe where applicable.

## 5.3 Availability Requirements
- **NFR-007** The system should be available during expected class and submission periods, subject to infrastructure limitations.

## 5.4 Security Requirements
- **NFR-008** Passwords shall be securely hashed.
- **NFR-009** Sensitive routes shall require authenticated and authorized access.
- **NFR-010** The system shall protect against CSRF and common web input attacks.
- **NFR-011** File access shall be restricted to authorized users.
- **NFR-012** The system shall support rate limiting on authentication-sensitive endpoints.

## 5.5 Usability Requirements
- **NFR-013** The system shall use a consistent, mobile-first interface design.
- **NFR-014** Navigation shall be understandable without requiring external training.
- **NFR-015** Core user tasks shall be completable in minimal steps.

## 5.6 Accessibility Requirements
- **NFR-016** The system shall maintain sufficient text and background contrast.
- **NFR-017** The system shall provide visible focus indicators.
- **NFR-018** The system shall support screen-reader-friendly structures for forms and navigation.
- **NFR-019** The system shall avoid conveying essential information by color alone.

## 5.7 Maintainability Requirements
- **NFR-020** The system shall use modular code organization.
- **NFR-021** The system shall support future feature extension without major rewrites.
- **NFR-022** Database schema changes shall be version-controlled through migrations.

## 5.8 Scalability Requirements
- **NFR-023** The system should support growth in users, courses, and submissions through cache and queue-backed architecture.

---

## 6. Data Requirements

### 6.1 Data Entities
The system shall manage data for:
- users,
- student profiles,
- instructor profiles,
- courses,
- course sections,
- enrollments,
- modules,
- lessons,
- resources,
- announcements,
- assignments,
- submissions,
- submission files,
- grades,
- notifications,
- user preferences,
- audit logs.

### 6.2 Data Integrity Rules
- Each submission must belong to one valid assignment and one valid student.
- Each grade must belong to a valid submission.
- Each lesson must belong to one valid module.
- Each module must belong to one valid course section.
- Users may only access records permitted by their role and course relationship.

### 6.3 Retention Assumptions
The MVP will store course, submission, and grade data for demonstration and evaluation purposes. Production retention policy is outside MVP scope and should be defined by institutional policy later.

---

## 7. Constraints
- The system is limited to a web-based MVP/prototype scope.
- The system will not replace AIMS.
- The system will not process tuition, billing, or payment workflows.
- The system will not include advanced proctoring or plagiarism engines in the MVP.
- The system must remain buildable within academic timelines.

---

## 8. Assumptions
- Students and instructors can access modern web browsers.
- Internet connectivity is available during usage.
- Sample academic data may be used if official data is unavailable.
- Email notifications are sufficient for the MVP.

---

## 9. Acceptance Criteria Summary
The project may be considered acceptable when:
- authentication and role-based access work correctly,
- students can access course materials,
- instructors can create assignments,
- students can submit work,
- instructors can grade and provide feedback,
- the UI demonstrates accessibility features,
- notifications and exports function at MVP level,
- core user flows are validated through usability testing.

---

## 10. Appendices
### Appendix A: Suggested MVP Priorities
1. Authentication and roles
2. Courses and modules
3. Assignments and submissions
4. Grading and feedback
5. Accessibility mode
6. Notifications
7. Reporting and exports

### Appendix B: Suggested Testable Core Scenarios
- Student logs in and accesses a course.
- Instructor uploads a lesson resource.
- Instructor creates an assignment.
- Student uploads a submission.
- Instructor grades the submission.
- Student views released feedback.
- User changes accessibility settings and sees them persist.
