<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner'
import * as z from 'zod';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { store, update } from '@/routes/users';
import type { User } from '@/types';
import currentUserId from '@/utils/currentUserId';

const props = defineProps<{
  open: boolean;
  user: User | null;
  roles: string[];
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();

const isEditing = computed(() => !!props.user);
const dialogTitle = computed(() =>
  isEditing.value ? 'Edit User' : 'Create User',
);
const dialogDescription = computed(() =>
  isEditing.value
    ? 'Update the user details below.'
    : 'Fill in the details to create a new user.',
);

const formSchema = computed(() => {
  const base = {
    name: z.string().min(1, 'Name is required').max(255),
    email: z.string().email('Invalid email address').max(255),
    role: z.string().min(1, 'Role is required'),
  };

  if (isEditing.value) {
    return toTypedSchema(
      z.object({
        ...base,
        password: z
          .string()
          .min(8, 'Password must be at least 8 characters')
          .optional()
          .or(z.literal('')),
      }),
    );
  }

  return toTypedSchema(
    z.object({
      ...base,
      password: z
        .string()
        .min(8, 'Password must be at least 8 characters'),
    }),
  );
});

const { handleSubmit, resetForm, setValues, isSubmitting } = useForm({
  validationSchema: formSchema,
  initialValues: {
    name: '',
    email: '',
    password: '',
    role: '',
  },
});

watch(
  () => props.open,
  (open) => {
    if (open && props.user) {
      setValues({
        name: props.user.name,
        email: props.user.email,
        password: '',
        role: props.user.role,
      });
    } else if (open) {
      resetForm();
    }
  },
);

const onSubmit = handleSubmit((values) => {
  const data: Record<string, string> = {
    name: values.name,
    email: values.email,
    role: values.role,
  };

  if (values.password) {
    data.password = values.password;
  }

  if (isEditing.value && props.user) {
    router.put(update.url(props.user.id), data, {
      preserveScroll: true,
      onSuccess: () => {
        emit('update:open', false);
      },
      onError: (errors) => {
        toast.error(`Gagal memperbarui pengguna ${props.user?.name}: ${Object.values(errors)[0]}`);
      },
    });
  } else {
    router.post(store.url(), data, {
      preserveScroll: true,
      onSuccess: () => {
        emit('update:open', false);
      },
      onError: (errors) => {
        toast.error(`Gagal membuat pengguna: ${Object.values(errors)[0]}`);
      },
    });
  }
});
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-106.25">
      <DialogHeader>
        <DialogTitle>{{ dialogTitle }}</DialogTitle>
        <DialogDescription>{{
          dialogDescription
        }}</DialogDescription>
      </DialogHeader>

      <form class="grid gap-4 py-4" @submit="onSubmit">
        <FormField v-slot="{ componentField }" name="name">
          <FormItem>
            <FormLabel>Name</FormLabel>
            <FormControl>
              <Input type="text" placeholder="John Doe" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="email">
          <FormItem>
            <FormLabel>Email</FormLabel>
            <FormControl>
              <Input type="email" placeholder="john@example.com" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="password">
          <FormItem>
            <FormLabel>
              Password
              <span v-if="isEditing" class="text-muted-foreground text-xs font-normal">
                (leave blank to keep current)
              </span>
            </FormLabel>
            <FormControl>
              <Input type="password" placeholder="••••••••" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="role">
          <FormItem>
            <FormLabel>Role</FormLabel>
            <Select v-bind="componentField">
              <FormControl>
                <SelectTrigger>
                  <SelectValue placeholder="Select a role" />
                </SelectTrigger>
              </FormControl>
              <SelectContent>
                <SelectItem v-for="role in roles" :key="role" :value="role" :disabled="currentUserId === props.user?.id && role !== 'admin'">
                  {{
                    role.charAt(0).toUpperCase() +
                    role.slice(1)
                  }}
                </SelectItem>
              </SelectContent>
            </Select>
            <FormMessage />
          </FormItem>
        </FormField>

        <DialogFooter>
          <Button type="button" variant="outline" @click="emit('update:open', false)">
            Cancel
          </Button>
          <Button type="submit" :disabled="isSubmitting">
            {{ isEditing ? 'Save Changes' : 'Create User' }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
