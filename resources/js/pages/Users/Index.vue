<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, index } from '@/routes/users';

import { User, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User',
        href: index().url,
    },
];

defineProps<{
    users: User[];
}>();

function confirmDelete(userId: number) {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(destroy(userId).url, {
            preserveScroll: true,
            preserveState: false,
            onError: () => {
                alert('There was an error deleting the user');
            },
        });
    }
}
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #header>
            <h2 class="text-xl leading-tight font-semibold">Users</h2>
        </template>

        <div class="p-4">
            <div
                class="rounded-lg border bg-card text-card-foreground shadow-sm"
            >
                <div class="relative w-full overflow-auto">
                    <div
                        class="flex items-center justify-between gap-4 bg-slate-200 p-4 dark:bg-accent/40"
                    >
                        <h1>Create a new user</h1>
                        <Link
                            :href="create()"
                            class="rounded-md bg-foreground px-4 py-1 text-accent"
                            >Create</Link
                        >
                    </div>
                    <table class="w-full caption-bottom text-sm">
                        <thead class="[&_tr]:border-b">
                            <tr
                                class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                            >
                                <th
                                    class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0"
                                >
                                    ID
                                </th>
                                <th
                                    class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0"
                                >
                                    Name
                                </th>
                                <th
                                    class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0"
                                >
                                    Email
                                </th>
                                <th
                                    class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <transition-group
                            name="users-list"
                            tag="tbody"
                            class="[&_tr:last-child]:border-0"
                        >
                            <tr
                                v-for="user in users"
                                :key="user.id"
                                class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                            >
                                <td
                                    class="p-4 align-middle [&:has([role=checkbox])]:pr-0"
                                >
                                    {{ user.id }}
                                </td>
                                <td
                                    class="p-4 align-middle [&:has([role=checkbox])]:pr-0"
                                >
                                    {{ user.name }}
                                </td>
                                <td
                                    class="p-4 align-middle [&:has([role=checkbox])]:pr-0"
                                >
                                    {{ user.email }}
                                </td>
                                <td class="flex gap-x-2 p-4">
                                    <Link
                                        :href="edit(user.id)"
                                        as="button"
                                        method="get"
                                        class="rounded-md bg-blue-900 px-3 py-1 text-sm text-white"
                                    >
                                        Edit
                                    </Link>
                                    <Link
                                        :href="destroy(user.id).url"
                                        preserve-scroll
                                        as="button"
                                        method="delete"
                                        class="rounded-md bg-red-900 px-3 py-1 text-sm text-white"
                                        @click.prevent="confirmDelete(user.id)"
                                    >
                                        Delete
                                    </Link>
                                </td>
                            </tr>
                        </transition-group>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.users-list-enter-active,
.users-list-leave-active {
    transition: all 0.3s ease;
}
.users-list-enter-from {
    opacity: 0;
    transform: translateX(-20px);
}
.users-list-leave-to {
    opacity: 0;
    transform: translateX(20px);
    position: absolute;
}
</style>
