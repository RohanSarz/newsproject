<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Award,
    Book,
    CheckCircle,
    Clock,
    Quote,
    TrendingUp,
    PlayCircle,
    Calendar,
    Lock,
} from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Route helpers
const studentRoutes = {
    schedule: () => ({ url: '/student/schedule' }),
    coursesIndex: () => ({ url: '/student/courses' }),
    learn: (params: { course: string }) => ({ url: `/student/courses/${params.course}/learn` }),
};

interface Lesson {
    id: number;
    title: string;
    lesson_progress: Array<{ completed: boolean }>;
}

interface Module {
    id: number;
    title: string;
    lessons: Lesson[];
}

interface Course {
    id: number;
    title: string;
    slug: string;
    level: string;
    thumbnail?: string;
    modules: Module[];
}

interface Enrollment {
    id: number;
    enrolled_at: string;
    progress: number;
    status: string;
    course: Course;
}

interface UpcomingLesson {
    lesson: {
        id: number;
        title: string;
        published_at: string;
    };
    module: {
        id: number;
        title: string;
    };
    course: {
        id: number;
        title: string;
        slug: string;
    };
    published_at: string;
    days_until: number;
}

interface Quote {
    quote: string;
    author: string;
}

interface Props {
    enrollments: Enrollment[];
    stats: {
        totalHoursSpent: number;
        coursesInProgress: number;
        certificates: number;
    };
    upcomingLessons: UpcomingLesson[];
    quote: Quote;
}

const { props } = usePage<Props>();
const { enrollments, stats, upcomingLessons, quote } = props;

const studentName = computed(() => {
    const user = usePage().props.auth as any;
    return user?.user?.name || 'Student';
});

// Get current course to continue learning
const currentCourse = computed(() => {
    return enrollments.find((e) => e.progress < 100 && e.progress > 0) || enrollments[0];
});

const completedThisWeek = computed(() => {
    // Calculate completed lessons this week
    let count = 0;
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);

    enrollments.forEach((enrollment) => {
        if (enrollment.course?.modules) {
            enrollment.course.modules.forEach((module) => {
                if (module.lessons && module.lessons.length > 0) {
                    module.lessons.forEach((lesson) => {
                        if (
                            lesson.lesson_progress &&
                            lesson.lesson_progress[0]?.completed &&
                            lesson.lesson_progress[0]?.completed_at
                        ) {
                            const completedDate = new Date(lesson.lesson_progress[0].completed_at);
                            if (completedDate >= oneWeekAgo) {
                                count++;
                            }
                        }
                    });
                }
            });
        }
    });

    return count;
});

const getTimeUntil = (dateString: string) => {
    const now = new Date();
    const date = new Date(dateString);
    const diff = date.getTime() - now.getTime();
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

    if (days === 0 && hours === 0) {
        return 'Coming soon';
    } else if (days === 0) {
        return `in ${hours}h`;
    } else if (days < 7) {
        return `in ${days}d`;
    } else {
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }
};

const getTotalLessons = (course: Course) => {
    if (!course?.modules) return 0;
    return course.modules.reduce((sum, module) => sum + module.lessons.length, 0);
};

const getCompletedLessons = (course: Course) => {
    if (!course?.modules) return 0;
    return course.modules.reduce((sum, module) => {
        return (
            sum +
            module.lessons.filter(
                (lesson) => lesson.lesson_progress[0]?.completed
            ).length
        );
    }, 0);
};

const getCurrentModule = (enrollment: Enrollment) => {
    if (!enrollment.course?.modules) return 'Introduction';
    // Find the first incomplete module
    for (const module of enrollment.course.modules) {
        if (module.lessons && module.lessons.length > 0) {
            const hasIncompleteLesson = module.lessons.some(
                (lesson) => !lesson.lesson_progress[0]?.completed
            );
            if (hasIncompleteLesson) {
                return module.title;
            }
        }
    }
    return enrollment.course.modules[0]?.title || 'Introduction';
};

