<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/layouts/app/MainLayout.vue';
import StudentHeaderLayout from '@/layouts/app/StudentHeaderLayout.vue';
import InstructorSidebarLayout from '@/layouts/app/InstructorSidebarLayout.vue';
import type { BreadcrumbItemType, NavItem } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    mainNavItems?: NavItem[];
    footerNavItems?: NavItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    mainNavItems: () => [],
    footerNavItems: () => [],
});

const page = usePage();
const userRole = computed(() => page.props.auth?.role);

// Dynamically select layout based on user role
const LayoutComponent = computed(() => {
    switch (userRole.value) {
        case 'student':
            return StudentHeaderLayout;
        case 'instructor':
            return InstructorSidebarLayout;
        case 'admin':
            // You can create a specific admin layout or use InstructorSidebarLayout
            return InstructorSidebarLayout;
        default:
            // Default to main layout for other roles or when not authenticated
            return MainLayout;
    }
});
</script>

<template>
    <component
        :is="LayoutComponent"
        :breadcrumbs="breadcrumbs"
        :mainNavItems="mainNavItems"
        :footerNavItems="footerNavItems"
    >
        <slot />
    </component>
</template>