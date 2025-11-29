<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { update } from '@/routes/password';
import { index, store } from '@/routes/users';
import { User, type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    user: User;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Edit the user',
        href: '/users/create',
    },
];
const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    errors: {},
});

function submit() {
    form.post(update().url, {
        onSuccess: () => {
            // Use Inertia visit instead of window.location.href to prevent full page refresh
            router.visit(index().url, {
                preserveScroll: true,
            });
        },
        onError: (errors) => {
            form.errors.name = errors.email;
        },
    });
}
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div></div>
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
                        <h1>User Create Form</h1>
                        <Button
                            :as="Link"
                            :href="index().url"
                            class="hover:bg-accent hover:text-foreground"
                            >Back</Button
                        >
                    </div>
                    <div class="px-4 py-8">
                        <form
                            class="mx-auto flex max-w-md flex-col gap-6 rounded border px-2 py-4"
                            @submit.prevent="submit()"
                        >
                            <label for="name">Email</label>
                            <input
                                v-model="form.name"
                                class="rounded-2xl border px-2 text-gray-500 caret-gray-400 outline-none"
                                type="name"
                                name="name"
                                id="name"
                                required
                            />
                            <div v-if="form.errors.name" class="text-red-500">
                                {{ form.errors.name }}
                            </div>
                            <label for="Email">Email</label>
                            <input
                                v-model="form.email"
                                class="rounded-2xl border px-2 text-gray-500 caret-gray-400 outline-none"
                                type="email"
                                name="email"
                                id="Email"
                                required
                            />
                            <div v-if="form.errors.email" class="text-red-500">
                                {{ form.errors.email }}
                            </div>

                            <Button
                                type="submit"
                                :href="store().url"
                                class="hover:bg-accent hover:text-foreground"
                                >Save</Button
                            >
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
