<script setup lang="ts">
import {
    instructorFooterNavItems,
    instructorMainNavItems,
} from '@/config/navigation';
import AppLayout from '@/layouts/app/InstructorSidebarLayout.vue';
import student from '@/routes/student';
import type { BreadcrumbItemType } from '@/types';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();

const user = page.props.auth;
if (user.role === 'student') {
    router.visit(student.dashboard().url);
}
interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    mainNavItems: () => [...instructorMainNavItems],
    footerNavItems: () => [],
});
</script>

<template>
    <AppLayout
        :breadcrumbs="breadcrumbs"
        :mainNavItems="instructorMainNavItems"
        :footerNavItems="instructorFooterNavItems"
    >
        <slot />
    </AppLayout>
</template>
