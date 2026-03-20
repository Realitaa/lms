<script setup lang="ts">
import { Check, Book } from 'lucide-vue-next';
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
</script>

<template>
  <ScrollArea class="h-[calc(100vh-320px)] pr-4">
    <div>
      <template v-for="(item, index) in 2" :key="index">
        <h3 class="text-md font-bold my-4">Nama Modul 1</h3>
        <!-- When the course is completed, the background color will be green -->
        <Card v-for="(item, index) in 10" :key="index" class="mb-4 hover:bg-gray-100 cursor-pointer"
          :class="index % 2 === 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-white dark:bg-slate-500'">
          <CardHeader class="flex">
            <div class="flex justify-center items-center">
              <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                <!-- if couse completed, icon is check, else icon is book -->
                <Check v-if="index % 2 === 0" class="w-3 h-3 text-white" :stroke-width="5" />
                <Book v-else class="w-3 h-3 text-white" :stroke-width="3" />
              </div>
              <div class="ml-4">
                <CardTitle>Card Title</CardTitle>
                <CardDescription>
                  1 Kuis
                </CardDescription>
              </div>
            </div>
          </CardHeader>
        </Card>
      </template>
    </div>
  </ScrollArea>
</template>