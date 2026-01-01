<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
  BookOpen,
  Plus,
  Search,
  Eye,
  Edit,
  Trash2,
  Clock,
  DollarSign,
  Users
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table';
import { BreadcrumbItem } from '@/types';
import routes from '@/routes/instructor/courses';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

// Define props interface
interface Course {
  id: number;
  title: string;
  description: string;
  students_count?: number;
  created_at: string;
  status: string;
  level: string;
  price: number;
  slug: string;
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
    status?: string;
  };
}

const { props } = usePage<Props>();
const { courses, filters = {} } = props;

// Search suggestions
const searchInput = ref<HTMLInputElement | null>(null);
const showSuggestions = ref(false);
const searchTimeout = ref<number | null>(null);
const suggestions = ref<Course[]>([]);

// Reactive search value to maintain input state
const searchValue = ref(props.filters?.search || '');

// Format date function
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

// Format price function
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(price);
};

// Handle course deletion
const deleteCourse = (slug: string) => {
  if (confirm('Are you sure you want to delete this course?')) {
    router.delete(routes.destroy({ course: slug }).url, {
      onSuccess: () => {
        // Show success message
        console.log('Course deleted successfully');
      }
    });
  }
};

// Handle status badge variant
const getStatusVariant = (status: string) => {
  switch (status) {
    case 'published':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100';
    case 'draft':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100';
    case 'archived':
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100';
  }
};

// Filter suggestions based on current input
const filteredSuggestions = computed(() => {
  // Use the reactive search value
  const currentInput = searchValue.value || filters.search || '';

  if (!currentInput) return suggestions.value;
  return suggestions.value.filter(course =>
    course.title.toLowerCase().includes(currentInput.toLowerCase())
  );
});

// Handle search input with debouncing
const handleSearchInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const currentValue = target.value;

  // Clear previous timeout
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }

  // Set new timeout for debouncing
  searchTimeout.value = window.setTimeout(() => {
    // Update suggestions based on current courses
    if (currentValue.length > 0) {
      suggestions.value = courses.data.filter(course =>
        course.title.toLowerCase().includes(currentValue.toLowerCase())
      );
      showSuggestions.value = true;
    } else {
      showSuggestions.value = false;
      suggestions.value = [];
    }

    // Only update the filter if the search value has actually changed
    if (filters.search !== currentValue) {
      router.get('/instructor/courses', { ...filters, search: currentValue }, { preserveState: true, preserveScroll: true });
    }
  }, 300);
};

// Handle search selection
const selectSuggestion = (courseTitle: string) => {
  searchValue.value = courseTitle;
  router.get('/instructor/courses', { ...filters, search: courseTitle }, { preserveState: true, preserveScroll: true });
  showSuggestions.value = false;
};

// Handle click outside to close suggestions
const handleClickOutside = (event: Event) => {
  if (searchInput.value && !searchInput.value.contains(event.target as Node)) {
    showSuggestions.value = false;
  }
};

// Handle Enter key press
const handleEnterKey = () => {
  if (showSuggestions.value && filteredSuggestions.value.length > 0) {
    selectSuggestion(filteredSuggestions.value[0].title);
  } else {
    // If no suggestions are shown, trigger search with current input
    router.get('/instructor/courses', { ...filters, search: searchValue.value }, { preserveState: true, preserveScroll: true });
  }
};

// Add event listener when component mounts
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

// Watch for changes in filters to ensure UI updates
watch(
    () => props.filters,
    (newFilters) => {
        console.log('Filters updated:', newFilters);
        // Update the reactive search value to reflect current filter value
        searchValue.value = newFilters?.search || '';
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

// Remove event listener when component unmounts
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
});
</script>

