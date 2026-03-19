<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import Title from '@/components/Title.vue';

defineProps<{
  backUrl: string;
  title: string;
  subtitle?: string;
}>();
</script>

<template>
  <div class="flex items-center gap-4">
    <Button variant="outline" size="icon" as-child>
      <Link :href="backUrl">
        <ArrowLeft class="h-4 w-4" />
      </Link>
    </Button>
    <div>
      <Title :title="title" :subtitle="subtitle" />
    </div>
  </div>
</template>