<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import { studentMainNavItems } from '@/config/navigation';
import instructor from '@/routes/instructor';
import type { BreadcrumbItemType, NavItem } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
const page = usePage();

const user = page.props.auth;

if (user.role === 'instructor') {
    router.visit(instructor.dashboard().url);
}
interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    mainNavItems?: NavItem[];
    rightNavItems?: NavItem[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    mainNavItems: () => [...studentMainNavItems],
    rightNavItems: () => [],
});
</script>

<template>
    <AppShell class="flex-col">
        <AppHeader
            :breadcrumbs="breadcrumbs"
            :mainNavItems="[...studentMainNavItems]"
            :rightNavItems="rightNavItems"
        />
        <AppContent>
            <slot />
        </AppContent>
    </AppShell>
</template>
