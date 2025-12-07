<script setup lang="ts">
import {
    instructorFooterNavItems,
    instructorMainNavItems,
} from '@/config/navigation';
import AppLayout from '@/layouts/app/InstructorSidebarLayout.vue';
import type { BreadcrumbItemType, NavItem } from '@/types';

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
