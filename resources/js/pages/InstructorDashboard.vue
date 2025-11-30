<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { dashboard } from '@/routes';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import {
    ArrowDownRight,
    ArrowUpRight,
    BarChart2,
    CheckCircle,
    DollarSign,
    Download,
    MessageSquare,
    Users,
} from 'lucide-vue-next';

interface Stat {
    title: string;
    value: string;
    change: string;
    positive: boolean;
    icon: any;
}

interface Enrollment {
    id: number;
    name: string;
    course: string;
    initials: string;
    color: string;
    amount: string;
}

interface RevenueBar {
    month: string;
    value: number;
    isCurrent: boolean;
}

const stats: Stat[] = [
    {
        title: 'Total Revenue',
        value: '$12,450',
        change: '+12.5%',
        positive: true,
        icon: DollarSign,
    },
    {
        title: 'Active Students',
        value: '1,204',
        change: '+3.2%',
        positive: true,
        icon: Users,
    },
    {
        title: 'Course Rating',
        value: '4.8',
        change: 'Average of 450 reviews',
        positive: true,
        icon: BarChart2,
    },
    {
        title: 'Completion Rate',
        value: '68%',
        change: '-1.2%',
        positive: false,
        icon: CheckCircle,
    },
];

const revenueData: RevenueBar[] = [
    { month: 'May', value: 40, isCurrent: false },
    { month: 'Jun', value: 60, isCurrent: false },
    { month: 'Jul', value: 35, isCurrent: false },
    { month: 'Aug', value: 80, isCurrent: false },
    { month: 'Sep', value: 55, isCurrent: false },
    { month: 'Oct', value: 90, isCurrent: true },
];

const recentEnrollments: Enrollment[] = [
    {
        id: 1,
        name: 'John Doe',
        course: 'Scalable Microservices',
        initials: 'JD',
        color: 'purple',
        amount: '+$49',
    },
    {
        id: 2,
        name: 'Alice Smith',
        course: 'React Patterns',
        initials: 'AS',
        color: 'blue',
        amount: '+$59',
    },
    {
        id: 3,
        name: 'Ben Ross',
        course: 'PostgreSQL Mastery',
        initials: 'BR',
        color: 'yellow',
        amount: '+$39',
    },
];

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
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

            <!-- Visualization Mockup & List -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2"
                >
                    <h3 class="mb-6 text-base font-semibold text-gray-900">
                        Revenue History
                    </h3>
                    <!-- Simple CSS Bar Chart -->
                    <div class="mt-4 flex h-48 items-end gap-2 md:gap-4">
                        <div
                            v-for="(bar, index) in revenueData"
                            :key="index"
                            class="group relative flex-1 rounded-t-sm bg-gray-100"
                        >
                            <div
                                :class="[
                                    'absolute bottom-0 w-full rounded-t-sm transition-all group-hover:bg-blue-600',
                                    bar.isCurrent
                                        ? 'bg-gray-900'
                                        : 'bg-blue-500',
                                ]"
                                :style="{ height: bar.value + '%' }"
                            ></div>
                        </div>
                    </div>
                    <div
                        class="mt-3 flex justify-between text-xs font-medium text-gray-400 uppercase"
                    >
                        <span>May</span>
                        <span>Jun</span>
                        <span>Jul</span>
                        <span>Aug</span>
                        <span>Sep</span>
                        <span>Oct</span>
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
                    <div class="divide-y divide-gray-100">
                        <div
                            v-for="enrollment in recentEnrollments"
                            :key="enrollment.id"
                            class="flex items-center gap-3 p-4 transition-colors hover:bg-gray-50"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full"
                                :class="`bg-${enrollment.color}-100 text-xs font-bold text-${enrollment.color}-600`"
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
                    <div
                        class="border-t border-gray-100 bg-gray-50 p-3 text-center"
                    >
                        <button
                            class="text-xs font-medium text-gray-600 hover:text-gray-900"
                        >
                            View all transactions
                        </button>
                    </div>
                </div>
            </div>

            <!-- Course Performance Section -->
            <div
                class="mt-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
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
                                <th class="px-4 pb-3">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="i in 5" :key="i" class="text-sm">
                                <td class="px-4 py-4 font-medium text-gray-900">
                                    Course Title {{ i }}
                                </td>
                                <td class="px-4 py-4">
                                    {{ 100 + i * 25 }}
                                </td>
                                <td class="px-4 py-4">{{ 65 + i * 5 }}%</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-1">
                                        <span>4.{{ 5 + i }}</span>
                                        <MessageSquare
                                            class="h-3 w-3 fill-yellow-500 text-yellow-500"
                                        />
                                    </div>
                                </td>
                                <td class="px-4 py-4">${{ 1500 + i * 250 }}</td>
                            </tr>
                        </tbody>
                    </table>
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
