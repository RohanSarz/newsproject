<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    ArrowRight,
    Brain,
    Check,
    Database,
    Layout,
    Server,
    Shield,
    Star,
    Users,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Course {
    id: number;
    title: string;
    description: string;
    instructor: string;
    initials: string;
    rating: number;
    category: string;
    icon: any;
    color: string;
    level: 'beginner' | 'intermediate' | 'advanced';
}

const courses: Course[] = [
    {
        id: 1,
        title: 'Scalable Microservices',
        description:
            'Learn to build distributed systems that can handle millions of requests.',
        instructor: 'J. Doe',
        initials: 'JD',
        rating: 4.9,
        category: 'Backend',
        icon: Server,
        color: 'from-gray-100 to-gray-200',
        level: 'intermediate',
    },
    {
        id: 2,
        title: 'Advanced React Patterns',
        description:
            'Master complex React concepts including state management and performance optimization.',
        instructor: 'A. Smith',
        initials: 'AS',
        rating: 4.8,
        category: 'Frontend',
        icon: Layout,
        color: 'from-gray-900 to-gray-800',
        level: 'advanced',
    },
    {
        id: 3,
        title: 'PostgreSQL Mastery',
        description:
            'Deep dive into PostgreSQL for complex data management and optimization.',
        instructor: 'B. Johnson',
        initials: 'BJ',
        rating: 4.7,
        category: 'Database',
        icon: Database,
        color: 'from-blue-100 to-blue-200',
        level: 'intermediate',
    },
    {
        id: 4,
        title: 'Security Best Practices',
        description:
            'Implement robust security measures in your applications and infrastructure.',
        instructor: 'C. Wilson',
        initials: 'CW',
        rating: 4.9,
        category: 'Security',
        icon: Shield,
        color: 'from-green-100 to-green-200',
        level: 'advanced',
    },
    {
        id: 5,
        title: 'Team Leadership',
        description:
            'Develop skills to lead engineering teams and technical projects effectively.',
        instructor: 'D. Brown',
        initials: 'DB',
        rating: 4.6,
        category: 'Leadership',
        icon: Users,
        color: 'from-purple-100 to-purple-200',
        level: 'intermediate',
    },
    {
        id: 6,
        title: 'AI for Developers',
        description:
            'Leverage artificial intelligence to enhance your development workflow.',
        instructor: 'E. Davis',
        initials: 'ED',
        rating: 4.8,
        category: 'AI',
        icon: Brain,
        color: 'from-yellow-100 to-yellow-200',
        level: 'beginner',
    },
];

const selectedTopics = ref<string[]>(['Backend Systems']);
const selectedLevel = ref<string>('Intermediate');
const sortBy = ref<string>('Most Popular');

const toggleTopic = (topic: string) => {
    if (selectedTopics.value.includes(topic)) {
        selectedTopics.value = selectedTopics.value.filter((t) => t !== topic);
    } else {
        selectedTopics.value.push(topic);
    }
};

const selectLevel = (level: string) => {
    selectedLevel.value = level;
};

