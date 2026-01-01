<script setup lang="ts">
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    PlayCircle,
    CheckCircle2,
    Lock,
    ChevronRight,
    ChevronLeft,
    ChevronDown,
    BookOpen,
    Clock,
    List,
    X,
} from 'lucide-vue-next';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

interface LessonProgress {
    completed: boolean;
    completed_at: string | null;
    last_watched_at: string | null;
    watch_time: number;
}

interface Lesson {
    id: number;
    title: string;
    description: string;
    duration: number;
    video_url: string;
    is_preview: boolean;
    is_published: boolean;
    published_at: string | null;
    lesson_progress: LessonProgress[];
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
    modules: Module[];
}

interface Enrollment {
    id: number;
    progress: number;
    enrolled_at: string;
}

interface Props {
    course: Course;
    enrollment: Enrollment;
    currentModule: Module;
    currentLesson: Lesson;
}

const { props } = usePage<Props>();
const { course, enrollment, currentModule, currentLesson } = props;

// Route helpers
const studentRoutes = {
    progress: (params: { lesson: number }) => ({ url: `/student/lessons/${params.lesson}/progress` }),
    showLesson: (params: { course: string; module: number; lesson: number }) => ({
        url: `/student/courses/${params.course}/modules/${params.module}/lessons/${params.lesson}`
    }),
};

const sidebarOpen = ref(true);
const completedLessons = ref<Set<number>>(new Set());
const currentModuleOpen = ref<number>(currentModule.id);

const progressForm = useForm({
    completed: false,
    watch_time: 0,
});

onMounted(() => {
    // Initialize completed lessons
    course.modules.forEach((module) => {
        module.lessons.forEach((lesson) => {
            if (lesson.lesson_progress[0]?.completed) {
                completedLessons.value.add(lesson.id);
            }
        });
    });

    // Set current lesson completion status
    progressForm.completed = completedLessons.value.has(currentLesson.id);
});

