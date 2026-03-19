<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import student from '@/routes/student';
import Title from '@/components/Title.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: student.index().url,
  },
];

const { auth } = usePage().props;
const userName = auth.user.name;
</script>

<template>

  <Head title="Home" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4">
      <Title title="Dashboard" :subtitle="`Selamat Belajar ${userName}`" />

      <!-- Show latest users learning course -->
      <div class="flex justify-between mt-4">
        <p>Lanjutkan Proses Belajar Anda terakhir kali:</p>
      </div>
    </div>
  </AppLayout>
</template>
