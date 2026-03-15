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
    enrollments_count?: number;
    assignments_count?: number;
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
    section?: CourseSection;
    lessons?: Lesson[];
    created_at: string;
    updated_at: string;
}

export interface Lesson {
    id: number;
    module_id: number;
    title: string;
    content: string | null;
    type: 'text' | 'pdf' | 'video' | 'link';
    order_no: number;
    published_at: string | null;
    module?: Module;
    resources?: Resource[];
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
    visibility: 'enrolled' | 'instructor' | 'public';
    lesson?: Lesson;
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
    section?: CourseSection;
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
    submission?: Submission;
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
    assignment?: Assignment;
    student?: User;
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
    section?: CourseSection;
    created_at: string;
    updated_at: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedResponse<T> {
    data: T[];
    links: PaginationLink[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

export interface AuditLog {
    id: number;
    actor_id: number;
    action: string;
    entity_type: string;
    entity_id: number;
    metadata: Record<string, unknown> | null;
    created_at: string;
    actor?: User;
}
