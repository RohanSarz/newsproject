<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Calendar,
    Clock,
    BookOpen,
    Lock,
    ChevronRight,
} from 'lucide-vue-next';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Lesson {
    id: number;
    title: string;
    published_at: string;
}

interface Module {
    id: number;
    title: string;
    published_at: string;
}

interface Course {
    id: number;
    title: string;
    slug: string;
}

interface UpcomingLesson {
    lesson: Lesson;
    module: Module;
    course: Course;
    published_at: string;
    days_until: number;
}

interface UpcomingModule {
    module: Module;
    course: Course;
    published_at: string;
    days_until: number;
}

interface Props {
    upcomingLessons: UpcomingLesson[];
    upcomingModules: UpcomingModule[];
}

const { props } = usePage<Props>();
const { upcomingLessons, upcomingModules } = props;

// Route helpers
const studentRoutes = {
    coursesIndex: () => ({ url: '/student/courses' }),
};

const groupedByTime = computed(() => {
    const groups: Record<string, (UpcomingLesson | UpcomingModule)[]> = {
        'Today': [],
        'Tomorrow': [],
        'This Week': [],
        'Later': [],
    };

    // Process upcoming lessons
    upcomingLessons.forEach((item) => {
        const days = item.days_until;
        if (days === 0) {
            groups['Today'].push(item);
        } else if (days === 1) {
            groups['Tomorrow'].push(item);
        } else if (days <= 7) {
            groups['This Week'].push(item);
        } else {
            groups['Later'].push(item);
        }
    });

    // Process upcoming modules
    upcomingModules.forEach((item) => {
        const days = item.days_until;
        if (days === 0) {
            groups['Today'].push(item);
        } else if (days === 1) {
            groups['Tomorrow'].push(item);
        } else if (days <= 7) {
            groups['This Week'].push(item);
        } else {
            groups['Later'].push(item);
        }
    });

    return groups;
});

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getTimeUntil = (dateString: string) => {
    const now = new Date();
    const date = new Date(dateString);
    const diff = date.getTime() - now.getTime();
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

    if (days === 0 && hours === 0) {
        return 'Coming soon';
    } else if (days === 0) {
        return `in ${hours} ${hours === 1 ? 'hour' : 'hours'}`;
    } else if (days < 7) {
        return `in ${days} ${days === 1 ? 'day' : 'days'}`;
    } else {
        return formatDate(dateString);
    }
};
</script>

<template>
    <Head title="Upcoming Lessons | CodeRept" />
    <StudentLayout>
        <div class="mx-auto max-w-5xl px-6 py-8 md:py-12">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
                    Upcoming Lessons
                </h1>
                <p class="mt-2 text-gray-600">
                    Track your scheduled content and upcoming releases
                </p>
            </div>

            <!-- Content -->
            <div v-if="upcomingLessons.length > 0 || upcomingModules.length > 0" class="space-y-8">
                <!-- Today -->
                <div v-if="groupedByTime['Today'].length > 0">
                    <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <Calendar class="h-5 w-5" />
                        Today
                    </h2>
                    <div class="space-y-3">
                        <Card
                            v-for="item in groupedByTime['Today']"
                            :key="item.lesson ? item.lesson.id : item.module.id"
                            class="border-orange-200 bg-orange-50/50"
                        >
                            <CardContent class="flex items-center justify-between p-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100">
                                        <Lock class="h-5 w-5 text-orange-600" />
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">
                                            {{ item.lesson ? item.lesson.title : item.module.title }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ item.course.title }} • {{ item.lesson ? item.module.title : 'Module' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ formatDate(item.published_at) }}
                                    </div>
                                    <Badge variant="outline" class="mt-1">
                                        Today
                                    </Badge>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Tomorrow -->
                <div v-if="groupedByTime['Tomorrow'].length > 0">
                    <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <Calendar class="h-5 w-5" />
                        Tomorrow
                    </h2>
                    <div class="space-y-3">
                        <Card
                            v-for="item in groupedByTime['Tomorrow']"
                            :key="item.lesson ? item.lesson.id : item.module.id"
                        >
                            <CardContent class="flex items-center justify-between p-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                                        <Lock class="h-5 w-5 text-blue-600" />
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">
                                            {{ item.lesson ? item.lesson.title : item.module.title }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ item.course.title }} • {{ item.lesson ? item.module.title : 'Module' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">
                                        Tomorrow
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ new Date(item.published_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- This Week -->
                <div v-if="groupedByTime['This Week'].length > 0">
                    <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <Calendar class="h-5 w-5" />
                        This Week
                    </h2>
                    <div class="space-y-3">
                        <Card
                            v-for="item in groupedByTime['This Week']"
                            :key="item.lesson ? item.lesson.id : item.module.id"
                        >
                            <CardContent class="flex items-center justify-between p-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                                        <Lock class="h-5 w-5 text-gray-600" />
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">
                                            {{ item.lesson ? item.lesson.title : item.module.title }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ item.course.title }} • {{ item.lesson ? item.module.title : 'Module' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ getTimeUntil(item.published_at) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ formatDate(item.published_at) }}
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Later -->
                <div v-if="groupedByTime['Later'].length > 0">
                    <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <Calendar class="h-5 w-5" />
                        Coming Later
                    </h2>
                    <div class="space-y-3">
                        <Card
                            v-for="item in groupedByTime['Later']"
                            :key="item.lesson ? item.lesson.id : item.module.id"
                        >
                            <CardContent class="flex items-center justify-between p-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                                        <Lock class="h-5 w-5 text-gray-600" />
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">
                                            {{ item.lesson ? item.lesson.title : item.module.title }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ item.course.title }} • {{ item.lesson ? item.module.title : 'Module' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ formatDate(item.published_at) }}
                                    </div>
                                    <Badge variant="outline" class="mt-1">
                                        {{ item.days_until }} days
                                    </Badge>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="flex flex-col items-center rounded-lg border border-dashed border-gray-300 bg-gray-50 py-16 text-center"
            >
                <Calendar class="mb-4 h-16 w-16 text-gray-400" />
                <h3 class="text-lg font-medium text-gray-900">
                    No upcoming content
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    All available content is ready for you to learn!
                </p>
                <Link
                    :href="studentRoutes.coursesIndex().url"
                    class="mt-6"
                >
                    <Button>View My Courses</Button>
                </Link>
            </div>

            <!-- Info Box -->
            <Card class="mt-8 border-blue-200 bg-blue-50/50">
                <CardContent class="flex items-start gap-3 p-4">
                    <Clock class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600" />
                    <div>
                        <h4 class="font-semibold text-gray-900">
                            About Scheduled Content
                        </h4>
                        <p class="mt-1 text-sm text-gray-600">
                            Lessons and modules are released automatically on their scheduled date and time.
                            You'll see upcoming content here, and they'll become available in your
                            course as soon as they're published.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </StudentLayout>
</template>
