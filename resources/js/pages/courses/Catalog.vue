<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Search,
    Star,
    Users,
    Clock,
    BookOpen,
    Filter,
} from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { ref, watch, onUnmounted } from 'vue';

interface Course {
    id: number;
    title: string;
    description: string;
    level: string;
    price: number;
    slug: string;
    thumbnail?: string;
    instructor: {
        id: number;
        name: string;
    };
    module_count: number;
    total_lesson_count: number;
}

interface Props {
    courses: {
        data: Course[];
        links: any;
        meta: {
            current_page: number;
            from: number;
            last_page: number;
            path: string;
            per_page: number;
            to: number;
            total: number;
        };
    };
    filters?: {
        search?: string;
        level?: string;
        sort?: string;
    };
}

const { props } = usePage<Props>();
const { courses, filters = {} } = props;

// Route helpers
const courseRoutes = {
    catalog: () => ({ url: '/courses' }),
    detail: (params: { course: string }) => ({ url: `/courses/${params.course}` }),
};

// Initialize reactive variables with current filter values
const searchInput = ref(props.filters?.search || '');
const levelInput = ref(props.filters?.level || '');
const sortInput = ref(props.filters?.sort || 'newest');

const levels = [
    { value: 'beginner', label: 'Beginner' },
    { value: 'intermediate', label: 'Intermediate' },
    { value: 'advanced', label: 'Advanced' },
];

const sortOptions = [
    { value: 'newest', label: 'Newest' },
    { value: 'title', label: 'Title A-Z' },
];

// Debounced search function
let searchTimeout: number | null = null;

const performSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = window.setTimeout(() => {
        router.get(courseRoutes.catalog().url, {
            search: searchInput.value,
            level: levelInput.value,
            sort: sortInput.value
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300);
};

// Watch for changes and trigger search
watch(
    () => [searchInput.value, levelInput.value, sortInput.value],
    () => {
        performSearch();
    }
);

// Watch for changes in filters prop and update reactive variables
watch(
    () => props.filters,
    (newFilters) => {
        if (newFilters) {
            searchInput.value = newFilters.search || '';
            levelInput.value = newFilters.level || '';
            sortInput.value = newFilters.sort || 'newest';
        }
    },
    { deep: true }
);

// Watch for changes in courses data to ensure UI updates
watch(
    () => props.courses,
    (newCourses) => {
        console.log('Courses data updated:', newCourses?.data?.length, 'courses found');
    },
    { deep: true }
);

// Clean up timeout on component unmount
onUnmounted(() => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
});

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
</script>

<template>
    <Head title="Course Catalog | CodeRept" />
    <AppLayout>
        <div class="mx-auto max-w-7xl px-6 py-8 md:py-12">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
                    Course Catalog
                </h1>
                <p class="mt-2 text-gray-600">
                    Explore our comprehensive collection of programming courses
                </p>
            </div>

            <!-- Filters -->
            <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center">
                    <!-- Search -->
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <Input
                            v-model="searchInput"
                            type="text"
                            placeholder="Search courses..."
                            class="pl-10"
                        />
                    </div>

                    <!-- Level Filter -->
                    <div class="flex items-center gap-2">
                        <Filter class="h-4 w-4 text-gray-500" />
                        <select
                            v-model="levelInput"
                            class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-500 focus:outline-none focus:ring-0"
                        >
                            <option value="">All Levels</option>
                            <option
                                v-for="level in levels"
                                :key="level.value"
                                :value="level.value"
                            >
                                {{ level.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="flex items-center gap-2">
                        <select
                            v-model="sortInput"
                            class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-500 focus:outline-none focus:ring-0"
                        >
                            <option
                                v-for="option in sortOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Course Grid -->
            <div
                v-if="courses.data.length > 0"
                class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <Card
                    v-for="course in courses.data"
                    :key="course.id"
                    class="group overflow-hidden transition-all hover:shadow-lg"
                >
                    <Link
                        :href="courseRoutes.detail({ course: course.slug }).url"
                        class="block"
                    >
                        <!-- Thumbnail or Placeholder -->
                        <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200">
                            <div
                                v-if="course.thumbnail"
                                class="h-full w-full bg-cover bg-center"
                                :style="{ backgroundImage: `url(${course.thumbnail})` }"
                            ></div>
                            <div
                                v-else
                                class="flex h-full items-center justify-center"
                            >
                                <BookOpen class="h-16 w-16 text-gray-400" />
                            </div>
                            <Badge
                                :class="[getLevelColor(course.level), 'absolute top-3 right-3']"
                            >
                                {{ course.level.charAt(0).toUpperCase() + course.level.slice(1) }}
                            </Badge>
                        </div>

                        <CardContent class="p-5">
                            <!-- Title -->
                            <h3
                                class="mb-2 text-lg font-semibold text-gray-900 group-hover:text-blue-600"
                            >
                                <Link
                                    :href="courseRoutes.detail({ course: course.slug }).url"
                                    class="hover:text-blue-600 transition-colors"
                                >
                                    {{ course.title }}
                                </Link>
                            </h3>

                            <!-- Description -->
                            <p class="mb-4 line-clamp-2 text-sm text-gray-600">
                                {{ course.description }}
                            </p>

                            <!-- Stats -->
                            <div class="mb-4 flex flex-wrap gap-3 text-xs text-gray-500">
                                <div class="flex items-center gap-1">
                                    <BookOpen class="h-3.5 w-3.5" />
                                    <span>{{ course.module_count }} modules</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <Clock class="h-3.5 w-3.5" />
                                    <span>{{ course.total_lesson_count }} lessons</span>
                                </div>
                            </div>

                            <!-- Instructor and Price -->
                            <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-xs font-bold text-gray-600">
                                        {{
                                            course.instructor.name
                                                .split(' ')
                                                .map((n: string) => n[0])
                                                .join('')
                                                .toUpperCase()
                                        }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">
                                        {{ course.instructor.name }}
                                    </span>
                                </div>
                                <span class="text-lg font-bold text-gray-900">
                                    {{ formatPrice(course.price) }}
                                </span>
                            </div>
                        </CardContent>
                    </Link>
                </Card>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="rounded-lg border border-dashed border-gray-300 bg-gray-50 py-12 text-center"
            >
                <Search class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                <h3 class="text-lg font-medium text-gray-900">
                    No courses found
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Try adjusting your search or filters
                </p>
            </div>

            <!-- Pagination -->
            <div
                v-if="courses && courses.meta && courses.meta.last_page > 1"
                class="mt-8 flex items-center justify-center gap-2"
            >
                <Button
                    v-for="link in courses.links"
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
    </AppLayout>
</template>