<template>
  <InstructorLayout
    :breadcrumbs="[
      { title: 'Home', href: '/' },
      { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
      { title: 'Courses', href: '/instructor/courses' }
    ]"
  >
    <Head title="Instructor Courses" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">My Courses</h1>
          <p class="text-muted-foreground mt-2">
            Manage and track your courses
          </p>
        </div>

        <Button as-child>
          <Link :href="routes.create().url">
            <Plus class="h-4 w-4 mr-2" />
            Create Course
          </Link>
        </Button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium">Total Courses</CardTitle>
            <BookOpen class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ courses?.meta?.total || 0 }}</div>
            <p class="text-xs text-muted-foreground">All your courses</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium">Published</CardTitle>
            <Eye class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">
              {{ (courses?.data || []).filter(course => course.status === 'published').length }}
            </div>
            <p class="text-xs text-muted-foreground">Live courses</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium">Drafts</CardTitle>
            <Edit class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">
              {{ (courses?.data || []).filter(course => course.status === 'draft').length }}
            </div>
            <p class="text-xs text-muted-foreground">Work in progress</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium">Total Revenue</CardTitle>
            <DollarSign class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">
              {{ formatPrice(0) }}
            </div>
            <p class="text-xs text-muted-foreground">Potential earnings</p>
          </CardContent>
        </Card>
      </div>

      <!-- Courses Table -->
      <Card>
        <CardContent class="p-0 mt-4">
          <!-- Filters and Search -->
          <div class="p-6 pb-4">
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="relative flex-1">
                <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input
                  ref="searchInput"
                  type="search"
                  placeholder="Search courses..."
                  class="pl-8"
                  v-model="searchValue"
                  @input="handleSearchInput"
                @keydown.enter="handleEnterKey"
                @focus="showSuggestions = true"
                />

                <!-- Search Suggestions Dropdown -->
                <div
                  v-if="showSuggestions && filteredSuggestions.length > 0"
                  class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto"
                >
                  <div
                    v-for="suggestion in filteredSuggestions"
                    :key="suggestion.id"
                    class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer"
                    @click="selectSuggestion(suggestion.title)"
                  >
                    <div class="font-medium">{{ suggestion.title }}</div>
                    <div class="text-sm text-muted-foreground truncate">{{ suggestion.description }}</div>
                  </div>
                </div>
              </div>

              <select
                class="border rounded-md px-3 py-2 bg-background"
                :value="filters.status || ''"
                @change="router.get('/instructor/courses', { ...filters, status: ($event.target as HTMLSelectElement).value }, { preserveState: true, preserveScroll: true })"
              >
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
              </select>
            </div>
          </div>

          <Table v-if="courses.data && courses.data.length > 0">
            <TableHeader>
              <TableRow>
                <TableHead>Course</TableHead>
                <TableHead>Level</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Price</TableHead>
                <TableHead>Created</TableHead>
                <TableHead class="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="course in courses.data" :key="course.id">
                <TableCell class="font-medium">
                  <div class="flex items-center space-x-4">
                    <div class="flex-1">
                      <Link
                        :href="routes.show({ course: course.slug }).url"
                        class="font-medium hover:text-blue-600 transition-colors"
                      >
                        {{ course.title }}
                      </Link>
                      <div class="text-sm text-muted-foreground line-clamp-1">
                        {{ course.description }}
                      </div>
                    </div>
                  </div>
                </TableCell>
                <TableCell>
                  <Badge variant="outline">{{ course.level }}</Badge>
                </TableCell>
                <TableCell>
                  <Badge :class="getStatusVariant(course.status)">
                    {{ course.status.charAt(0).toUpperCase() + course.status.slice(1) }}
                  </Badge>
                </TableCell>
                <TableCell>{{ formatPrice(course.price) }}</TableCell>
                <TableCell>{{ formatDate(course.created_at) }}</TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end space-x-2">
                    <Button variant="outline" size="sm" as-child>
                      <Link :href="routes.edit({ course: course.slug }).url">
                        <Edit class="h-4 w-4 mr-1" />
                        Edit
                      </Link>
                    </Button>
                    <Button
                      variant="outline"
                      size="sm"
                      @click="deleteCourse(course.slug)"
                    >
                      <Trash2 class="h-4 w-4 mr-1" />
                      Delete
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>

          <div v-else class="text-center py-12">
            <BookOpen class="mx-auto h-12 w-12 text-muted-foreground" />
            <h3 class="mt-2 text-sm font-medium">No courses</h3>
            <p class="mt-1 text-sm text-muted-foreground">
              No courses found with the current filters.
            </p>
            <div class="mt-6">
              <Button as-child>
                <Link :href="routes.create().url">
                  <Plus class="h-4 w-4 mr-2" />
                  Create Course
                </Link>
              </Button>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="courses.data && courses.data.length > 0 && courses.links.length > 3" class="flex items-center justify-between border-t px-6 py-3">
            <div class="text-sm text-muted-foreground">
              Showing {{ courses.meta.from }} to {{ courses.meta.to }} of {{ courses.meta.total }} results
            </div>
            <div class="flex space-x-2">
              <Button
                v-for="(link, index) in courses.links"
                :key="index"
                :disabled="!link.url || link.active"
                :variant="link.active ? 'default' : 'outline'"
                size="sm"
                @click="link.url && router.visit(link.url, { preserveState: true, preserveScroll: true })"
                v-html="link.label.replace('Previous', '&laquo;').replace('Next', '&raquo;')"
              />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </InstructorLayout>
</template>