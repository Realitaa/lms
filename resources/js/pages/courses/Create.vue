<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import CourseForm from '@/components/courses/CourseForm.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { store } from '@/routes/courses';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kursus',
        href: '/courses',
    },
    {
        title: 'Buat Kursus',
        href: '#',
    },
];

const isSubmitting = ref(false);

function onSubmit(formData: FormData) {
    isSubmitting.value = true;
    router.post(store.url(), formData, {
        onSuccess: () => {
            toast.success('Kursus berhasil dibuat');
        },
        onError: (errors) => {
            toast.error(`Gagal membuat kursus: ${Object.values(errors)[0]}`);
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
}
</script>

<template>

    <Head title="Buat Kursus" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child>
                    <Link href="/courses">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Buat Kursus</h1>
                    <p class="text-muted-foreground">Halaman untuk membuat kursus baru</p>
                </div>
            </div>

            <CourseForm :is-submitting="isSubmitting" @submit="onSubmit" />
        </div>
    </AppLayout>
</template>
