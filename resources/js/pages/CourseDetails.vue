<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    BarChart3,
    Check,
    ChevronDown,
    ChevronRight,
    Clock,
    ExternalLink,
    FileText,
    Heart,
    MessageSquare,
    Play,
    Share2,
    Star,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Lesson {
    id: number;
    title: string;
    duration: string;
    type: 'video' | 'reading' | 'quiz' | 'assignment';
}

interface Review {
    id: number;
    name: string;
    rating: number;
    date: string;
    content: string;
}

interface Course {
    id: number;
    title: string;
    description: string;
    instructor: string;
    instructorBio: string;
    rating: number;
    students: number;
    duration: string;
    level: string;
    category: string;
    price: number;
    lessons: Lesson[];
    reviews: Review[];
    modules: string[];
}

const course: Course = {
    id: 1,
    title: 'Scalable Microservices Architecture',
    description:
        'Master the art of building highly scalable, fault-tolerant microservices that can handle millions of requests. Learn design patterns, communication strategies, and deployment techniques used by top tech companies.',
    instructor: 'Jordan Doe',
    instructorBio:
        'Senior Engineering Manager at TechCorp with 10+ years of experience building large-scale distributed systems. Previously worked at Google and Netflix.',
    rating: 4.9,
    students: 12500,
    duration: '8 weeks',
    level: 'Advanced',
    category: 'Backend Systems',
    price: 89,
    lessons: [
        {
            id: 1,
            title: 'Introduction to Microservices',
            duration: '15 min',
            type: 'video',
        },
        {
            id: 2,
            title: 'Monolith vs Microservices',
            duration: '22 min',
            type: 'video',
        },
        { id: 3, title: 'Design Patterns', duration: '30 min', type: 'video' },
        {
            id: 4,
            title: 'Service Communication',
            duration: '28 min',
            type: 'video',
        },
        { id: 5, title: 'Data Management', duration: '35 min', type: 'video' },
        {
            id: 6,
            title: 'Security in Microservices',
            duration: '26 min',
            type: 'video',
        },
        {
            id: 7,
            title: 'Testing Strategies',
            duration: '24 min',
            type: 'video',
        },
        {
            id: 8,
            title: 'Deployment Patterns',
            duration: '32 min',
            type: 'video',
        },
        {
            id: 9,
            title: 'Monitoring & Observability',
            duration: '28 min',
            type: 'video',
        },
        {
            id: 10,
            title: 'Performance Optimization',
            duration: '30 min',
            type: 'video',
        },
    ],
    reviews: [
        {
            id: 1,
            name: 'Alex Johnson',
            rating: 5,
            date: '2 months ago',
            content:
                'This course completely changed how I think about system design. The practical examples were extremely helpful.',
        },
        {
            id: 2,
            name: 'Sam Wilson',
            rating: 5,
            date: '3 months ago',
            content:
                'Excellent instructor with deep knowledge. The real-world case studies made the concepts stick.',
        },
        {
            id: 3,
            name: 'Taylor Smith',
            rating: 4,
            date: '4 months ago',
            content:
                'Comprehensive coverage of microservices patterns. Some concepts were challenging but well explained.',
        },
    ],
    modules: [
        'Introduction & Foundations',
        'Design Patterns & Architecture',
        'Communication & APIs',
        'Data Management & Consistency',
        'Security & Authentication',
        'Testing & Quality Assurance',
        'Deployment & Orchestration',
        'Monitoring & Observability',
    ],
};

const activeTab = ref('overview');
const selectedLesson = ref(1);
const isEnrolled = ref(false);

const enrollCourse = () => {
    isEnrolled.value = true;
    // In a real app, this would make an API call to enroll the user
};
</script>

