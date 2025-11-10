<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, store } from '@/routes/users';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    users: Array,
    errors: Object,
});
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create a new User',
        href: '/users/create',
    },
];
const form = useForm({
    email: '',
    password: '',
    errors: {},
});

function submit() {
    form.post(store().url, {
        onSuccess: () => {
            window.location.href = index().url;
        },
        onError: (errors) => {
            form.errors.email = errors.email;
            form.errors.password = errors.password;
        },
    });
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
                        class="flex items-center justify-between gap-4 bg-slate-200 p-4"
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
                            class="mx-auto flex w-[20rem] flex-col gap-6 rounded border px-2 py-4"
                            :action="store().url"
                            :method="store().method"
                            @submit.prevent="submit()"
                        >
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
                            <label for="password">Password</label>
                            <input
                                v-model="form.password"
                                class="rounded-2xl border px-2 text-gray-500 caret-gray-400 outline-none"
                                type="password"
                                name="password"
                                id="password"
                                required
                            />
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
