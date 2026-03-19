<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Discover Course',
    href: '',
  },
];
</script>

<template>

  <Head title="Discover Course" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4">
      <Title title="Discover Course" />

      <!-- Show all available courses with search and pagination -->
      <div class="flex justify-between mt-4">

      </div>
    </div>
  </AppLayout>
</template>