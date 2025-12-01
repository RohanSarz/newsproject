<script setup lang="ts">
import { defaultNavItems } from '@/config/navigation';
import Layout from '@/layouts/app/MainLayout.vue';
import type { BreadcrumbItemType, NavItem } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    mainNavItems?: NavItem[];
    footerNavItems?: NavItem[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    mainNavItems: () => [...defaultNavItems],
    footerNavItems: () => [],
});
</script>

<template>
    <Layout
        :breadcrumbs="breadcrumbs"
        :mainNavItems="mainNavItems"
        :footerNavItems="footerNavItems"
    >
        <slot />
    </Layout>
</template>
