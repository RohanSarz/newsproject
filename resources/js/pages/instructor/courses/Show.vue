<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import {
  BookOpen,
  Plus,
  Edit,
  Trash2,
  Play,
  Eye,
  DollarSign,
  User,
  Calendar,
  FileText,
  BarChart3
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Module {
  id: number;
  title: string;
  description: string;
  order: number;
  published_at: string | null;
  created_at: string;
  lesson_count: number;
}

interface Lesson {
  id: number;
  title: string;
  order: number;
  is_preview: boolean;
  published_at: string | null;
  video_url?: string;
}

interface Course {
  id: number;
  title: string;
  slug: string;
  description: string;
  overview: string;
  level: string;
  price: number | string;
  status: string;
  status_label?: string;
  created_at: string;
  published_at?: string;
  instructor?: {
    name: string;
    email?: string;
  };
}

interface FormData {
  title: string;
  description: string;
  order: number;
  publish_now: boolean;
  published_at: string;
}

interface Props {
  course: Course;
  modules: Module[];
}

const { props } = usePage<Props>();
const { course, modules } = props;

const formData = ref<FormData>({
  title: '',
  description: '',
  order: modules.length,
  publish_now: false,
  published_at: ''
});

const errors = ref<Record<string, string>>({});

const submitForm = () => {
  errors.value = {};

  router.post(`/instructor/courses/${course.slug}/modules`, {
    title: formData.value.title,
    description: formData.value.description,
    order: formData.value.order,
    publish_now: formData.value.publish_now,
    published_at: formData.value.published_at
  }, {
    onError: (errs) => {
      errors.value = errs;
    },
    onSuccess: () => {
      // Reset form
      formData.value = {
        title: '',
        description: '',
        order: modules.length,
        publish_now: false,
        published_at: ''
      };
      // In a real app, we would update the modules list without a full page reload
      router.reload({ only: ['modules'] });
    }
  });
};

// Handle module deletion
const deleteModule = (moduleId: number) => {
  if (confirm('Are you sure you want to delete this module? This will also delete all lessons in this module.')) {
    router.delete(`/instructor/courses/${course.slug}/modules/${moduleId}`, {
      onSuccess: () => {
        // In a real app, we would update the modules list without a full page reload
        router.reload({ only: ['modules'] });
      }
    });
  }
};

// Handle status badge variant for modules
const getStatusVariant = (publishedAt: string | null) => {
  return publishedAt
    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100';
};

// Handle course status badge variant
const getCourseStatusVariant = (status: string) => {
  switch (status) {
    case 'published':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100';
    case 'draft':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100';
    case 'archived':
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100';
  }
};

// Get level badge color
const getLevelVariant = (level: string) => {
  switch (level) {
    case 'beginner':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100';
    case 'intermediate':
      return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-100';
    case 'advanced':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100';
  }
};

// Format date function
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};
</script>

