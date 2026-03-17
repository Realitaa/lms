export type Option = {
    id: number;
    question_id: number;
    option_text: Record<string, unknown>;
    is_correct: boolean;
};

export type Question = {
    id: number;
    quiz_id: number;
    question_text: Record<string, unknown>;
    points: number;
    order: number;
    options: Option[];
};

export type Quiz = {
    id: number;
    title: string;
    type: 'pre' | 'post';
    passing_score: number;
    time_limit: number;
    questions: Question[];
};

export type Lesson = {
    id: number;
    module_id: number;
    title: string;
    content: Record<string, unknown>;
    order: number;
    is_published: boolean;
    quizzes: Quiz[];
};

export type Module = {
    id: number;
    course_id: number;
    title: string;
    order: number;
    lessons: Lesson[];
    quizzes: Quiz[];
};

export type Course = {
    id: number;
    title: string;
    slug: string;
    cover_image?: string;
    code: string;
    description?: string;
    modules?: Module[];
    quizzes?: Quiz[];
};
