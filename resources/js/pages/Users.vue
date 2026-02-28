<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import DataTable from '@/components/users/DataTable.vue';
import DeleteUserDialog from '@/components/users/DeleteUserDialog.vue';
import UserFormDialog from '@/components/users/UserFormDialog.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, User } from '@/types';
import { columns } from '../components/users/columns';

const props = defineProps<{
    users: User[];
    roles: string[];
    authUserId: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '#',
    },
];

const formDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedUser = ref<User | null>(null);

function openCreateDialog() {
    selectedUser.value = null;
    formDialogOpen.value = true;
}

function openEditDialog(user: User) {
    selectedUser.value = user;
    formDialogOpen.value = true;
}

function openDeleteDialog(user: User) {
    selectedUser.value = user;
    deleteDialogOpen.value = true;
}

const tableMeta = {
    onEdit: (user: User) => openEditDialog(user),
    onDelete: (user: User) => openDeleteDialog(user),
};
</script>

<template>

    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight">Users</h1>
                <Button @click="openCreateDialog">
                    <Plus class="mr-2 h-4 w-4" />
                    Add User
                </Button>
            </div>

            <DataTable :columns="columns" :data="props.users" :meta="tableMeta" />
        </div>

        <UserFormDialog v-model:open="formDialogOpen" :user="selectedUser" :roles="props.roles" />

        <DeleteUserDialog v-model:open="deleteDialogOpen" :user="selectedUser" />
    </AppLayout>
</template>
