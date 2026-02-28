<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { destroy } from '@/routes/users';
import type { User } from '@/types';

const props = defineProps<{
  open: boolean;
  user: User | null;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const isDeleting = ref(false);

function onConfirm() {
  if (!props.user) return;

  isDeleting.value = true;
  router.delete(destroy.url(props.user.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success(`Pengguna ${props.user?.name} berhasil dihapus`);
      emit('update:open', false);
    },
    onError: (errors) => {
      toast.error(`Gagal menghapus pengguna ${props.user?.name}: ${Object.values(errors)[0]}`);
    },
    onFinish: () => {
      isDeleting.value = false;
    },
  });
}
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-106.25">
      <DialogHeader>
        <DialogTitle>Delete User</DialogTitle>
        <DialogDescription>
          Are you sure you want to delete
          <strong>{{ user?.name }}</strong>? This action cannot be undone.
        </DialogDescription>
      </DialogHeader>

      <DialogFooter>
        <Button type="button" variant="outline" @click="emit('update:open', false)">
          Cancel
        </Button>
        <Button type="button" variant="destructive" :disabled="isDeleting" @click="onConfirm">
          Delete
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
