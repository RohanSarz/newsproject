<script setup lang="ts">
import {
    defaultNavItems,
    instructorFooterNavItems,
    instructorMainNavItems,
    studentMainNavItems,
    studentRightNavItems,
} from '@/config/navigation';
import MainLayout from '@/layouts/app/MainLayout.vue';
import type { BreadcrumbItemType, NavItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { toReactive } from '@vueuse/core';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    mainNavItems?: NavItem[];
    footerNavItems?: NavItem[];
    rightNavItems?: NavItem[];
}

const page = usePage();

const user = page.props.auth;

const getMainNavItemsByRole = () => {
    let navLinks: NavItem[] = defaultNavItems;

    if (user) {
        navLinks =
            user.role === 'student'
                ? studentMainNavItems
                : user.role === 'instructor'
                  ? instructorMainNavItems
                  : defaultNavItems;
    }

    return navLinks;
};
const getFooterOrRightNavItemsByRole = () => {
    const navLinks =
        user.role === 'student'
            ? studentRightNavItems
            : user.role === 'instructor'
              ? instructorFooterNavItems
              : [];
    return navLinks;
};
const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    mainNavItems: () => [],
    footerNavItems: () => [],
    rightNavItems: () => [],
});

const { breadcrumbs } = toReactive(props);
</script>

<template>
    <MainLayout
        :breadcrumbs="breadcrumbs"
        :mainNavItems="getMainNavItemsByRole()"
        :rightNavItems="getFooterOrRightNavItemsByRole()"
        :footerNavItems="getFooterOrRightNavItemsByRole()"
    >
        <slot />
    </MainLayout>
</template>
