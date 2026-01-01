<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { home } from '@/routes';
import {
    ArrowRight,
    BookOpen,
    Star,
    Clock,
    Server,
    Layout,
    Shield,
    Users,
    Brain,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Course {
    id: number;
    title: string;
    description: string;
    level: string;
    price: number;
    slug: string;
    thumbnail?: string;
    instructor: {
        id: number;
        name: string;
    };
    module_count: number;
    total_lesson_count: number;
}

interface Props {
    featuredCourses?: Course[];
}

const props = defineProps<Props>();

// Use featured courses from props or empty array
const courses = props.featuredCourses || [];

const icons = [Server, Layout, Shield, Users, Brain, BookOpen];

const getCategoryIcon = (index: number) => {
    return icons[index % icons.length];
};

const selectedLevel = ref<string>('');
const sortBy = ref<string>('Newest');

const selectLevel = (level: string) => {
    selectedLevel.value = selectedLevel.value === level ? '' : level;
};

const filterCourses = () => {
    return courses.filter((course) => {
        // Filter by level - convert to lowercase for comparison
        const courseLevel = course.level.toLowerCase();
        const selectedLevelLower = selectedLevel.value.toLowerCase();
        if (selectedLevel.value && courseLevel !== selectedLevelLower) {
            return false;
        }

        return true;
    }).sort((a, b) => {
        // Sort courses based on sortBy selection
        switch (sortBy.value) {
            case 'Most Popular':
                // Assuming price as popularity indicator (you might want to use actual popularity data)
                return b.price - a.price;
            case 'Price: Low to High':
                return a.price - b.price;
            case 'Newest':
            default:
                return b.id - a.id; // Assuming higher ID = newer
        }
    });
};

const formatPrice = (price: number) => {
    return price === 0 ? 'Free' : `$${price}`;
};

const getLevelColor = (level: string) => {
    switch (level) {
        case 'beginner':
            return 'bg-green-100 text-green-800';
        case 'intermediate':
            return 'bg-blue-100 text-blue-800';
        case 'advanced':
            return 'bg-purple-100 text-purple-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

// Import route helpers
const courseRoutes = {
    catalog: () => ({ url: '/courses' }),
    detail: (params: { course: string }) => ({ url: `/courses/${params.course}` }),
};
</script>

<template>
    <Head title="CodeRept | Modern Learning Platform" />
    <AppLayout>
        <!-- Hero -->
        <div
            class="relative overflow-hidden border-b border-gray-200 bg-white pt-12 pb-16 md:pt-24 md:pb-20"
        >
            <div class="mx-auto max-w-7xl px-6 text-center">
                <div
                    class="mb-8 inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600"
                >
                    <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                    New Course: Advanced System Design
                </div>
                <h1
                    class="mx-auto mb-6 max-w-4xl text-4xl font-semibold tracking-tight text-gray-900 md:text-6xl"
                >
                    Master the art of modern software engineering.
                </h1>
                <p
                    class="mx-auto mb-10 max-w-2xl text-base leading-relaxed text-gray-700 md:text-lg"
                >
                    CodeRept provides industry-standard curriculum for
                    developers who want to ship better code, faster. Join
                    50,000+ engineers.
                </p>
                <div class="flex flex-col justify-center gap-4 sm:flex-row">
                    <Link
                        :href="courseRoutes.catalog().url"
                        class="flex items-center justify-center gap-2 rounded-lg bg-gray-900 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all hover:bg-gray-800"
                    >
                        Browse Courses
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <Link
                        :href="courseRoutes.catalog().url"
                        class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-800 transition-all hover:bg-gray-50"
                    >
                        View All Courses
                    </Link>
                </div>
            </div>
        </div>

        <!-- Catalog Section -->
        <div class="mx-auto max-w-7xl px-6 py-12 md:py-16">
            <div class="flex flex-col gap-8 md:flex-row md:gap-12">
                <!-- Sidebar Filters (Horizontal on mobile) -->
                <div class="w-full flex-shrink-0 space-y-8 md:w-64">
                    <!-- Hidden on very small screens to save space or collapsible if needed, kept simple for now -->
                    <div class="hidden sm:block">
                        <h3
                            class="mb-4 text-sm font-semibold tracking-tight text-gray-900"
                        >
                            Level
                        </h3>
                        <div
                            class="flex flex-wrap gap-x-6 gap-y-2.5 md:flex-col"
                        >
                            <label
                                class="group flex cursor-pointer items-center gap-3"
                            >
                                <div class="relative flex items-center">
                                    <input
                                        type="radio"
                                        name="level"
                                        class="peer h-4 w-4 appearance-none rounded-full border border-gray-300 bg-white transition-all checked:border-4 checked:border-gray-900"
                                        value="Beginner"
                                        :checked="selectedLevel === 'Beginner'"
                                        @change="selectLevel('Beginner')"
                                    />
                                </div>
                                <span
                                    class="text-sm text-gray-600 group-hover:text-gray-900"
                                >
                                    Beginner
                                </span>
                            </label>
                            <label
                                class="group flex cursor-pointer items-center gap-3"
                            >
                                <div class="relative flex items-center">
                                    <input
                                        type="radio"
                                        name="level"
                                        class="peer h-4 w-4 appearance-none rounded-full border border-gray-300 bg-white transition-all checked:border-4 checked:border-gray-900"
                                        value="Intermediate"
                                        :checked="
                                            selectedLevel === 'Intermediate'
                                        "
                                        @change="selectLevel('Intermediate')"
                                    />
                                </div>
                                <span
                                    class="text-sm text-gray-600 group-hover:text-gray-900"
                                >
                                    Intermediate
                                </span>
                            </label>
                            <label
                                class="group flex cursor-pointer items-center gap-3"
                            >
                                <div class="relative flex items-center">
                                    <input
                                        type="radio"
                                        name="level"
                                        class="peer h-4 w-4 appearance-none rounded-full border border-gray-300 bg-white transition-all checked:border-4 checked:border-gray-900"
                                        value="Advanced"
                                        :checked="selectedLevel === 'Advanced'"
                                        @change="selectLevel('Advanced')"
                                    />
                                </div>
                                <span
                                    class="text-sm text-gray-600 group-hover:text-gray-900"
                                >
                                    Advanced
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Course Grid -->
                <div class="flex-1">
                    <div
                        class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                    >
                        <h2
                            class="text-xl font-semibold tracking-tight text-gray-900"
                        >
                            Featured Courses
                        </h2>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-gray-500"
                                >Sort by:</span
                            >
                            <select
                                v-model="sortBy"
                                class="cursor-pointer border-none bg-transparent text-xs font-medium text-gray-900 focus:ring-0"
                            >
                                <option value="Most Popular">Most Popular</option>
                                <option value="Newest">Newest</option>
                                <option value="Price: Low to High">Price: Low to High</option>
                            </select>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <!-- Course Card -->
                        <Link
                            v-for="course in filterCourses()"
                            :key="course.id"
                            :href="courseRoutes.detail({ course: course.slug }).url"
                            class="group cursor-pointer overflow-hidden rounded-xl border border-gray-300 bg-white transition-all hover:border-gray-300 hover:shadow-md"
                        >
                            <div class="relative h-40 bg-gradient-to-br from-gray-100 to-gray-200">
                                <div
                                    v-if="course.thumbnail"
                                    class="absolute inset-0 bg-cover bg-center"
                                    :style="{ backgroundImage: `url(${course.thumbnail})` }"
                                ></div>
                                <div
                                    v-else
                                    class="flex h-full items-center justify-center"
                                >
                                    <component
                                        :is="getCategoryIcon(course.id)"
                                        class="h-10 w-10 text-gray-400 transition-transform duration-300 group-hover:scale-110"
                                    />
                                </div>
                                <div
                                    class="absolute top-3 right-3 rounded border border-gray-100 bg-white/90 px-2 py-1 text-[10px] font-semibold tracking-wide text-gray-900 uppercase backdrop-blur"
                                    :class="getLevelColor(course.level)"
                                >
                                    {{ course.level.charAt(0).toUpperCase() + course.level.slice(1) }}
                                </div>
                            </div>
                            <div class="p-5">
                                <h3
                                    class="mb-1 text-base font-semibold text-gray-900 transition-colors group-hover:text-blue-600"
                                >
                                    {{ course.title }}
                                </h3>
                                <p
                                    class="mb-4 line-clamp-2 text-sm text-gray-600"
                                >
                                    {{ course.description }}
                                </p>
                                <div
                                    class="mb-4 flex flex-wrap gap-3 text-xs text-gray-500"
                                >
                                    <div class="flex items-center gap-1">
                                        <BookOpen class="h-3.5 w-3.5" />
                                        <span>{{ course.module_count }} modules</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Clock class="h-3.5 w-3.5" />
                                        <span>{{ course.total_lesson_count }} lessons</span>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center justify-between border-t border-gray-100 pt-4"
                                >
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-[10px] font-bold text-gray-600"
                                        >
                                            {{
                                                course.instructor.name
                                                    .split(' ')
                                                    .map((n: string) => n[0])
                                                    .join('')
                                                    .toUpperCase()
                                            }}
                                        </div>
                                        <span
                                            class="text-xs font-medium text-gray-600"
                                        >
                                            {{ course.instructor.name }}
                                        </span>
                                    </div>
                                    <span
                                        class="text-lg font-bold text-gray-900"
                                    >
                                        {{ formatPrice(course.price) }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