<template>
    <Head :title="`${course.title} | CodeRept`" />
    <AppLayout>
        <!-- Course Header -->
        <div class="bg-white">
            <div class="mx-auto max-w-7xl px-6">
                <div class="py-8">
                    <nav
                        class="mb-6 flex items-center gap-2 text-sm text-gray-500"
                    >
                        <span>Home</span>
                        <ChevronRight class="h-4 w-4" />
                        <span>{{ course.category }}</span>
                        <ChevronRight class="h-4 w-4" />
                        <span class="text-gray-900">{{ course.title }}</span>
                    </nav>

                    <div class="flex flex-col gap-8 md:flex-row">
                        <!-- Left Column -->
                        <div class="md:w-2/3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h1
                                        class="text-3xl font-bold tracking-tight text-gray-900 md:text-4xl"
                                    >
                                        {{ course.title }}
                                    </h1>
                                    <div
                                        class="mt-3 flex flex-wrap items-center gap-4"
                                    >
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-gray-600"
                                            >
                                                JD
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    {{ course.instructor }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Instructor
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <Star
                                                class="h-4 w-4 fill-yellow-500 text-yellow-500"
                                            />
                                            <span
                                                class="text-sm font-medium text-gray-900"
                                                >{{ course.rating }}</span
                                            >
                                            <span class="text-sm text-gray-500"
                                                >({{
                                                    course.students.toLocaleString()
                                                }}
                                                students)</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="hidden md:block">
                                    <div class="text-right">
                                        <div
                                            class="text-3xl font-bold text-gray-900"
                                        >
                                            ${{ course.price }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            One-time payment
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabs -->
                            <div class="mt-8 border-b border-gray-200">
                                <nav class="-mb-px flex space-x-8">
                                    <button
                                        v-for="tab in [
                                            'overview',
                                            'curriculum',
                                            'reviews',
                                        ]"
                                        :key="tab"
                                        @click="activeTab = tab"
                                        :class="[
                                            'border-b-2 px-1 py-4 text-sm font-medium whitespace-nowrap',
                                            activeTab === tab
                                                ? 'border-gray-900 text-gray-900'
                                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                        ]"
                                    >
                                        {{
                                            tab.charAt(0).toUpperCase() +
                                            tab.slice(1)
                                        }}
                                    </button>
                                </nav>
                            </div>

                            <!-- Tab Content -->
                            <div class="mt-6">
                                <!-- Overview Tab -->
                                <div v-if="activeTab === 'overview'">
                                    <p class="leading-relaxed text-gray-700">
                                        {{ course.description }}
                                    </p>

                                    <div class="mt-8">
                                        <h3
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            What you'll learn
                                        </h3>
                                        <ul
                                            class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2"
                                        >
                                            <li class="flex items-start">
                                                <Check
                                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-500"
                                                />
                                                <span class="ml-3 text-gray-700"
                                                    >Design scalable
                                                    microservices
                                                    architectures</span
                                                >
                                            </li>
                                            <li class="flex items-start">
                                                <Check
                                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-500"
                                                />
                                                <span class="ml-3 text-gray-700"
                                                    >Implement service
                                                    communication patterns</span
                                                >
                                            </li>
                                            <li class="flex items-start">
                                                <Check
                                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-500"
                                                />
                                                <span class="ml-3 text-gray-700"
                                                    >Manage distributed data
                                                    consistency</span
                                                >
                                            </li>
                                            <li class="flex items-start">
                                                <Check
                                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-500"
                                                />
                                                <span class="ml-3 text-gray-700"
                                                    >Implement security best
                                                    practices</span
                                                >
                                            </li>
                                            <li class="flex items-start">
                                                <Check
                                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-500"
                                                />
                                                <span class="ml-3 text-gray-700"
                                                    >Deploy and monitor
                                                    microservices</span
                                                >
                                            </li>
                                            <li class="flex items-start">
                                                <Check
                                                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-500"
                                                />
                                                <span class="ml-3 text-gray-700"
                                                    >Troubleshoot distributed
                                                    systems</span
                                                >
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="mt-8">
                                        <h3
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            Instructor
                                        </h3>
                                        <div
                                            class="mt-4 flex items-start gap-4"
                                        >
                                            <div
                                                class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-200 text-lg font-bold text-gray-600"
                                            >
                                                JD
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-base font-semibold text-gray-900"
                                                >
                                                    {{ course.instructor }}
                                                </h4>
                                                <p
                                                    class="mt-1 text-sm text-gray-600"
                                                >
                                                    {{ course.instructorBio }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Curriculum Tab -->
                                <div v-if="activeTab === 'curriculum'">
                                    <div
                                        class="rounded-lg border border-gray-200"
                                    >
                                        <div
                                            class="border-b border-gray-200 bg-gray-50 px-4 py-3"
                                        >
                                            <h3
                                                class="font-medium text-gray-900"
                                            >
                                                Course Content
                                            </h3>
                                        </div>

                                        <div class="divide-y divide-gray-100">
                                            <div
                                                v-for="(
                                                    module, index
                                                ) in course.modules"
                                                :key="index"
                                                class="p-4"
                                            >
                                                <div
                                                    class="flex items-center justify-between"
                                                >
                                                    <h4
                                                        class="font-medium text-gray-900"
                                                    >
                                                        {{ module }}
                                                    </h4>
                                                    <ChevronDown
                                                        class="h-4 w-4 text-gray-500"
                                                    />
                                                </div>
                                                <div
                                                    class="mt-3 space-y-2 pl-4"
                                                >
                                                    <div
                                                        v-for="lesson in course.lessons.slice(
                                                            index * 1,
                                                            (index + 1) * 1,
                                                        )"
                                                        :key="lesson.id"
                                                        :class="[
                                                            'flex cursor-pointer items-center gap-3 rounded-lg p-3',
                                                            selectedLesson ===
                                                            lesson.id
                                                                ? 'border border-blue-200 bg-blue-50'
                                                                : 'hover:bg-gray-50',
                                                        ]"
                                                        @click="
                                                            selectedLesson =
                                                                lesson.id
                                                        "
                                                    >
                                                        <div
                                                            class="flex items-center justify-center rounded-full bg-gray-100 p-2"
                                                        >
                                                            <Play
                                                                v-if="
                                                                    lesson.type ===
                                                                    'video'
                                                                "
                                                                class="h-4 w-4 text-gray-600"
                                                            />
                                                            <FileText
                                                                v-else-if="
                                                                    lesson.type ===
                                                                    'reading'
                                                                "
                                                                class="h-4 w-4 text-gray-600"
                                                            />
                                                            <BarChart3
                                                                v-else-if="
                                                                    lesson.type ===
                                                                    'quiz'
                                                                "
                                                                class="h-4 w-4 text-gray-600"
                                                            />
                                                            <MessageSquare
                                                                v-else
                                                                class="h-4 w-4 text-gray-600"
                                                            />
                                                        </div>
                                                        <div class="flex-1">
                                                            <p
                                                                class="text-sm font-medium text-gray-900"
                                                            >
                                                                {{
                                                                    lesson.title
                                                                }}
                                                            </p>
                                                            <p
                                                                class="text-xs text-gray-500"
                                                            >
                                                                {{
                                                                    lesson.duration
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reviews Tab -->
                                <div v-if="activeTab === 'reviews'">
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div class="flex items-center gap-4">
                                            <div class="text-center">
                                                <div
                                                    class="text-4xl font-bold text-gray-900"
                                                >
                                                    {{ course.rating }}
                                                </div>
                                                <div
                                                    class="flex items-center gap-1"
                                                >
                                                    <Star
                                                        class="h-4 w-4 fill-yellow-500 text-yellow-500"
                                                    />
                                                    <span
                                                        class="text-sm text-gray-600"
                                                        >Rating</span
                                                    >
                                                </div>
                                            </div>
                                            <div>
                                                <div
                                                    class="flex items-center gap-1"
                                                >
                                                    <div
                                                        class="w-32 overflow-hidden bg-gray-200"
                                                    >
                                                        <div
                                                            class="h-2 w-5/6 bg-yellow-500"
                                                        ></div>
                                                    </div>
                                                    <span
                                                        class="text-sm font-medium"
                                                        >5 stars</span
                                                    >
                                                </div>
                                                <div
                                                    class="flex items-center gap-1"
                                                >
                                                    <div
                                                        class="w-32 overflow-hidden bg-gray-200"
                                                    >
                                                        <div
                                                            class="h-2 w-6/6 bg-yellow-500"
                                                        ></div>
                                                    </div>
                                                    <span
                                                        class="text-sm font-medium"
                                                        >4 stars</span
                                                    >
                                                </div>
                                                <div
                                                    class="flex items-center gap-1"
                                                >
                                                    <div
                                                        class="w-32 overflow-hidden bg-gray-200"
                                                    >
                                                        <div
                                                            class="h-2 w-4/6 bg-yellow-500"
                                                        ></div>
                                                    </div>
                                                    <span
                                                        class="text-sm font-medium"
                                                        >3 stars</span
                                                    >
                                                </div>
                                                <div
                                                    class="flex items-center gap-1"
                                                >
                                                    <div
                                                        class="w-32 overflow-hidden bg-gray-200"
                                                    >
                                                        <div
                                                            class="h-2 w-3/6 bg-yellow-500"
                                                        ></div>
                                                    </div>
                                                    <span
                                                        class="text-sm font-medium"
                                                        >2 stars</span
                                                    >
                                                </div>
                                                <div
                                                    class="flex items-center gap-1"
                                                >
                                                    <div
                                                        class="w-32 overflow-hidden bg-gray-200"
                                                    >
                                                        <div
                                                            class="h-2 w-1/6 bg-yellow-500"
                                                        ></div>
                                                    </div>
                                                    <span
                                                        class="text-sm font-medium"
                                                        >1 star</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button
                                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                            >
                                                Write a review
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-8 space-y-6">
                                        <div
                                            v-for="review in course.reviews"
                                            :key="review.id"
                                            class="border-b border-gray-100 pb-6 last:border-0"
                                        >
                                            <div class="flex items-start gap-4">
                                                <div
                                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-gray-600"
                                                >
                                                    {{ review.name.charAt(0) }}
                                                </div>
                                                <div class="flex-1">
                                                    <div
                                                        class="flex items-center justify-between"
                                                    >
                                                        <h4
                                                            class="font-medium text-gray-900"
                                                        >
                                                            {{ review.name }}
                                                        </h4>
                                                        <div
                                                            class="flex items-center gap-1"
                                                        >
                                                            <Star
                                                                class="h-4 w-4 fill-yellow-500 text-yellow-500"
                                                            />
                                                            <span
                                                                class="text-sm text-gray-900"
                                                                >{{
                                                                    review.rating
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>
                                                    <p
                                                        class="mt-1 text-xs text-gray-500"
                                                    >
                                                        {{ review.date }}
                                                    </p>
                                                    <p
                                                        class="mt-2 text-sm text-gray-700"
                                                    >
                                                        {{ review.content }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="md:w-1/3">
                            <div
                                class="sticky top-24 rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
                            >
                                <div
                                    class="flex aspect-video w-full items-center justify-center overflow-hidden rounded-lg bg-gray-100"
                                >
                                    <Play class="h-12 w-12 text-gray-400" />
                                </div>

                                <div class="mt-6">
                                    <div class="flex items-baseline gap-2">
                                        <div
                                            class="text-3xl font-bold text-gray-900"
                                        >
                                            ${{ course.price }}
                                        </div>
                                        <div
                                            class="text-sm text-gray-500 line-through"
                                        >
                                            $129
                                        </div>
                                    </div>

                                    <button
                                        v-if="!isEnrolled"
                                        @click="enrollCourse"
                                        class="mt-6 w-full rounded-lg bg-gray-900 px-4 py-3 text-center text-sm font-medium text-white shadow-sm transition-colors hover:bg-gray-800"
                                    >
                                        Enroll Now
                                    </button>
                                    <button
                                        v-else
                                        class="mt-6 w-full rounded-lg bg-green-600 px-4 py-3 text-center text-sm font-medium text-white shadow-sm transition-colors hover:bg-green-700"
                                    >
                                        Continue Learning
                                    </button>

                                    <div
                                        class="mt-4 text-center text-xs text-gray-500"
                                    >
                                        30-Day Money-Back Guarantee
                                    </div>
                                </div>

                                <div class="mt-6 space-y-3">
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span class="text-sm text-gray-600"
                                            >Duration</span
                                        >
                                        <div class="flex items-center gap-1">
                                            <Clock
                                                class="h-4 w-4 text-gray-500"
                                            />
                                            <span
                                                class="text-sm font-medium text-gray-900"
                                                >{{ course.duration }}</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span class="text-sm text-gray-600"
                                            >Level</span
                                        >
                                        <span
                                            class="text-sm font-medium text-gray-900"
                                            >{{ course.level }}</span
                                        >
                                    </div>
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span class="text-sm text-gray-600"
                                            >Students</span
                                        >
                                        <span
                                            class="text-sm font-medium text-gray-900"
                                            >{{
                                                course.students.toLocaleString()
                                            }}</span
                                        >
                                    </div>
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span class="text-sm text-gray-600"
                                            >Rating</span
                                        >
                                        <div class="flex items-center gap-1">
                                            <Star
                                                class="h-4 w-4 fill-yellow-500 text-yellow-500"
                                            />
                                            <span
                                                class="text-sm font-medium text-gray-900"
                                                >{{ course.rating }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-3">
                                    <button
                                        class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                                    >
                                        <Heart class="h-4 w-4" />
                                        Add to Wishlist
                                    </button>
                                    <button
                                        class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                                    >
                                        <Share2 class="h-4 w-4" />
                                        Share
                                    </button>
                                    <button
                                        class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                                    >
                                        <ExternalLink class="h-4 w-4" />
                                        Preview Course
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
