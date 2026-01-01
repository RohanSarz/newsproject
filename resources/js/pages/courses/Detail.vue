<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    BookOpen,
    Clock,
    Users,
    PlayCircle,
    CheckCircle2,
    ChevronRight,
    Lock,
} from 'lucide-vue-next';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Lesson {
    id: number;
    title: string;
    description: string;
    duration: number;
    is_preview: boolean;
    is_published: boolean;
    published_at?: string;
}

interface Module {
    id: number;
    title: string;
    description: string;
    lessons: Lesson[];
}

interface Course {
    id: number;
    title: string;
    description: string;
    overview: string;
    level: string;
    price: number;
    slug: string;
    thumbnail?: string;
    instructor: {
        id: number;
        name: string;
    };
    modules: Module[];
    objectives?: string[];
    requirements?: string[];
}

interface Props {
    course: Course;
    isEnrolled: boolean;
}

const { props } = usePage<Props>();
const { course, isEnrolled } = props;

// Route helpers
const courseRoutes = {
    catalog: () => ({ url: '/courses' }),
    detail: (params: { course: string }) => ({ url: `/courses/${params.course}` }),
};

const studentRoutes = {
    enroll: (params: { course: string }) => ({ url: `/student/courses/${params.course}/enroll` }),
    learn: (params: { course: string }) => ({ url: `/student/courses/${params.course}/learn` }),
};

const enrollForm = useForm({});

const enroll = () => {
    enrollForm.post(studentRoutes.enroll({ course: course.slug }).url);
};

const continueLearning = () => {
    router.get(studentRoutes.learn({ course: course.slug }).url);
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

const totalLessons = computed(() => {
    return course.modules.reduce((sum, module) => sum + module.lessons.length, 0);
});

const totalDuration = computed(() => {
    return course.modules.reduce((sum, module) => {
        return (
            sum +
            module.lessons.reduce((lessonSum, lesson) => lessonSum + (lesson.duration || 0), 0)
        );
    }, 0);
});

const formatDuration = (seconds: number) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);

    if (hours > 0) {
        return `${hours}h ${minutes}m`;
    }
    return `${minutes}m`;
};
</script>

