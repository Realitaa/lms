<script setup lang="ts">
defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    required: false,
  },
});
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold tracking-tight">{{ title }}</h1>
    <p class="text-muted-foreground" v-if="subtitle">{{ subtitle }}</p>
  </div>
</template>