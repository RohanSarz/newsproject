<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    Award,
    Book,
    CheckCircle,
    Clock,
    MessageSquare,
    MoreHorizontal,
    TrendingUp,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Course {
    id: number;
    title: string;
    module: string;
    progress: number;
    category: string;
    color: string;
    instructor: string;
    initials: string;
}

interface Activity {
    id: number;
    type: 'completed' | 'comment' | 'notification';
    title: string;
    description: string;
    time: string;
    score?: string;
}

interface Announcement {
    id: number;
    title: string;
    content: string;
    isNew: boolean;
}

interface Stat {
    title: string;
    value: string;
    icon: any;
    color: string;
    percentage?: string;
    positive?: boolean;
}

const studentName = 'Jordan';
const hoursSpent = '24.5h';
const coursesInProgress = 3;
const certificates = 12;

const stats: Stat[] = [
    {
        title: 'Hours Spent',
        value: hoursSpent,
        icon: Clock,
        color: 'blue',
        percentage: '65%',
        positive: true,
    },
    {
        title: 'Courses in Progress',
        value: coursesInProgress.toString(),
        icon: Book,
        color: 'purple',
        percentage: '+1 new this month',
        positive: true,
    },
    {
        title: 'Certificates',
        value: certificates.toString(),
        icon: Award,
        color: 'green',
    },
];

const courses: Course[] = [
    {
        id: 1,
        title: 'Scalable Microservices',
        module: 'Module 4: Distributed Tracing',
        progress: 45,
        category: 'Backend Systems',
        color: 'gray-100',
        instructor: 'J. Doe',
        initials: 'JD',
    },
    {
        id: 2,
        title: 'Advanced React Patterns',
        module: 'Module 3: State Management',
        progress: 75,
        category: 'Frontend',
        color: 'blue-100',
        instructor: 'A. Smith',
        initials: 'AS',
    },
    {
        id: 3,
        title: 'PostgreSQL Mastery',
        module: 'Module 2: Query Optimization',
        progress: 30,
        category: 'Database',
        color: 'green-100',
        instructor: 'B. Johnson',
        initials: 'BJ',
    },
];

const activities: Activity[] = [
    {
        id: 1,
        type: 'completed',
        title: 'Completed Quiz: Docker Basics',
        description: 'Score: 90%',
        time: '2 hours ago',
        score: '90%',
    },
    {
        id: 2,
        type: 'comment',
        title: 'Instructor replied to your comment',
        description: 'Regarding microservice communication patterns',
        time: 'Yesterday',
    },
    {
        id: 3,
        type: 'notification',
        title: 'New assignment available',
        description: 'Complete the system design challenge',
        time: '3 days ago',
    },
];

const announcements: Announcement[] = [
    {
        id: 1,
        title: 'Maintenance Scheduled',
        content: 'Platform will be down for 30 mins this Sunday at 2 AM UTC.',
        isNew: true,
    },
    {
        id: 2,
        title: 'Community Hackathon',
        content: 'Join us for the CodeRept winter hackathon. Prizes worth $5k.',
        isNew: false,
    },
];

//const currentCourse = ref(courses[0]);
const selectedCourse = ref(courses[0]);
</script>