const youtubeId = computed(() => {
    if (!currentLesson.video_url) return null;

    // Handle various YouTube URL formats:
    // - youtube.com/watch?v=ID
    // - youtube.com/embed/ID
    // - youtube.com/shorts/ID
    // - youtu.be/ID
    // - Just the ID (if stored directly)
    const patterns = [
        /(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([^&\n?#]+)/,
        /(?:https?:\/\/)?(?:www\.)?youtube\.com\/embed\/([^&\n?#]+)/,
        /(?:https?:\/\/)?(?:www\.)?youtube\.com\/shorts\/([^&\n?#]+)/,
        /(?:https?:\/\/)?(?:www\.)?youtu\.be\/([^&\n?#]+)/,
        /^([a-zA-Z0-9_-]{11})$/, // Direct ID match (11 character YouTube ID)
    ];

    for (const pattern of patterns) {
        const match = currentLesson.video_url.match(pattern);
        if (match) return match[1];
    }

    console.warn('Could not extract YouTube ID from URL:', currentLesson.video_url);
    return null;
});

const isLessonAvailable = (lesson: Lesson) => {
    // Show all lessons regardless of published status
    return true;
};

const isLessonCompleted = (lesson: Lesson) => {
    return completedLessons.value.has(lesson.id);
};

const toggleComplete = () => {
    progressForm.completed = !progressForm.completed;

    if (progressForm.completed) {
        completedLessons.value.add(currentLesson.id);
    } else {
        completedLessons.value.delete(currentLesson.id);
    }

    progressForm.post(
        studentRoutes.progress({ lesson: currentLesson.id }).url,
        {
            onSuccess: () => {
                // Progress updated
            },
        }
    );
};

const getAllLessons = () => {
    const lessons: Array<{ module: Module; lesson: Lesson }> = [];

    course.modules.forEach((module) => {
        module.lessons.forEach((lesson) => {
            lessons.push({ module, lesson });
        });
    });

    return lessons;
};

const currentLessonIndex = computed(() => {
    const lessons = getAllLessons();
    return lessons.findIndex(
        (item) => item.lesson.id === currentLesson.id
    );
});

const hasNextLesson = computed(() => {
    return currentLessonIndex.value < getAllLessons().length - 1;
});

const hasPreviousLesson = computed(() => {
    return currentLessonIndex.value > 0;
});

const goToNextLesson = () => {
    const lessons = getAllLessons();
    const nextItem = lessons[currentLessonIndex.value + 1];

    if (nextItem && isLessonAvailable(nextItem.lesson)) {
        router.get(
            studentRoutes.showLesson({
                course: course.slug,
                module: nextItem.module.id,
                lesson: nextItem.lesson.id,
            }).url
        );
    }
};

const goToPreviousLesson = () => {
    const lessons = getAllLessons();
    const previousItem = lessons[currentLessonIndex.value - 1];

    if (previousItem && isLessonAvailable(previousItem.lesson)) {
        router.get(
            studentRoutes.showLesson({
                course: course.slug,
                module: previousItem.module.id,
                lesson: previousItem.lesson.id,
            }).url
        );
    }
};

const selectLesson = (module: Module, lesson: Lesson) => {
    if (isLessonAvailable(lesson)) {
        router.get(
            studentRoutes.showLesson({
                course: course.slug,
                module: module.id,
                lesson: lesson.id,
            }).url
        );
    }
};

const formatDuration = (seconds: number) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);

    if (hours > 0) {
        return `${hours}h ${minutes}m`;
    }
    return `${minutes}m`;
};

const getAvailabilityText = (lesson: Lesson) => {
    // Show all lessons as available
    return '';
};
</script>

<template>
    <Head :title="`${currentLesson.title} | ${course.title} | CodeRept`" />
    <StudentLayout>
        <div class="flex h-[calc(100vh-4rem)]">
            <!-- Sidebar Toggle Button (Mobile) -->
            <button
                @click="sidebarOpen = !sidebarOpen"
                class="fixed bottom-6 right-6 z-50 rounded-full bg-gray-900 p-3 text-white shadow-lg md:hidden"
            >
                <List v-if="!sidebarOpen" class="h-6 w-6" />
                <X v-else class="h-6 w-6" />
            </button>

            <!-- Sidebar -->
            <div
                :class="[
                    'fixed inset-y-0 left-0 z-40 w-80 transform border-r border-gray-200 bg-white transition-transform duration-300 md:relative md:translate-x-0',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                ]"
            >
                <div class="flex h-full flex-col">
                    <!-- Sidebar Header -->
                    <div class="flex items-center justify-between border-b border-gray-200 p-4">
                        <div>
                            <h2 class="font-semibold text-gray-900">{{ course.title }}</h2>
                            <div class="mt-1 flex items-center gap-2 text-xs text-gray-600">
                                <div class="h-1.5 w-24 overflow-hidden rounded-full bg-gray-200">
                                    <div
                                        class="h-full bg-blue-600 transition-all"
                                        :style="{ width: `${enrollment.progress}%` }"
                                    ></div>
                                </div>
                                <span>{{ enrollment.progress }}% complete</span>
                            </div>
                        </div>
                        <button
                            @click="sidebarOpen = false"
                            class="rounded-md p-1 hover:bg-gray-100 md:hidden"
                        >
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <!-- Modules & Lessons -->
                    <div class="flex-1 overflow-y-auto p-4">
                        <div class="space-y-2">
                            <div
                                v-for="module in course.modules"
                                :key="module.id"
                                class="overflow-hidden rounded-lg border border-gray-200"
                            >
                                <!-- Module Header -->
                                <button
                                    @click="currentModuleOpen = currentModuleOpen === module.id ? null : module.id"
                                    class="flex w-full items-center justify-between bg-gray-50 px-4 py-3 text-left"
                                >
                                    <div class="flex items-center gap-2">
                                        <BookOpen class="h-4 w-4 text-gray-600" />
                                        <span class="font-medium text-gray-900">
                                            {{ module.title }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            ({{ module.lessons.length }})
                                        </span>
                                    </div>
                                    <ChevronDown
                                        :class="[
                                            'h-4 w-4 text-gray-500 transition-transform',
                                            currentModuleOpen === module.id ? 'rotate-180' : '',
                                        ]"
                                    />
                                </button>

                                <!-- Lessons -->
                                <div
                                    v-if="currentModuleOpen === module.id"
                                    class="border-t border-gray-200 bg-white"
                                >
                                    <div
                                        v-for="lesson in module.lessons"
                                        :key="lesson.id"
                                        :class="[
                                            'flex cursor-pointer items-center justify-between px-4 py-2.5 text-sm transition-colors',
                                            currentLesson.id === lesson.id
                                                ? 'bg-blue-50 text-blue-700'
                                                : 'text-gray-700 hover:bg-gray-50',
                                        ]"
                                        @click="selectLesson(module, lesson)"
                                    >
                                        <div class="flex items-center gap-2">
                                            <!-- Icon -->
                                            <CheckCircle2
                                                v-if="isLessonCompleted(lesson)"
                                                class="h-4 w-4 text-green-500"
                                            />
                                            <PlayCircle
                                                v-else-if="isLessonAvailable(lesson)"
                                                class="h-4 w-4"
                                                :class="[
                                                    currentLesson.id === lesson.id
                                                        ? 'text-blue-600'
                                                        : 'text-gray-400',
                                                ]"
                                            />
                                            <Lock
                                                v-else
                                                class="h-4 w-4 text-gray-400"
                                            />

                                            <!-- Title -->
                                            <span class="flex-1 truncate">
                                                {{ lesson.title }}
                                            </span>
                                        </div>

                                        <!-- Duration or Status -->
                                        <span class="text-xs">
                                            <span v-if="getAvailabilityText(lesson)" class="text-gray-500">
                                                {{ getAvailabilityText(lesson) }}
                                            </span>
                                            <span v-else class="text-gray-500">
                                                {{ formatDuration(lesson.duration) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 overflow-y-auto">
                <div class="mx-auto max-w-5xl p-6 md:p-8">
                    <!-- Video Player -->
                    <div class="mb-6 overflow-hidden rounded-lg bg-black shadow-lg">
                        <div v-if="youtubeId" class="aspect-video w-full">
                            <iframe
                                :src="`https://www.youtube.com/embed/${youtubeId}`"
                                class="h-full w-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>
                        <div
                            v-else
                            class="flex aspect-video items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900"
                        >
                            <div class="text-center text-white">
                                <PlayCircle class="mx-auto mb-4 h-16 w-16 text-gray-600" />
                                <p class="text-gray-400">No video available</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lesson Info -->
                    <div class="mb-6">
                        <div class="mb-4 flex flex-wrap items-center gap-2">
                            <Badge variant="outline">{{ currentModule.title }}</Badge>
                            <div class="flex items-center gap-1 text-xs text-gray-500">
                                <Clock class="h-3.5 w-3.5" />
                                <span>{{ formatDuration(currentLesson.duration) }}</span>
                            </div>
                        </div>

                        <h1 class="mb-3 text-2xl font-semibold text-gray-900 md:text-3xl">
                            {{ currentLesson.title }}
                        </h1>

                        <p
                            v-if="currentLesson.description"
                            class="text-gray-600"
                        >
                            {{ currentLesson.description }}
                        </p>
                    </div>

                    <!-- Mark Complete Button -->
                    <Card class="mb-6">
                        <CardContent class="flex items-center justify-between p-4">
                            <div class="flex items-center gap-3">
                                <div
                                    :class="[
                                        'flex h-10 w-10 items-center justify-center rounded-full transition-colors',
                                        progressForm.completed
                                            ? 'bg-green-100 text-green-600'
                                            : 'bg-gray-100 text-gray-600',
                                    ]"
                                >
                                    <CheckCircle2 class="h-5 w-5" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">
                                        {{ progressForm.completed ? 'Completed!' : 'Mark as Complete' }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        Track your progress by completing lessons
                                    </p>
                                </div>
                            </div>
                            <Button
                                :variant="progressForm.completed ? 'outline' : 'default'"
                                :disabled="progressForm.processing"
                                @click="toggleComplete"
                            >
                                {{ progressForm.completed ? 'Completed' : 'Mark Complete' }}
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Navigation -->
                    <div class="flex items-center justify-between gap-4">
                        <Button
                            variant="outline"
                            :disabled="!hasPreviousLesson"
                            @click="goToPreviousLesson"
                        >
                            <ChevronLeft class="mr-2 h-4 w-4" />
                            Previous
                        </Button>
                        <Button
                            :disabled="!hasNextLesson"
                            @click="goToNextLesson"
                        >
                            Next
                            <ChevronRight class="ml-2 h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
