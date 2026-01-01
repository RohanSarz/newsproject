<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { store } from '@/routes/instructor/courses';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface FormData {
    title: string;
    description: string;
    overview: string;
    level: string;
    price: string;
    status: string;
    publish_now: boolean;
    published_at: string;
}

const formData = ref<FormData>({
    title: '',
    description: '',
    overview: '',
    level: 'beginner',
    price: '0.00',
    status: 'draft',
    publish_now: false,
    published_at: '',
});

const errors = ref<Record<string, string>>({});

const submitForm = () => {
    errors.value = {};

    router.post(
        store().url,
        {
            ...formData.value,
            price: parseFloat(formData.value.price),
        },
        {
            onError: (errs) => {
                errors.value = errs;
            },
            onSuccess: () => {
                // Redirect to courses index on success
                router.visit('/instructor/courses');
            },
        },
    );
};
</script>

<template>
    <InstructorLayout
        :breadcrumbs="[
            { title: 'Home', href: '/' },
            { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
            { title: 'Courses', href: '/instructor/courses' },
            { title: 'Create', href: '/instructor/courses/create' },
        ]"
    >
        <Head title="Create Course" />

        <div class="mx-auto max-w-3xl space-y-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Create New Course
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Add a new course to your catalog
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Course Details</CardTitle>
                    <CardDescription>
                        Fill in the information for your new course
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-2">
                        <Label for="title">Course Title *</Label>
                        <Input
                            id="title"
                            v-model="formData.title"
                            placeholder="Enter course title"
                            :class="{ 'border-red-500': errors.title }"
                        />
                        <p v-if="errors.title" class="text-sm text-red-500">
                            {{ errors.title }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <Textarea
                            id="description"
                            v-model="formData.description"
                            placeholder="Brief description of the course"
                            :class="{ 'border-red-500': errors.description }"
                        />
                        <p
                            v-if="errors.description"
                            class="text-sm text-red-500"
                        >
                            {{ errors.description }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="overview">Overview</Label>
                        <Textarea
                            id="overview"
                            v-model="formData.overview"
                            placeholder="Detailed overview of the course content"
                            rows="5"
                            :class="{ 'border-red-500': errors.overview }"
                        />
                        <p v-if="errors.overview" class="text-sm text-red-500">
                            {{ errors.overview }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="level">Level *</Label>
                            <Select
                                v-model="formData.level"
                                :class="{ 'border-red-500': errors.level }"
                            >
                                <SelectTrigger id="level">
                                    <SelectValue placeholder="Select level" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="beginner"
                                        >Beginner</SelectItem
                                    >
                                    <SelectItem value="intermediate"
                                        >Intermediate</SelectItem
                                    >
                                    <SelectItem value="advanced"
                                        >Advanced</SelectItem
                                    >
                                </SelectContent>
                            </Select>
                            <p v-if="errors.level" class="text-sm text-red-500">
                                {{ errors.level }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="price">Price ($)</Label>
                            <Input
                                id="price"
                                v-model="formData.price"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                :class="{ 'border-red-500': errors.price }"
                            />
                            <p v-if="errors.price" class="text-sm text-red-500">
                                {{ errors.price }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="status">Status</Label>
                        <Select
                            v-model="formData.status"
                            :class="{ 'border-red-500': errors.status }"
                        >
                            <SelectTrigger id="status">
                                <SelectValue placeholder="Select status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="draft">Draft</SelectItem>
                                <SelectItem value="published"
                                    >Published</SelectItem
                                >
                                <SelectItem value="archived"
                                    >Archived</SelectItem
                                >
                            </SelectContent>
                        </Select>
                        <p v-if="errors.status" class="text-sm text-red-500">
                            {{ errors.status }}
                        </p>
                    </div>

                    <!-- Publishing Options -->
                    <div
                        v-if="formData.status === 'published'"
                        class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <h3 class="font-medium">Publishing Options</h3>

                        <!-- Publish Now -->
                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="publish_now"
                                :checked="formData.publish_now"
                                @update:checked="
                                    formData.publish_now = $event
                                "
                            />
                            <Label for="publish_now" class="cursor-pointer">
                                Publish immediately
                            </Label>
                        </div>

                        <!-- Scheduled Publishing Date -->
                        <div
                            v-if="formData.status === 'published' && !formData.publish_now"
                            class="space-y-2"
                        >
                            <Label for="published_at"
                                >Schedule Publishing Date</Label
                            >
                            <Input
                                id="published_at"
                                v-model="formData.published_at"
                                type="datetime-local"
                                :class="{
                                    'border-red-500': errors.published_at,
                                }"
                            />
                            <p class="text-xs text-muted-foreground">
                                Students will see this course but content will be
                                locked until this date
                            </p>
                            <p
                                v-if="errors.published_at"
                                class="text-sm text-red-500"
                            >
                                {{ errors.published_at }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <Button variant="outline" as-child>
                            <a href="/instructor/courses">Cancel</a>
                        </Button>
                        <Button @click="submitForm">Create Course</Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </InstructorLayout>
</template>