<template>
  <InstructorLayout 
    :breadcrumbs="[
      { title: 'Home', href: '/' },
      { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
      { title: 'Courses', href: '/instructor/courses' },
      { title: course.title, href: `/instructor/courses/${course.slug}` }
    ]"
  >
    <Head :title="course.title" />
    
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">{{ course.title }}</h1>
          <p class="text-muted-foreground mt-2">
            Manage your course and its modules
          </p>
        </div>
        
        <Button as-child>
          <a :href="`/instructor/courses/${course.slug}/edit`">
            <Edit class="h-4 w-4 mr-2" />
            Edit Course
          </a>
        </Button>
      </div>

      <!-- Course Information -->
      <Card>
        <CardHeader>
          <div class="flex items-center justify-between">
            <CardTitle>Course Information</CardTitle>
            <div class="flex items-center space-x-2">
              <Badge :class="getCourseStatusVariant(course.status)">
                {{ course.status_label || course.status }}
              </Badge>
              <Badge :class="getLevelVariant(course.level)">
                {{ course.level.charAt(0).toUpperCase() + course.level.slice(1) }}
              </Badge>
            </div>
          </div>
        </CardHeader>
        <CardContent class="space-y-4">
          <!-- Description -->
          <div v-if="course.description">
            <div class="flex items-start space-x-2">
              <FileText class="h-4 w-4 mt-1 text-muted-foreground" />
              <div>
                <h4 class="font-medium text-sm">Description</h4>
                <p class="text-sm text-muted-foreground mt-1">{{ course.description }}</p>
              </div>
            </div>
          </div>

          <!-- Overview -->
          <div v-if="course.overview">
            <div class="flex items-start space-x-2">
              <BarChart3 class="h-4 w-4 mt-1 text-muted-foreground" />
              <div>
                <h4 class="font-medium text-sm">Overview</h4>
                <p class="text-sm text-muted-foreground mt-1 whitespace-pre-wrap">{{ course.overview }}</p>
              </div>
            </div>
          </div>

          <!-- Course Details Grid -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
            <div class="flex items-center space-x-2">
              <DollarSign class="h-4 w-4 text-muted-foreground" />
              <div>
                <h4 class="font-medium text-sm">Price</h4>
                <p class="text-sm text-muted-foreground">${{ Number(course.price).toFixed(2) }}</p>
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <User class="h-4 w-4 text-muted-foreground" />
              <div>
                <h4 class="font-medium text-sm">Instructor</h4>
                <p class="text-sm text-muted-foreground">{{ course.instructor?.name || 'Not specified' }}</p>
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <Calendar class="h-4 w-4 text-muted-foreground" />
              <div>
                <h4 class="font-medium text-sm">Published</h4>
                <p class="text-sm text-muted-foreground">
                  {{ course.published_at ? formatDate(course.published_at) : 'Not published' }}
                </p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Course Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium">Total Modules</CardTitle>
            <BookOpen class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ modules.length }}</div>
            <p class="text-xs text-muted-foreground">Course modules</p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium">Total Lessons</CardTitle>
            <Play class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">
              {{ modules.reduce((sum, module) => sum + (module.lesson_count || 0), 0) }}
            </div>
            <p class="text-xs text-muted-foreground">Lessons in all modules</p>
          </CardContent>
        </Card>
      </div>

      <!-- Module Creation Form and Module List -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Module Creation Form -->
        <div class="lg:col-span-1">
          <Card>
            <CardHeader>
              <CardTitle>Create New Module</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="space-y-2">
                <Label for="title">Module Title *</Label>
                <Input 
                  id="title" 
                  v-model="formData.title" 
                  placeholder="Enter module title"
                  :class="{ 'border-red-500': errors.title }"
                />
                <p v-if="errors.title" class="text-sm text-red-500">{{ errors.title }}</p>
              </div>

              <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea 
                  id="description" 
                  v-model="formData.description" 
                  placeholder="Brief description of the module"
                  :class="{ 'border-red-500': errors.description }"
                />
                <p v-if="errors.description" class="text-sm text-red-500">{{ errors.description }}</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="order">Order</Label>
                  <Input 
                    id="order" 
                    v-model.number="formData.order" 
                    type="number" 
                    min="0"
                    placeholder="0"
                    :class="{ 'border-red-500': errors.order }"
                  />
                  <p v-if="errors.order" class="text-sm text-red-500">{{ errors.order }}</p>
                </div>

                <div class="space-y-2">
                  <div class="flex items-center space-x-2 pt-6">
                    <Checkbox
                      id="publish_now"
                      v-model:checked="formData.publish_now"
                    />
                    <Label for="publish_now">Publish Immediately</Label>
                  </div>
                </div>

                <div v-if="!formData.publish_now" class="space-y-2">
                  <Label for="published_at">Schedule Publishing Date</Label>
                  <Input
                    id="published_at"
                    v-model="formData.published_at"
                    type="datetime-local"
                  />
                  <p class="text-xs text-muted-foreground">
                    Students will see this module but content will be locked until this date
                  </p>
                </div>
              </div>

              <Button class="w-full" @click="submitForm">
                <Plus class="h-4 w-4 mr-2" />
                Create Module
              </Button>
            </CardContent>
          </Card>
        </div>

        <!-- Module List -->
        <div class="lg:col-span-2">
          <Card>
            <CardHeader>
              <CardTitle>Course Modules</CardTitle>
            </CardHeader>
            <CardContent>
              <div v-if="modules.length > 0" class="space-y-4">
                <div v-for="module in modules" :key="module.id" class="border rounded-lg p-4">
                  <div class="flex justify-between items-start">
                    <div>
                      <Link
                        :href="`/instructor/courses/${course.slug}/modules/${module.id}`"
                        class="font-semibold text-lg hover:text-blue-600 transition-colors"
                      >
                        {{ module.title }}
                      </Link>
                      <p class="text-sm text-muted-foreground mt-1">{{ module.description }}</p>
                      <div class="flex items-center mt-2 space-x-4">
                        <div v-if="module.published_at" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                          <span>Published</span>
                          <span class="ml-1 text-xs text-green-600 dark:text-green-300">({{ formatDate(module.published_at) }})</span>
                        </div>
                        <div v-else class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
                          <span>Scheduled</span>
                        </div>
                        <span class="text-sm text-muted-foreground">
                          {{ module.lesson_count }} lessons
                        </span>
                        <span class="text-sm text-muted-foreground">
                          Created: {{ formatDate(module.created_at) }}
                        </span>
                      </div>
                    </div>
                    <div class="flex space-x-2">
                      <Button
                        variant="default"
                        size="sm"
                        as-child
                      >
                        <a :href="`/instructor/courses/${course.slug}/modules/${module.id}`">
                          <Eye class="h-4 w-4 mr-1" />
                          View
                        </a>
                      </Button>
                      <Button
                        variant="outline"
                        size="sm"
                        as-child
                      >
                        <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/edit`">
                          <Edit class="h-4 w-4 mr-1" />
                          Edit
                        </a>
                      </Button>
                      <Button
                        variant="outline"
                        size="sm"
                        @click="deleteModule(module.id)"
                      >
                        <Trash2 class="h-4 w-4 mr-1" />
                        Delete
                      </Button>
                    </div>
                  </div>
                  
                  <!-- Lessons for this module -->
                  <div v-if="module.lessons && module.lessons.length > 0" class="mt-4 pl-4 border-l-2 border-muted-foreground/20">
                    <h4 class="font-medium text-sm mb-2">Lessons in this module:</h4>
                    <ul class="space-y-2">
                      <li 
                        v-for="lesson in module.lessons" 
                        :key="lesson.id" 
                        class="flex justify-between items-center py-2 border-b border-muted-foreground/10"
                      >
                        <div class="flex items-center space-x-2">
                          <Play class="h-4 w-4 text-muted-foreground" />
                          <Link
                            :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}/edit`"
                            class="hover:text-blue-600 transition-colors"
                          >
                            {{ lesson.title }}
                          </Link>
                          <div v-if="lesson.published_at" class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                            <span>Published</span>
                            <span class="ml-1 text-xs text-green-600 dark:text-green-300">({{ formatDate(lesson.published_at) }})</span>
                          </div>
                          <div v-else class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
                            <span>Scheduled</span>
                          </div>
                          <Badge v-if="lesson.is_preview" variant="secondary" class="ml-2">
                            Preview
                          </Badge>
                          <Badge v-if="lesson.video_url" variant="outline" class="ml-2">
                            <Play class="h-3 w-3 mr-1" />
                            Video
                          </Badge>
                        </div>
                        <Button 
                          variant="ghost" 
                          size="sm" 
                          as-child
                        >
                          <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}/edit`">
                            Edit
                          </a>
                        </Button>
                      </li>
                    </ul>
                  </div>
                  <div v-else class="mt-4 pl-4 border-l-2 border-muted-foreground/20">
                    <p class="text-sm text-muted-foreground">No lessons in this module yet.</p>
                  </div>
                </div>
              </div>
              
              <div v-else class="text-center py-8">
                <BookOpen class="mx-auto h-12 w-12 text-muted-foreground" />
                <h3 class="mt-2 text-sm font-medium">No modules</h3>
                <p class="mt-1 text-sm text-muted-foreground">
                  Get started by creating your first module.
                </p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </InstructorLayout>
</template>