const formatHours = (seconds: number) => {
    const hours = Math.floor(seconds / 3600);
    if (hours === 0) return '0h';
    return `${hours}h`;
};
</script>

<template>
    <Head title="Student Dashboard | CodeRept" />
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
                        You've completed {{ completedThisWeek }} lesson{{ completedThisWeek !== 1 ? 's' : '' }} this week.
                        Keep it up!
                    </p>
                </div>
                <div class="flex items-center gap-4 self-start md:self-center">
                    <Link
                        :href="studentRoutes.schedule().url"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                    >
                        View Schedule
                    </Link>
                </div>
            </header>

            <!-- Motivational Quote Section -->
            <Card v-if="quote" class="mb-8 border-blue-200 bg-gradient-to-r from-blue-50 to-purple-50">
                <CardContent class="flex items-start gap-4 p-6">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-white shadow-sm">
                        <Quote class="h-5 w-5 text-blue-600" />
                    </div>
                    <div class="flex-1">
                        <blockquote class="text-lg font-medium text-gray-900">
                            "{{ quote.quote }}"
                        </blockquote>
                        <p class="mt-2 text-sm text-gray-600">
                            — {{ quote.author }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Stats Row -->
            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div
                    class="rounded-xl border border-gray-300 bg-white p-5 shadow-sm"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-medium text-gray-500 uppercase"
                            >
                                Hours Spent
                            </p>
                            <h3
                                class="mt-1 text-2xl font-semibold tracking-tight text-gray-900"
                            >
                                {{ formatHours(stats.totalHoursSpent) }}
                            </h3>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-2 text-blue-600">
                            <Clock class="h-4 w-4" />
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xl border border-gray-300 bg-white p-5 shadow-sm"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-medium text-gray-500 uppercase"
                            >
                                In Progress
                            </p>
                            <h3
                                class="mt-1 text-2xl font-semibold tracking-tight text-gray-900"
                            >
                                {{ stats.coursesInProgress }}
                            </h3>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-2 text-purple-600">
                            <Book class="h-4 w-4" />
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-xl border border-gray-300 bg-white p-5 shadow-sm"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-medium text-gray-500 uppercase"
                            >
                                Certificates
                            </p>
                            <h3
                                class="mt-1 text-2xl font-semibold tracking-tight text-gray-900"
                            >
                                {{ stats.certificates }}
                            </h3>
                        </div>
                        <div class="rounded-lg bg-green-50 p-2 text-green-600">
                            <Award class="h-4 w-4" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Continue Learning -->
                    <h2 class="mb-4 text-base font-semibold text-gray-900">
                        Continue Learning
                    </h2>

                    <div
                        v-if="currentCourse?.course"
                        class="mb-8 rounded-xl border border-gray-200 bg-white p-6"
                    >
                        <div class="flex flex-col gap-6 sm:flex-row">
                            <div
                                class="flex aspect-video w-full flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 sm:w-48"
                            >
                                <div
                                    v-if="currentCourse.course.thumbnail"
                                    class="h-full w-full rounded-lg bg-cover bg-center"
                                    :style="{
                                        backgroundImage: `url(${currentCourse.course.thumbnail})`,
                                    }"
                                ></div>
                                <Book v-else class="h-12 w-12 text-gray-400" />
                            </div>
                            <div class="w-full flex-1">
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        {{ currentCourse.course.title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ getCurrentModule(currentCourse) }}
                                    </p>
                                </div>

                                <div class="mt-4">
                                    <div
                                        class="mb-2 flex justify-between text-xs font-medium text-gray-500"
                                    >
                                        <span>Progress</span>
                                        <span>{{ currentCourse.progress }}%</span>
                                    </div>
                                    <div
                                        class="h-2 w-full rounded-full bg-gray-100"
                                    >
                                        <div
                                            class="h-2 rounded-full bg-gray-900 transition-all"
                                            :style="{
                                                width: currentCourse.progress + '%',
                                            }"
                                        ></div>
                                    </div>
                                </div>

                                <div class="mt-4 flex gap-3">
                                    <Link
                                        v-if="currentCourse.course.slug"
                                        :href="studentRoutes.learn({ course: currentCourse.course.slug }).url"
                                        class="flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                                    >
                                        <PlayCircle class="h-4 w-4" />
                                        Resume
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Courses State -->
                    <div
                        v-else
                        class="mb-8 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-12 text-center"
                    >
                        <Book class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                        <h3 class="text-lg font-medium text-gray-900">
                            No courses yet
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Start your learning journey by enrolling in a course
                        </p>
                        <Link
                            href="/courses"
                            class="mt-4 inline-block"
                        >
                            <Button>Browse Courses</Button>
                        </Link>
                    </div>

                    <!-- Upcoming Lessons -->
                    <h2 class="mb-4 text-base font-semibold text-gray-900">
                        Upcoming Lessons
                    </h2>

                    <div
                        v-if="upcomingLessons.length > 0"
                        class="divide-y divide-gray-100 rounded-xl border border-gray-200 bg-white"
                    >
                        <div
                            v-for="item in upcomingLessons"
                            :key="item.lesson.id"
                            class="flex items-center gap-4 p-4"
                        >
                            <div
                                class="rounded-lg bg-orange-50 p-2 text-orange-600"
                            >
                                <Lock class="h-4 w-4" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ item.lesson.title }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ item.course.title }} • {{ item.module.title }}
                                </p>
                            </div>
                            <div class="text-right">
                                <Badge variant="outline">
                                    {{ getTimeUntil(item.published_at) }}
                                </Badge>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center"
                    >
                        <Calendar class="mx-auto mb-3 h-8 w-8 text-gray-400" />
                        <p class="text-sm text-gray-500">
                            All lessons are available! Check back later for new content.
                        </p>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="lg:col-span-1">
                    <!-- My Courses -->
                    <h2 class="mb-4 text-base font-semibold text-gray-900">
                        My Courses
                    </h2>

                    <div class="space-y-4">
                        <div
                            v-for="enrollment in enrollments.filter(e => e.course)"
                            :key="enrollment.id"
                            class="rounded-lg border border-gray-200 bg-white p-4 transition-shadow hover:shadow-md"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-gray-100 to-gray-200"
                                >
                                    <Book class="h-5 w-5 text-gray-500" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <Link
                                        v-if="enrollment.course?.slug"
                                        :href="studentRoutes.learn({ course: enrollment.course.slug }).url"
                                        class="block"
                                    >
                                        <p
                                            class="truncate text-sm font-medium text-gray-900 hover:text-blue-600"
                                        >
                                            {{ enrollment.course.title }}
                                        </p>
                                    </Link>
                                    <div class="mt-1 flex items-center gap-2">
                                        <div class="flex-1">
                                            <div
                                                class="h-1.5 w-full rounded-full bg-gray-100"
                                            >
                                                <div
                                                    class="h-1.5 rounded-full bg-gray-900"
                                                    :style="{
                                                        width: enrollment.progress + '%',
                                                    }"
                                                ></div>
                                            </div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">
                                            {{ enrollment.progress }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Link
                            v-if="enrollments.length > 0"
                            :href="studentRoutes.coursesIndex().url"
                            class="block text-center text-sm font-medium text-blue-600 hover:text-blue-700"
                        >
                            View All Courses →
                        </Link>
                    </div>

                    <!-- Quick Stats -->
                    <h2 class="mt-8 mb-4 text-base font-semibold text-gray-900">
                        Quick Stats
                    </h2>

                    <Card>
                        <CardContent class="space-y-4 p-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Total Enrolled</span>
                                <span class="font-semibold text-gray-900">
                                    {{ enrollments.length }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Completed</span>
                                <span class="font-semibold text-gray-900">
                                    {{ stats.certificates }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">In Progress</span>
                                <span class="font-semibold text-gray-900">
                                    {{ stats.coursesInProgress }}
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </main>
    </StudentLayout>
</template>
