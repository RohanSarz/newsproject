<script setup lang="ts">
import { studentMainNavItems, studentRightNavItems } from '@/config/navigation';
import AppLayout from '@/layouts/app/StudentHeaderLayout.vue';
import type { BreadcrumbItemType, NavItem } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    mainNavItems?: NavItem[];
    rightNavItems?: NavItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    mainNavItems: () => [...studentMainNavItems],
    rightNavItems: () => [...studentRightNavItems],
});
const { mainNavItems, rightNavItems, breadcrumbs } = props;
</script>

<template>
    <AppLayout
        :breadcrumbs="breadcrumbs"
        :mainNavItems="mainNavItems"
        :rightNavItems="rightNavItems"
    >
        <slot />
    </AppLayout>
</template>
