<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { Head, router, Link, usePage } from '@inertiajs/vue3';
import {
  ArrowLeft,
  BookOpen,
  Plus,
  Edit,
  Trash2,
  Play,
  Eye,
  Clock
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { ref } from 'vue';

interface Lesson {
  id: number;
  title: string;
  description: string;
  order: number;
  is_preview: boolean;
  published_at: string | null;
  video_url?: string;
  duration?: number;
  created_at: string;
}

interface Module {
  id: number;
  title: string;
  description: string;
  order: number;
  published_at: string | null;
  created_at: string;
}

interface Course {
  id: number;
  title: string;
  slug: string;
  instructor: {
    name: string;
  };
}

interface FormData {
  title: string;
  description: string;
  content: string;
  video_url: string;
  order: number;
  publish_now: boolean;
  published_at: string;
  is_preview: boolean;
}

interface Props {
  course: Course;
  module: Module & { lessons: Lesson[] };
}

const { props } = usePage<Props>();
const { course, module } = props;

const formData = ref<FormData>({
  title: '',
  description: '',
  content: '',
  video_url: '',
  order: module.lessons.length,
  publish_now: false,
  published_at: '',
  is_preview: false
});

const errors = ref<Record<string, string>>({});

const submitForm = () => {
  errors.value = {};

  router.post(`/instructor/courses/${course.slug}/modules/${module.id}/lessons`, {
    title: formData.value.title,
    description: formData.value.description,
    content: formData.value.content,
    video_url: formData.value.video_url,
    order: formData.value.order,
    publish_now: formData.value.publish_now,
    published_at: formData.value.published_at,
    is_preview: formData.value.is_preview
  }, {
    onError: (errs) => {
      errors.value = errs;
    },
    onSuccess: () => {
      // Reset form
      formData.value = {
        title: '',
        description: '',
        content: '',
        video_url: '',
        order: module.lessons.length,
        publish_now: false,
        published_at: '',
        is_preview: false
      };
      // Reload to get updated lessons list
      router.reload({ only: ['module'] });
    }
  });
};

// Handle lesson deletion
const deleteLesson = (lessonId: number) => {
  if (confirm('Are you sure you want to delete this lesson?')) {
    router.delete(`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lessonId}`, {
      onSuccess: () => {
        router.reload({ only: ['module'] });
      }
    });
  }
};

// Handle status badge variant
const getStatusVariant = (publishedAt: string | null) => {
  return publishedAt
    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100';
};

// Format duration function
const formatDuration = (seconds: number) => {
  if (!seconds) return '--:--';
  const hours = Math.floor(seconds / 3600);
  const minutes = Math.floor((seconds % 3600) / 60);
  const secs = seconds % 60;

  if (hours > 0) {
    return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
  }
  return `${minutes}:${secs.toString().padStart(2, '0')}`;
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
      { title: course.title, href: `/instructor/courses/${course.slug}` },
      { title: module.title, href: `/instructor/courses/${course.slug}/modules/${module.id}` }
    ]"
  >
    <Head :title="`${module.title} - ${course.title}`" />

    <div class="space-y-6">
      <!-- Header with back button -->
      <div class="flex items-center space-x-4">
        <Button variant="ghost" size="sm" as-child>
          <a :href="`/instructor/courses/${course.slug}`">
            <ArrowLeft class="h-4 w-4 mr-2" />
            Back to Course
          </a>
        </Button>
      </div>

      <!-- Module Information -->
      <div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <div class="flex items-center space-x-3">
              <h1 class="text-3xl font-bold tracking-tight">{{ module.title }}</h1>
              <div v-if="module.published_at" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                <span>Published</span>
                <span class="ml-1 text-xs text-green-600 dark:text-green-300">({{ formatDate(module.published_at) }})</span>
              </div>
              <div v-else class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
                <span>Scheduled</span>
              </div>
            </div>
            <p v-if="module.description" class="text-muted-foreground mt-2">
              {{ module.description }}
            </p>
            <p class="text-sm text-muted-foreground mt-1">
              Order: {{ module.order }} • Created: {{ formatDate(module.created_at) }}
            </p>
          </div>

          <div class="flex space-x-2">
            <Button variant="outline" as-child>
              <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/edit`">
                <Edit class="h-4 w-4 mr-2" />
                Edit Module
              </a>
            </Button>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium">Total Lessons</CardTitle>
            <Play class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ module.lessons.length }}</div>
            <p class="text-xs text-muted-foreground">Lessons in this module</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium">Published Lessons</CardTitle>
            <Eye class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">
              {{ module.lessons.filter(l => l.published_at).length }}
            </div>
            <p class="text-xs text-muted-foreground">Visible to students</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium">Total Duration</CardTitle>
            <Clock class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">
              {{
                formatDuration(
                  module.lessons.reduce((sum, lesson) => sum + (lesson.duration || 0), 0)
                )
              }}
            </div>
            <p class="text-xs text-muted-foreground">Total content time</p>
          </CardContent>
        </Card>
      </div>

      <!-- Lesson Creation Form and Lesson List -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lesson Creation Form -->
        <div class="lg:col-span-1">
          <Card>
            <CardHeader>
              <CardTitle>Create New Lesson</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="space-y-2">
                <Label for="title">Lesson Title *</Label>
                <Input
                  id="title"
                  v-model="formData.title"
                  placeholder="Enter lesson title"
                  :class="{ 'border-red-500': errors.title }"
                />
                <p v-if="errors.title" class="text-sm text-red-500">{{ errors.title }}</p>
              </div>

              <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea
                  id="description"
                  v-model="formData.description"
                  placeholder="Brief description of the lesson"
                  rows="2"
                  :class="{ 'border-red-500': errors.description }"
                />
                <p v-if="errors.description" class="text-sm text-red-500">{{ errors.description }}</p>
              </div>

              <div class="space-y-2">
                <Label for="content">Content</Label>
                <Textarea
                  id="content"
                  v-model="formData.content"
                  placeholder="Main content of the lesson"
                  rows="3"
                  :class="{ 'border-red-500': errors.content }"
                />
                <p v-if="errors.content" class="text-sm text-red-500">{{ errors.content }}</p>
              </div>

              <div class="space-y-2">
                <Label for="video_url">Video URL (YouTube)</Label>
                <Input
                  id="video_url"
                  v-model="formData.video_url"
                  type="url"
                  placeholder="https://youtube.com/watch?v=..."
                  :class="{ 'border-red-500': errors.video_url }"
                />
                <p v-if="errors.video_url" class="text-sm text-red-500">{{ errors.video_url }}</p>
                <p class="text-xs text-muted-foreground">
                  Paste a YouTube URL and we'll extract the video ID
                </p>
              </div>

              <div class="grid grid-cols-2 gap-4">
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
              </div>

              <div class="space-y-3">
                <div class="flex items-center space-x-2">
                  <Checkbox
                    id="publish_now"
                    v-model:checked="formData.publish_now"
                  />
                  <Label for="publish_now">Publish Immediately</Label>
                </div>

                <div v-if="!formData.publish_now" class="space-y-2">
                  <Label for="published_at">Schedule Publishing Date</Label>
                  <Input
                    id="published_at"
                    v-model="formData.published_at"
                    type="datetime-local"
                  />
                  <p class="text-xs text-muted-foreground">
                    Students will see this lesson but content will be locked until this date
                  </p>
                </div>

                <div class="flex items-center space-x-2">
                  <Checkbox
                    id="is_preview"
                    v-model:checked="formData.is_preview"
                  />
                  <Label for="is_preview">Free Preview</Label>
                </div>
              </div>

              <Button class="w-full" @click="submitForm">
                <Plus class="h-4 w-4 mr-2" />
                Create Lesson
              </Button>
            </CardContent>
          </Card>
        </div>

        <!-- Lesson List -->
        <div class="lg:col-span-2">
          <Card>
            <CardHeader>
              <CardTitle>Module Lessons</CardTitle>
            </CardHeader>
            <CardContent>
              <div v-if="module.lessons.length > 0" class="space-y-3">
                <div
                  v-for="lesson in module.lessons"
                  :key="lesson.id"
                  class="border rounded-lg p-4 hover:bg-muted/50 transition-colors"
                >
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <div class="flex items-center space-x-3">
                        <Play class="h-4 w-4 text-muted-foreground" />
                        <Link
                          :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}`"
                          class="font-semibold hover:text-blue-600 transition-colors"
                        >
                          {{ lesson.title }}
                        </Link>
                        <div v-if="lesson.published_at" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                          <span>Published</span>
                          <span class="ml-1 text-xs text-green-600 dark:text-green-300">({{ formatDate(lesson.published_at) }})</span>
                        </div>
                        <div v-else class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
                          <span>Scheduled</span>
                        </div>
                        <Badge v-if="lesson.is_preview" variant="secondary">
                          <Eye class="h-3 w-3 mr-1" />
                          Preview
                        </Badge>
                        <Badge v-if="lesson.video_url" variant="outline">
                          <Play class="h-3 w-3 mr-1" />
                          Video
                        </Badge>
                      </div>

                      <p v-if="lesson.description" class="text-sm text-muted-foreground mt-2">
                        {{ lesson.description }}
                      </p>

                      <div class="flex items-center mt-2 space-x-4 text-sm text-muted-foreground">
                        <span>Order: {{ lesson.order }}</span>
                        <span v-if="lesson.duration">
                          <Clock class="h-3 w-3 inline mr-1" />
                          {{ formatDuration(lesson.duration) }}
                        </span>
                        <span>Created: {{ formatDate(lesson.created_at) }}</span>
                      </div>
                    </div>

                    <div class="flex space-x-2 ml-4">
                      <Button
                        variant="outline"
                        size="sm"
                        as-child
                      >
                        <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}/edit`">
                          <Edit class="h-4 w-4 mr-1" />
                          Edit
                        </a>
                      </Button>
                      <Button
                        variant="outline"
                        size="sm"
                        @click="deleteLesson(lesson.id)"
                      >
                        <Trash2 class="h-4 w-4" />
                      </Button>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else class="text-center py-8">
                <Play class="mx-auto h-12 w-12 text-muted-foreground" />
                <h3 class="mt-2 text-sm font-medium">No lessons</h3>
                <p class="mt-1 text-sm text-muted-foreground">
                  Get started by creating your first lesson.
                </p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </InstructorLayout>
</template>
