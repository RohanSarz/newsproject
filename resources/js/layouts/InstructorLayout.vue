<script setup lang="ts">
import {
    instructorFooterNavItems,
    instructorMainNavItems,
} from '@/config/navigation';
import AppLayout from '@/layouts/app/InstructorSidebarLayout.vue';
import student from '@/routes/student';
import type { BreadcrumbItemType, NavItem } from '@/types';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();

const user = page.props.auth;
if (user.role === 'student') {
    router.visit(student.dashboard().url);
}
interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    mainNavItems?: NavItem[];
    footerNavItems?: NavItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    mainNavItems: () => [...instructorMainNavItems],
    footerNavItems: () => [...instructorFooterNavItems],
});
const { mainNavItems, footerNavItems, breadcrumbs } = props;
</script>

<template>
    <AppLayout
        :breadcrumbs="breadcrumbs"
        :mainNavItems="mainNavItems"
        :footerNavItems="footerNavItems"
    >
        <slot />
    </AppLayout>
</template>