<template>
    <Head title="Student Dashboard" />
    <StudentLayout>
        <!-- Dashboard Content -->
        <main class="p-6 md:p-8">
            <header
                class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center"
            >
                <div>
                    <h1
                        class="text-2xl font-semibold tracking-tight text-gray-900"
                    >
                        Welcome back, {{ studentName }}
                    </h1>
                    <p class="text-sm text-gray-500">
                        You've completed 3 lessons this week. Keep it up!
                    </p>
                </div>
                <div class="flex items-center gap-4 self-start md:self-center">
                    <button
                        class="relative p-2 text-gray-400 transition-colors hover:text-gray-600"
                    >
                        <Bell class="h-5 w-5" />
                        <span
                            class="absolute top-2 right-2 h-2 w-2 rounded-full border-2 border-white bg-red-500"
                        ></span>
                    </button>
                    <div
                        class="h-8 w-8 rounded-full border border-white bg-gradient-to-r from-blue-500 to-purple-500 shadow-sm"
                    ></div>
                </div>
            </header>

            <!-- Stats Row -->
            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div
                    v-for="stat in stats"
                    :key="stat.title"
                    class="rounded-xl border border-gray-300 bg-white p-5 shadow-sm"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-medium text-gray-500 uppercase"
                            >
                                {{ stat.title }}
                            </p>
                            <h3
                                class="mt-1 text-2xl font-semibold tracking-tight text-gray-900"
                            >
                                {{ stat.value }}
                            </h3>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-2 text-blue-600">
                            <component :is="stat.icon" class="h-4 w-4" />
                        </div>
                    </div>
                    <div
                        v-if="stat.percentage"
                        class="flex items-center gap-1 text-xs"
                        :class="
                            stat.positive ? 'text-green-600' : 'text-red-600'
                        "
                    >
                        <TrendingUp v-if="stat.positive" class="h-3 w-3" />
                        <div v-else class="h-3 w-3"></div>
                        {{ stat.percentage }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Current Course -->
                <div class="lg:col-span-2">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">
                        Continue Learning
                    </h2>
                    <div
                        class="flex flex-col items-start gap-6 rounded-xl border border-gray-200 bg-white p-6 sm:flex-row"
                    >
                        <div
                            class="flex aspect-video w-full flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400 sm:w-48"
                        >
                            <div class="h-8 w-8 rounded bg-gray-300"></div>
                        </div>
                        <div class="w-full flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        {{ selectedCourse.title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ selectedCourse.module }}
                                    </p>
                                </div>
                                <button
                                    class="text-gray-400 hover:text-gray-900"
                                >
                                    <MoreHorizontal class="h-5 w-5" />
                                </button>
                            </div>

                            <div class="mt-6">
                                <div
                                    class="mb-2 flex justify-between text-xs font-medium text-gray-500"
                                >
                                    <span>Progress</span>
                                    <span>{{ selectedCourse.progress }}%</span>
                                </div>
                                <div
                                    class="h-2 w-full rounded-full bg-gray-100"
                                >
                                    <div
                                        class="h-2 rounded-full bg-gray-900"
                                        :style="{
                                            width:
                                                selectedCourse.progress + '%',
                                        }"
                                    ></div>
                                </div>
                            </div>

                            <div class="mt-6 flex gap-3">
                                <button
                                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                                >
                                    Resume
                                </button>
                            </div>
                        </div>
                    </div>

                    <h2 class="mt-8 mb-4 text-base font-semibold text-gray-900">
                        Recent Activity
                    </h2>
                    <div
                        class="divide-y divide-gray-100 rounded-xl border border-gray-200 bg-white"
                    >
                        <div
                            v-for="activity in activities"
                            :key="activity.id"
                            class="flex items-center gap-4 p-4"
                        >
                            <div
                                class="rounded-lg bg-green-50 p-2 text-green-600"
                                v-if="activity.type === 'completed'"
                            >
                                <CheckCircle class="h-4 w-4" />
                            </div>
                            <div
                                class="rounded-lg bg-blue-50 p-2 text-blue-600"
                                v-else-if="activity.type === 'comment'"
                            >
                                <MessageSquare class="h-4 w-4" />
                            </div>
                            <div
                                class="rounded-lg bg-blue-50 p-2 text-blue-600"
                                v-else
                            >
                                <MessageSquare class="h-4 w-4" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ activity.title }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ activity.time }}
                                </p>
                            </div>
                            <span
                                v-if="activity.score"
                                class="rounded bg-gray-50 px-2 py-1 text-xs font-semibold text-gray-900"
                            >
                                Score: {{ activity.score }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar inside Dashboard -->
                <div class="lg:col-span-1">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">
                        Announcements
                    </h2>
                    <div
                        class="space-y-6 rounded-xl border border-gray-200 bg-white p-5"
                    >
                        <div
                            v-for="announcement in announcements"
                            :key="announcement.id"
                        >
                            <span
                                v-if="announcement.isNew"
                                class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold tracking-wide text-blue-600 uppercase"
                            >
                                New
                            </span>
                            <h4 class="mt-2 text-sm font-medium text-gray-900">
                                {{ announcement.title }}
                            </h4>
                            <p
                                class="mt-1 text-xs leading-relaxed text-gray-500"
                            >
                                {{ announcement.content }}
                            </p>
                        </div>
                    </div>

                    <h2 class="mt-8 mb-4 text-base font-semibold text-gray-900">
                        Upcoming Courses
                    </h2>
                    <div class="space-y-4">
                        <div
                            v-for="course in courses.slice(1)"
                            :key="course.id"
                            class="flex items-center gap-3 rounded-lg border border-gray-200 p-4"
                        >
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-600"
                            >
                                {{ course.initials }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p
                                    class="truncate text-sm font-medium text-gray-900"
                                >
                                    {{ course.title }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ course.category }}
                                </p>
                            </div>
                            <div class="text-xs font-medium text-gray-900">
                                {{ course.progress }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </StudentLayout>
</template>

<style scoped>
/* Custom scrollbar for sidebar */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
