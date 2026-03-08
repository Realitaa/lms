export type Option = {
    id: number;
    question_id: number;
    option_text: Record<string, unknown>;
    is_correct: boolean;
    created_at: string;
    updated_at: string;
};

export type Question = {
    id: number;
    quiz_id: number;
    question_text: Record<string, unknown>;
    order: number;
    points: number;
    options: Option[];
    created_at: string;
    updated_at: string;
};

export type Quiz = {
    id: number;
    module_id: number;
    title: string;
    passing_score: number;
    time_limit: number | null;
    questions: Question[];
    created_at: string;
    updated_at: string;
};

export type Lesson = {
    id: number;
    module_id: number;
    title: string;
    content: Record<string, unknown>;
    order: number;
    is_published: boolean;
    created_at: string;
    updated_at: string;
};

export type Module = {
    id: number;
    course_id: number;
    title: string;
    order: number;
    lessons: Lesson[];
    quizzes: Quiz[];
    created_at: string;
    updated_at: string;
};

export type Course = {
    id: number;
    title: string;
    slug: string;
    cover_image: string;
    code: string;
    description: string;
    modules?: Module[];
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};