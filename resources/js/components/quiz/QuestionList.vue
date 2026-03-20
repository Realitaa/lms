<script setup lang="ts">
import { Button } from '@/components/ui/button';
</script>

<template>
  <div class="m-4 gap-4 grid grid-cols-10">
    <Button variant="outline" v-for="item in 90" class="cursor-pointer">{{ item }}</Button>
  </div>
</template>