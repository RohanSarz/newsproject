<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    BookOpen,
    Clock,
    PlayCircle,
    CheckCircle2,
    Lock,
} from 'lucide-vue-next';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface LessonProgress {
    completed: boolean;
    completed_at: string | null;
}

interface Lesson {
    id: number;
    title: string;
    is_published: boolean;
    published_at: string | null;
    lesson_progress: LessonProgress[];
}

interface Module {
    id: number;
    title: string;
    lessons?: Lesson[];
}

interface Course {
    id: number;
    title: string;
    description: string;
    slug: string;
    thumbnail?: string;
    level: string;
    modules: Module[];
}

interface Enrollment {
    id: number;
    enrolled_at: string;
    progress: number;
    completed_at: string | null;
    status: string;
    course: Course;
}

interface Props {
    enrollments: {
        data: Enrollment[];
        links?: any;
        meta?: {
            current_page: number;
            from: number;
            last_page: number;
            path: string;
            per_page: number;
            to: number;
            total: number;
        };
    };
}

const { props } = usePage<Props>();
const { enrollments } = props;

// Route helpers
const studentRoutes = {
    learn: (params: { course: string }) => ({ url: `/student/courses/${params.course}/learn` }),
};

const courseRoutes = {
    catalog: () => ({ url: '/courses' }),
};

const getProgressColor = (progress: number) => {
    if (progress === 100) return 'bg-green-500';
    if (progress >= 50) return 'bg-blue-500';
    return 'bg-gray-300';
};

const getStatusText = (enrollment: Enrollment) => {
    if (enrollment.completed_at) return 'Completed';
    if (enrollment.progress > 0) return 'In Progress';
    return 'Not Started';
};

const getStatusColor = (enrollment: Enrollment) => {
    if (enrollment.completed_at) return 'bg-green-100 text-green-800';
    if (enrollment.progress > 0) return 'bg-blue-100 text-blue-800';
    return 'bg-gray-100 text-gray-800';
};

const totalLessons = (course: Course) => {
    if (!course?.modules) return 0;
    return course.modules.reduce((sum, module) => {
        return sum + (module.lessons ? module.lessons.length : 0);
    }, 0);
};

const completedLessons = (course: Course) => {
    if (!course?.modules) return 0;
    return course.modules.reduce((sum, module) => {
        return (
            sum +
            (module.lessons ? module.lessons.filter(
                (lesson) => lesson.lesson_progress[0]?.completed
            ).length : 0)
        );
    }, 0);
};

const firstIncompleteLesson = (course: Course) => {
    if (!course?.modules) return null;
    for (const module of course.modules) {
        if (module.lessons) {
            for (const lesson of module.lessons) {
                if (!lesson.lesson_progress[0]?.completed && lesson.is_published) {
                    return { module, lesson };
                }
            }
        }
    }
    return null;
};
</script>

<template>
    <Head title="My Courses | CodeRept" />
    <StudentLayout>
        <div class="mx-auto max-w-7xl px-6 py-8 md:py-12">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
                    My Courses
                </h1>
                <p class="mt-2 text-gray-600">
                    Continue your learning journey
                </p>
            </div>

            <!-- Courses Grid -->
            <div v-if="enrollments.data.length > 0" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="enrollment in enrollments.data.filter(e => e.course)"
                    :key="enrollment.id"
                    class="group overflow-hidden transition-all hover:shadow-lg"
                >
                    <!-- Thumbnail or Placeholder -->
                    <div class="relative aspect-video bg-gradient-to-br from-gray-100 to-gray-200">
                        <div
                            v-if="enrollment.course.thumbnail"
                            class="h-full w-full bg-cover bg-center"
                            :style="{
                                backgroundImage: `url(${enrollment.course.thumbnail})`,
                            }"
                        ></div>
                        <div
                            v-else
                            class="flex h-full items-center justify-center"
                        >
                            <BookOpen class="h-16 w-16 text-gray-400" />
                        </div>

                        <!-- Progress Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                            <div class="mb-2 flex items-center justify-between text-xs text-white">
                                <span>
                                    {{ completedLessons(enrollment.course) }} /
                                    {{ totalLessons(enrollment.course) }} lessons
                                </span>
                                <span>{{ enrollment.progress }}%</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-white/30">
                                <div
                                    class="h-full transition-all duration-500"
                                    :class="getProgressColor(enrollment.progress)"
                                    :style="{ width: `${enrollment.progress}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <CardContent class="p-5">
                        <!-- Title -->
                        <h3 class="mb-2 text-lg font-semibold text-gray-900">
                            <Link
                                v-if="enrollment.course?.slug"
                                :href="studentRoutes.learn({ course: enrollment.course.slug }).url"
                                class="hover:text-blue-600 transition-colors"
                            >
                                {{ enrollment.course.title }}
                            </Link>
                            <span v-else class="text-gray-900">
                                {{ enrollment.course?.title }}
                            </span>
                        </h3>

                        <!-- Description -->
                        <p class="mb-4 line-clamp-2 text-sm text-gray-600">
                            {{ enrollment.course?.description }}
                        </p>

                        <!-- Status Badge -->
                        <div class="mb-4">
                            <Badge :class="getStatusColor(enrollment)">
                                {{ getStatusText(enrollment) }}
                            </Badge>
                        </div>

                        <!-- Enrolled Date -->
                        <div class="mb-4 text-xs text-gray-500">
                            Enrolled {{ new Date(enrollment.enrolled_at).toLocaleDateString() }}
                        </div>

                        <!-- Continue/Start Button -->
                        <Link
                            v-if="enrollment.course?.slug"
                            :href="studentRoutes.learn({ course: enrollment.course.slug }).url"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-800"
                        >
                            <PlayCircle class="h-4 w-4" />
                            {{ enrollment.progress > 0 ? 'Continue Learning' : 'Start Course' }}
                        </Link>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="flex flex-col items-center rounded-lg border border-dashed border-gray-300 bg-gray-50 py-16 text-center"
            >
                <BookOpen class="mb-4 h-16 w-16 text-gray-400" />
                <h3 class="text-lg font-medium text-gray-900">
                    No courses yet
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Start your learning journey by enrolling in a course
                </p>
                <Link
                    :href="courseRoutes.catalog().url"
                    class="mt-6"
                >
                    <Button>Browse Courses</Button>
                </Link>
            </div>

            <!-- Pagination -->
            <div
                v-if="enrollments.meta && enrollments.meta.last_page > 1"
                class="mt-8 flex items-center justify-center gap-2"
            >
                <Button
                    v-for="link in enrollments.meta?.links || []"
                    :key="link.label"
                    :disabled="!link.url || link.active"
                    :variant="link.active ? 'default' : 'outline'"
                    size="sm"
                    @click="
                        link.url &&
                            router.get(link.url, {
                                preserveState: true,
                                preserveScroll: true,
                            })
                    "
                    v-html="link.label.replace('&laquo;', '«').replace('&raquo;', '»')"
                />
            </div>
        </div>
    </StudentLayout>
</template>
