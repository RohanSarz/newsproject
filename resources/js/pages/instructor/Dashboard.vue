<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import {
    ArrowDownRight,
    ArrowUpRight,
    BarChart2,
    CheckCircle,
    DollarSign,
    Download,
    MessageSquare,
    Users,
    BookOpen,
} from 'lucide-vue-next';

interface Stat {
    title: string;
    value: string;
    change: string;
    positive: boolean;
}

interface Enrollment {
    id: number;
    name: string;
    course: string;
    initials: string;
    color: string;
    amount: string;
    enrolled_at: string;
}

interface CoursePerformance {
    title: string;
    enrollments: number;
    completion_rate: number;
    avg_rating: number;
    revenue: number;
}

interface Props {
    stats: Stat[];
    recentEnrollments: Enrollment[];
    coursePerformance: CoursePerformance[];
}

const { props } = usePage<Props>();
const { stats, recentEnrollments, coursePerformance } = props;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/instructor/dashboard',
    },
];
</script>

<template>
    <Head title="Instructor Dashboard | CodeRept" />
    <InstructorLayout :breadcrumbs="breadcrumbs">
        <main class="p-6 md:p-8">
            <header
                class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center"
            >
                <div>
                    <h1
                        class="text-2xl font-semibold tracking-tight text-gray-900"
                    >
                        Course Analytics
                    </h1>
                    <p class="text-sm text-gray-500">
                        Overview of your performance across all courses.
                    </p>
                </div>
                <button
                    class="flex items-center gap-2 self-start rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 md:self-auto"
                >
                    <Download class="h-4 w-4" />
                    Export Report
                </button>
            </header>

            <!-- Stats -->
            <div
                class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4"
            >
                <div
                    v-for="(stat, index) in stats"
                    :key="index"
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
                >
                    <p class="mb-2 text-xs font-medium text-gray-500 uppercase">
                        {{ stat.title }}
                    </p>
                    <h3
                        class="text-3xl font-semibold tracking-tight text-gray-900"
                    >
                        {{ stat.value }}
                    </h3>
                    <p
                        :class="[
                            'mt-2 flex items-center gap-1 text-xs',
                            stat.positive ? 'text-green-600' : 'text-red-600',
                        ]"
                    >
                        <ArrowUpRight v-if="stat.positive" class="h-3 w-3" />
                        <ArrowDownRight v-else class="h-3 w-3" />
                        {{ stat.change }}
                    </p>
                </div>
            </div>

            <!-- Recent Enrollments & Quick Stats -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2"
                >
                    <h3 class="mb-6 text-base font-semibold text-gray-900">
                        Course Performance
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr
                                    class="border-b border-gray-200 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                >
                                    <th class="px-4 pb-3">Course</th>
                                    <th class="px-4 pb-3">Enrollments</th>
                                    <th class="px-4 pb-3">Completion</th>
                                    <th class="px-4 pb-3">Avg. Rating</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-if="coursePerformance.length > 0"
                                    v-for="(course, index) in coursePerformance"
                                    :key="index"
                                    class="text-sm"
                                >
                                    <td class="px-4 py-4 font-medium text-gray-900">
                                        {{ course.title }}
                                    </td>
                                    <td class="px-4 py-4">
                                        {{ course.enrollments }}
                                    </td>
                                    <td class="px-4 py-4">
                                        {{ course.completion_rate }}%
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-1">
                                            <span v-if="course.avg_rating > 0">{{ course.avg_rating.toFixed(1) }}</span>
                                            <span v-else class="text-gray-400">—</span>
                                            <MessageSquare
                                                v-if="course.avg_rating > 0"
                                                class="h-3 w-3 fill-yellow-500 text-yellow-500"
                                            />
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No courses yet. Start by creating your first course!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white p-0 shadow-sm lg:col-span-1"
                >
                    <div class="border-b border-gray-100 p-5">
                        <h3 class="text-base font-semibold text-gray-900">
                            Recent Enrollments
                        </h3>
                    </div>
                    <div v-if="recentEnrollments.length > 0" class="divide-y divide-gray-100">
                        <div
                            v-for="enrollment in recentEnrollments"
                            :key="enrollment.id"
                            class="flex items-center gap-3 p-4 transition-colors hover:bg-gray-50"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600"
                            >
                                {{ enrollment.initials }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p
                                    class="truncate text-sm font-medium text-gray-900"
                                >
                                    {{ enrollment.name }}
                                </p>
                                <p class="truncate text-xs text-gray-500">
                                    {{ enrollment.course }}
                                </p>
                            </div>
                            <span class="text-xs font-medium text-green-600">{{
                                enrollment.amount
                            }}</span>
                        </div>
                    </div>
                    <div v-else class="p-8 text-center text-sm text-gray-500">
                        No enrollments yet
                    </div>
                </div>
            </div>
        </main>
    </InstructorLayout>
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
