<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import type { BreadcrumbItemType, NavItem } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    mainNavItems?: NavItem[];
    rightNavItems?: NavItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    mainNavItems: () => [],
    rightNavItems: () => [],
});
const mainNavItems = props.mainNavItems;
const rightNavItems = props.rightNavItems;
const breadcrumbs = props.breadcrumbs;
</script>

<template>
    <AppShell class="flex-col">
        <AppHeader
            :breadcrumbs="breadcrumbs"
            :mainNavItems="mainNavItems"
            :rightNavItems="rightNavItems"
        />
        <AppContent>
            <slot />
        </AppContent>
    </AppShell>
</template>