const filterCourses = () => {
    return courses.filter((course) => {
        // For simplicity in this example, we just return all courses
        // In a real app, we would filter based on selectedTopics and selectedLevel
        return true;
    });
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
                    <button
                        class="flex items-center justify-center gap-2 rounded-lg bg-gray-900 px-6 py-3 text-sm font-medium text-white shadow-sm transition-all hover:bg-gray-800"
                    >
                        Browse Courses
                        <ArrowRight class="h-4 w-4" />
                    </button>
                    <button
                        class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-800 transition-all hover:bg-gray-50"
                    >
                        View Syllabus
                    </button>
                </div>
            </div>
        </div>

        <!-- Catalog Section -->
        <div class="mx-auto max-w-7xl px-6 py-12 md:py-16">
            <div class="flex flex-col gap-8 md:flex-row md:gap-12">
                <!-- Sidebar Filters (Horizontal on mobile) -->
                <div class="w-full flex-shrink-0 space-y-8 md:w-64">
                    <div>
                        <h3
                            class="mb-4 text-sm font-semibold tracking-tight text-gray-900"
                        >
                            Topics
                        </h3>
                        <div
                            class="flex flex-wrap gap-x-6 gap-y-2.5 md:flex-col"
                        >
                            <label
                                class="group flex cursor-pointer items-center gap-3"
                            >
                                <div class="relative flex items-center">
                                    <input
                                        type="checkbox"
                                        class="peer h-4 w-4 appearance-none rounded border border-gray-300 bg-white transition-all checked:border-gray-900 checked:bg-gray-900"
                                        :checked="
                                            selectedTopics.includes(
                                                'Frontend Engineering',
                                            )
                                        "
                                        @change="
                                            toggleTopic('Frontend Engineering')
                                        "
                                    />
                                    <Check
                                        class="pointer-events-none absolute inset-0 m-auto h-3 w-3 text-white opacity-0 peer-checked:opacity-100"
                                    />
                                </div>
                                <span
                                    class="text-sm text-gray-600 group-hover:text-gray-900"
                                >
                                    Frontend Engineering
                                </span>
                            </label>
                            <label
                                class="group flex cursor-pointer items-center gap-3"
                            >
                                <div class="relative flex items-center">
                                    <input
                                        type="checkbox"
                                        class="peer h-4 w-4 appearance-none rounded border border-gray-300 bg-white transition-all checked:border-gray-900 checked:bg-gray-900"
                                        :checked="
                                            selectedTopics.includes(
                                                'Backend Systems',
                                            )
                                        "
                                        @change="toggleTopic('Backend Systems')"
                                    />
                                    <Check
                                        class="pointer-events-none absolute inset-0 m-auto h-3 w-3 text-white opacity-0 peer-checked:opacity-100"
                                    />
                                </div>
                                <span
                                    class="text-sm text-gray-600 group-hover:text-gray-900"
                                >
                                    Backend Systems
                                </span>
                            </label>
                            <label
                                class="group flex cursor-pointer items-center gap-3"
                            >
                                <div class="relative flex items-center">
                                    <input
                                        type="checkbox"
                                        class="peer h-4 w-4 appearance-none rounded border border-gray-300 bg-white transition-all checked:border-gray-900 checked:bg-gray-900"
                                        :checked="
                                            selectedTopics.includes(
                                                'DevOps & Cloud',
                                            )
                                        "
                                        @change="toggleTopic('DevOps & Cloud')"
                                    />
                                    <Check
                                        class="pointer-events-none absolute inset-0 m-auto h-3 w-3 text-white opacity-0 peer-checked:opacity-100"
                                    />
                                </div>
                                <span
                                    class="text-sm text-gray-600 group-hover:text-gray-900"
                                >
                                    DevOps & Cloud
                                </span>
                            </label>
                        </div>
                    </div>
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
                                <option>Most Popular</option>
                                <option>Newest</option>
                                <option>Price: Low to High</option>
                            </select>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <!-- Course Card -->
                        <div
                            v-for="course in filterCourses()"
                            :key="course.id"
                            class="group cursor-pointer overflow-hidden rounded-xl border border-gray-300 bg-white transition-all hover:border-gray-300 hover:shadow-md"
                        >
                            <div
                                class="relative flex h-40 items-center justify-center bg-gradient-to-br"
                                :class="course.color"
                            >
                                <div
                                    class="absolute top-3 right-3 rounded border border-gray-100 bg-white/90 px-2 py-1 text-[10px] font-semibold tracking-wide text-gray-900 uppercase backdrop-blur"
                                >
                                    {{ course.category }}
                                </div>
                                <component
                                    :is="course.icon"
                                    class="h-10 w-10 text-gray-400 transition-transform duration-300 group-hover:scale-110"
                                />
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
                                    class="flex items-center justify-between border-t border-gray-100 pt-4"
                                >
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-[10px] font-bold text-gray-600"
                                        >
                                            {{ course.initials }}
                                        </div>
                                        <span
                                            class="text-xs font-medium text-gray-600"
                                        >
                                            {{ course.instructor }}
                                        </span>
                                    </div>
                                    <div
                                        class="flex items-center gap-1 text-xs font-medium text-gray-900"
                                    >
                                        <Star
                                            class="h-3 w-3 fill-yellow-500 text-yellow-500"
                                        />
                                        {{ course.rating }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
