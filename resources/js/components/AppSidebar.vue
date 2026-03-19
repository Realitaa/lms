<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, House, Search, Users } from 'lucide-vue-next';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import student from '@/routes/student';
import courses from '@/routes/courses';
import users from '@/routes/users';
import type { NavItem } from '@/types';
import AppLogo from './AppLogo.vue';
import HugeIconsCourse from './icons/HugeIconsCourse.vue';

const { auth } = usePage().props;
const userRole = auth.user.role;

const userManagement = () => {
    if (userRole === 'admin') {
        return [
            {
                title: 'Manajemen Pengguna',
                href: users.index(),
                icon: Users,
            },
        ];
    }
    return [];
};

const adminEditorMenu = () => {
    if (userRole === 'admin' || userRole === 'editor') {
        return [
            {
                title: 'Dashboard',
                href: dashboard(),
                icon: LayoutGrid,
            },
            ...userManagement(),
            {
                title: 'Manajemen Kursus',
                href: courses.index(),
                icon: HugeIconsCourse,
            },
        ];
    }
    return [];
};

const studentMenu = () => {
    if (userRole === 'user') {
        return [
            {
                title: 'Home',
                href: student.index(),
                icon: House,
            },
            {
                title: 'Discover',
                href: student.discover(),
                icon: Search,
            },
        ];
    }
    return [];
};

const mainNavItems: NavItem[] = [
    ...adminEditorMenu(),
    ...studentMenu(),
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
