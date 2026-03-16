export interface UserPreferences {
    id: number;
    user_id: number;
    font_size: 'small' | 'medium' | 'large';
    high_contrast: boolean;
    reduced_motion: boolean;
    simplified_layout: boolean;
    language: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    role: 'student' | 'instructor' | 'admin';
    email_verified_at: string | null;
    preferences?: UserPreferences;
    created_at: string;
    updated_at: string;
}

export interface Course {
    id: number;
    code: string;
    title: string;
    description: string | null;
    department: string;
    term: string;
    academic_year: string;
    status: 'draft' | 'published' | 'archived';
    created_by: number;
    created_at: string;
    updated_at: string;
}

export interface CourseSection {
    id: number;
    course_id: number;
    section_name: string;
    instructor_id: number;
    schedule_text: string | null;
    course?: Course;
    instructor?: User;
    created_at: string;
    updated_at: string;
}

export interface Module {
    id: number;
    course_section_id: number;
    title: string;
    description: string | null;
    order_no: number;
    published_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Lesson {
    id: number;
    module_id: number;
    title: string;
    content: string | null;
    type: string;
    order_no: number;
    published_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Resource {
    id: number;
    lesson_id: number;
    title: string;
    file_path: string;
    mime_type: string;
    size_bytes: number;
    visibility: string;
    created_at: string;
    updated_at: string;
}

export interface Assignment {
    id: number;
    course_section_id: number;
    title: string;
    instructions: string | null;
    due_at: string | null;
    max_score: number;
    allow_resubmission: boolean;
    published_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface SubmissionFile {
    id: number;
    submission_id: number;
    file_path: string;
    original_name: string;
    mime_type: string;
    size_bytes: number;
    created_at: string;
    updated_at: string;
}

export interface Grade {
    id: number;
    submission_id: number;
    graded_by: number;
    score: number;
    feedback: string | null;
    released_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Submission {
    id: number;
    assignment_id: number;
    student_id: number;
    status: 'submitted' | 'graded' | 'returned';
    submitted_at: string;
    is_late: boolean;
    attempt_no: number;
    files?: SubmissionFile[];
    grade?: Grade;
    created_at: string;
    updated_at: string;
}

export interface Announcement {
    id: number;
    course_section_id: number;
    title: string;
    body: string;
    published_at: string | null;
    created_by: number;
    author?: User;
    course_section?: CourseSection & { course: Course };
    created_at: string;
    updated_at: string;
}

export interface Enrollment {
    id: number;
    user_id: number;
    course_section_id: number;
    status: string;
    enrolled_at: string;
    created_at: string;
    updated_at: string;
}

export interface AuditLog {
    id: number;
    actor_id: number | null;
    action: string;
    entity_type: string;
    entity_id: number | null;
    metadata: Record<string, unknown> | null;
    created_at: string;
}

export type Role = 'student' | 'instructor' | 'admin';
