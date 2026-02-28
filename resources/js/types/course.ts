export type Course = {
    id: number;
    title: string;
    slug: string;
    cover_image: string;
    code: string;
    description: string;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};