<template>
    <Head :title="`${course.title} | CodeRept`" />
    <AppLayout>
        <div class="mx-auto max-w-7xl px-6 py-8 md:py-12">
            <!-- Breadcrumb -->
            <div class="mb-6 flex items-center gap-2 text-sm">
                <Link
                    :href="courseRoutes.catalog().url"
                    class="text-gray-600 hover:text-gray-900"
                >
                    Courses
                </Link>
                <ChevronRight class="h-4 w-4 text-gray-400" />
                <span class="font-medium text-gray-900">{{ course.title }}</span>
            </div>

            <div class="flex flex-col gap-8 lg:flex-row">
                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Course Header -->
                    <div class="mb-8">
                        <div class="mb-4 flex flex-wrap items-center gap-2">
                            <Badge :class="getLevelColor(course.level)">
                                {{ course.level.charAt(0).toUpperCase() + course.level.slice(1) }}
                            </Badge>
                            <Badge variant="outline">
                                {{ course.modules.length }} Modules
                            </Badge>
                            <Badge variant="outline">
                                {{ totalLessons }} Lessons
                            </Badge>
                        </div>

                        <h1 class="mb-4 text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
                            {{ course.title }}
                        </h1>

                        <p class="text-lg text-gray-600">{{ course.description }}</p>
                    </div>

                    <!-- Course Overview -->
                    <Card class="mb-8">
                        <CardHeader>
                            <CardTitle>Course Overview</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="whitespace-pre-line text-gray-700">
                                {{ course.overview || course.description }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Learning Objectives -->
                    <Card v-if="course.objectives && course.objectives.length > 0" class="mb-8">
                        <CardHeader>
                            <CardTitle>What You'll Learn</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ul class="space-y-2">
                                <li
                                    v-for="(objective, index) in course.objectives"
                                    :key="index"
                                    class="flex items-start gap-2"
                                >
                                    <CheckCircle2 class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-500" />
                                    <span class="text-gray-700">{{ objective }}</span>
                                </li>
                            </ul>
                        </CardContent>
                    </Card>

                    <!-- Requirements -->
                    <Card v-if="course.requirements && course.requirements.length > 0" class="mb-8">
                        <CardHeader>
                            <CardTitle>Requirements</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ul class="space-y-2">
                                <li
                                    v-for="(requirement, index) in course.requirements"
                                    :key="index"
                                    class="flex items-start gap-2"
                                >
                                    <div class="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-gray-400" />
                                    <span class="text-gray-700">{{ requirement }}</span>
                                </li>
                            </ul>
                        </CardContent>
                    </Card>

                    <!-- Curriculum -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Course Curriculum</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div
                                    v-for="module in course.modules"
                                    :key="module.id"
                                    class="rounded-lg border border-gray-200 p-4"
                                >
                                    <h3 class="mb-2 font-semibold text-gray-900">
                                        {{ module.title }}
                                    </h3>
                                    <p
                                        v-if="module.description"
                                        class="mb-3 text-sm text-gray-600"
                                    >
                                        {{ module.description }}
                                    </p>
                                    <ul class="space-y-1">
                                        <li
                                            v-for="lesson in module.lessons"
                                            :key="lesson.id"
                                            class="flex items-center justify-between rounded px-2 py-1.5 text-sm text-gray-600 hover:bg-gray-50"
                                        >
                                            <div class="flex items-center gap-2">
                                                <PlayCircle class="h-4 w-4" />
                                                <span>{{ lesson.title }}</span>
                                                <Badge
                                                    v-if="lesson.is_preview"
                                                    variant="outline"
                                                    class="text-xs"
                                                >
                                                    Preview
                                                </Badge>
                                            </div>
                                            <span class="text-xs text-gray-500">
                                                {{ formatDuration(lesson.duration) }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="w-full space-y-6 lg:w-80">
                    <!-- Course Card -->
                    <Card class="sticky top-6">
                        <CardContent class="p-6">
                            <!-- Thumbnail -->
                            <div class="mb-4 aspect-video rounded-lg bg-gradient-to-br from-gray-100 to-gray-200">
                                <div
                                    v-if="course.thumbnail"
                                    class="h-full w-full rounded-lg bg-cover bg-center"
                                    :style="{ backgroundImage: `url(${course.thumbnail})` }"
                                ></div>
                                <div
                                    v-else
                                    class="flex h-full items-center justify-center rounded-lg"
                                >
                                    <BookOpen class="h-16 w-16 text-gray-400" />
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="mb-4 text-center">
                                <div class="text-3xl font-bold text-gray-900">
                                    {{ formatPrice(course.price) }}
                                </div>
                            </div>

                            <!-- Enroll/Continue Button -->
                            <Button
                                v-if="!isEnrolled"
                                class="mb-4 w-full"
                                size="lg"
                                :disabled="enrollForm.processing"
                                @click="enroll"
                            >
                                {{ enrollForm.processing ? 'Enrolling...' : 'Enroll Now' }}
                            </Button>
                            <Button
                                v-else
                                class="mb-4 w-full"
                                size="lg"
                                @click="continueLearning"
                            >
                                Continue Learning
                            </Button>

                            <!-- Stats -->
                            <div class="space-y-3 border-t border-gray-100 pt-4">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <BookOpen class="h-4 w-4" />
                                        <span>Lessons</span>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ totalLessons }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <Clock class="h-4 w-4" />
                                        <span>Duration</span>
                                    </div>
                                    <span class="font-medium text-gray-900">
                                        {{ formatDuration(totalDuration) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <Users class="h-4 w-4" />
                                        <span>Instructor</span>
                                    </div>
                                    <span class="font-medium text-gray-900">
                                        {{ course.instructor.name }}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
