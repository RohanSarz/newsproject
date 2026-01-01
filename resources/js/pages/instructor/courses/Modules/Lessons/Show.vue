<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import {
  ArrowLeft,
  Edit,
  Trash2,
  Play,
  Eye,
  EyeOff,
  Clock,
  BookOpen,
  CheckCircle
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { BreadcrumbItem } from '@/types';

interface Course {
  id: number;
  title: string;
  slug: string;
}

interface Module {
  id: number;
  title: string;
  slug: string;
}

interface Lesson {
  id: number;
  title: string;
  description: string;
  content: string;
  video_url: string;
  duration: number;
  is_preview: boolean;
  published_at: string | null;
  order: number;
}

interface Props {
  course: Course;
  module: Module;
  lesson: Lesson;
}

const { props } = usePage<Props>();
const { course, module, lesson } = props;

const deleteLesson = () => {
  if (confirm('Are you sure you want to delete this lesson? This action cannot be undone.')) {
    router.delete(`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}`);
  }
};

const formatDuration = (seconds: number) => {
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const getYoutubeEmbedUrl = (url: string) => {
  if (!url) return null;
  const match = url.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&\n?#]+)/);
  return match ? `https://www.youtube.com/embed/${match[1]}` : null;
};

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Home', href: '/' },
  { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
  { title: 'Courses', href: '/instructor/courses' },
  { title: course.title, href: `/instructor/courses/${course.slug}` },
  { title: 'Modules', href: `/instructor/courses/${course.slug}/modules` },
  { title: module.title, href: `/instructor/courses/${course.slug}/modules/${module.id}` },
  { title: lesson.title, href: '#' }
];
</script>

<template>
  <Head :title="`${lesson.title} | ${module.title} | CodeRept`" />
  <InstructorLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-5xl p-6 md:p-8">
      <!-- Header -->
      <div class="mb-6 flex items-start justify-between">
        <div class="flex-1">
          <Button
            variant="ghost"
            size="sm"
            class="mb-4"
            as-child
          >
            <a :href="`/instructor/courses/${course.slug}/modules/${module.id}`">
              <ArrowLeft class="mr-2 h-4 w-4" />
              Back to Module
            </a>
          </Button>

          <div class="mb-3 flex items-center gap-2">
            <div v-if="lesson.published_at" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
              <CheckCircle class="mr-1 h-3 w-3" />
              Published
            </div>
            <div v-else class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
              <EyeOff class="mr-1 h-3 w-3" />
              Scheduled
            </div>
            <Badge v-if="lesson.is_preview" variant="outline">
              <Eye class="mr-1 h-3 w-3" />
              Free Preview
            </Badge>
            <Badge v-if="lesson.published_at" variant="outline" class="text-xs">
              Scheduled: {{ new Date(lesson.published_at).toLocaleString() }}
            </Badge>
          </div>

          <h1 class="text-2xl font-semibold text-gray-900 md:text-3xl">
            {{ lesson.title }}
          </h1>
          <p v-if="lesson.description" class="mt-2 text-gray-600">
            {{ lesson.description }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
          <Button
            variant="outline"
            size="sm"
            as-child
          >
            <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}/edit`">
              <Edit class="mr-2 h-4 w-4" />
              Edit
            </a>
          </Button>
          <Button
            variant="destructive"
            size="sm"
            @click="deleteLesson"
          >
            <Trash2 class="mr-2 h-4 w-4" />
            Delete
          </Button>
        </div>
      </div>

      <!-- Video Preview -->
      <Card v-if="lesson.video_url" class="mb-6">
        <CardContent class="p-0">
          <div class="aspect-video w-full overflow-hidden rounded-lg bg-black">
            <iframe
              v-if="getYoutubeEmbedUrl(lesson.video_url)"
              :src="getYoutubeEmbedUrl(lesson.video_url)"
              class="h-full w-full"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>
            <div v-else class="flex h-full items-center justify-center">
              <Play class="h-16 w-16 text-gray-600" />
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Lesson Details -->
      <div class="grid gap-6 md:grid-cols-3">
        <!-- Main Content -->
        <div class="space-y-6 md:col-span-2">
          <!-- Content -->
          <Card>
            <CardHeader>
              <CardTitle class="text-lg">Lesson Content</CardTitle>
            </CardHeader>
            <CardContent>
              <div v-if="lesson.content" class="prose max-w-none text-gray-700">
                {{ lesson.content }}
              </div>
              <p v-else class="text-sm text-gray-500 italic">
                No content added yet
              </p>
            </CardContent>
          </Card>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
          <!-- Lesson Info -->
          <Card>
            <CardHeader>
              <CardTitle class="text-lg">Lesson Info</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="flex items-center gap-2 text-sm">
                <Clock class="h-4 w-4 text-gray-500" />
                <span class="text-gray-600">Duration:</span>
                <span class="font-medium">{{ formatDuration(lesson.duration) }}</span>
              </div>

              <div class="flex items-center gap-2 text-sm">
                <BookOpen class="h-4 w-4 text-gray-500" />
                <span class="text-gray-600">Order:</span>
                <span class="font-medium">{{ lesson.order }}</span>
              </div>

              <div class="flex items-center gap-2 text-sm">
                <CheckCircle v-if="lesson.published_at" class="h-4 w-4 text-green-600" />
                <Clock v-else class="h-4 w-4 text-yellow-500" />
                <span class="text-gray-600">Status:</span>
                <span class="font-medium">{{ lesson.published_at ? 'Published' : 'Scheduled' }}</span>
              </div>

              <div v-if="lesson.video_url" class="flex items-start gap-2 text-sm">
                <Play class="h-4 w-4 text-gray-500 mt-0.5" />
                <div class="flex-1 min-w-0">
                  <span class="text-gray-600">Video URL:</span>
                  <a
                    :href="lesson.video_url"
                    target="_blank"
                    rel="noopener"
                    class="block font-medium text-blue-600 truncate hover:underline"
                  >
                    {{ lesson.video_url }}
                  </a>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Publishing Status -->
          <Card>
            <CardHeader>
              <CardTitle class="text-lg">Publishing</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Published:</span>
                <Badge :variant="lesson.published_at ? 'default' : 'secondary'">
                  {{ lesson.published_at ? 'Yes' : 'No' }}
                </Badge>
              </div>

              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Free Preview:</span>
                <Badge :variant="lesson.is_preview ? 'default' : 'outline'">
                  {{ lesson.is_preview ? 'Yes' : 'No' }}
                </Badge>
              </div>

              <div v-if="lesson.published_at" class="text-sm">
                <span class="text-gray-600">Published At:</span>
                <div class="mt-1 font-medium">
                  {{ new Date(lesson.published_at).toLocaleString() }}
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </InstructorLayout>
</template>
