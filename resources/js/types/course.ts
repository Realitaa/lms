export type Lesson = {
    id: number;
    module_id: number;
    title: string;
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