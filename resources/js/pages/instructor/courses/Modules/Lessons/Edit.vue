<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Save, Play } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';

interface Course {
  id: number;
  title: string;
  slug: string;
}

interface Module {
  id: number;
  title: string;
}

interface Lesson {
  id: number;
  title: string;
  description: string;
  content: string;
  video_url: string;
  duration: number;
  order: number;
  is_preview: boolean;
  published_at: string | null;
}

interface Props {
  course: Course;
  module: Module;
  lesson: Lesson;
}

const { props } = usePage<Props>();
const { course, module, lesson } = props;

const form = useForm({
  title: lesson.title,
  description: lesson.description || '',
  content: lesson.content || '',
  video_url: lesson.video_url || '',
  duration: lesson.duration,
  order: lesson.order,
  is_preview: lesson.is_preview,
  publish_now: false,
  published_at: lesson.published_at ? lesson.published_at.slice(0, 16) : '',
});

const submitForm = () => {
  form.put(`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}`, {
    title: form.title,
    description: form.description,
    content: form.content,
    video_url: form.video_url,
    duration: form.duration,
    order: form.order,
    is_preview: form.is_preview,
    publish_now: form.publish_now,
    published_at: form.published_at
  }, {
    onSuccess: () => {
      // Lesson updated successfully
    }
  });
};

// Get YouTube ID from URL
const getYouTubeId = (url: string): string | null => {
  if (!url) return null;

  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
  const match = url.match(regExp);

  return (match && match[2].length === 11) ? match[2] : null;
};

// Generate YouTube embed URL
const getYouTubeEmbedUrl = (url: string): string | null => {
  const id = getYouTubeId(url);
  return id ? `https://www.youtube.com/embed/${id}` : null;
};
</script>

<template>
  <Head title="Edit Lesson | CodeRept" />
  <InstructorLayout
    :breadcrumbs="[
      { title: 'Home', href: '/' },
      { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
      { title: 'Courses', href: '/instructor/courses' },
      { title: course.title, href: `/instructor/courses/${course.slug}` },
      { title: 'Modules', href: `/instructor/courses/${course.slug}/modules` },
      { title: module.title, href: `/instructor/courses/${course.slug}/modules/${module.id}` },
      { title: 'Edit Lesson', href: '#' }
    ]"
  >
    <div class="mx-auto max-w-4xl p-6 md:p-8">
      <!-- Header -->
      <div class="mb-6">
        <Button
          variant="ghost"
          size="sm"
          class="mb-4"
          as-child
        >
          <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons`">
            <ArrowLeft class="mr-2 h-4 w-4" />
            Back to Lessons
          </a>
        </Button>

        <h1 class="text-2xl font-semibold text-gray-900 md:text-3xl">
          Edit Lesson
        </h1>
        <p class="mt-2 text-sm text-gray-600">
          Update lesson content for {{ module.title }}
        </p>
      </div>

      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Form -->
        <div class="lg:col-span-2">
          <Card>
            <CardContent class="p-6">
              <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Title -->
                <div class="space-y-2">
                  <Label for="title">Lesson Title *</Label>
                  <Input
                    id="title"
                    v-model="form.title"
                    placeholder="e.g., Introduction to Laravel Routing"
                    :disabled="form.processing"
                    required
                  />
                  <p v-if="form.errors.title" class="text-sm text-red-600">
                    {{ form.errors.title }}
                  </p>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                  <Label for="description">Description</Label>
                  <Textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Brief summary of what this lesson covers..."
                    rows="2"
                    :disabled="form.processing"
                  />
                  <p v-if="form.errors.description" class="text-sm text-red-600">
                    {{ form.errors.description }}
                  </p>
                </div>

                <!-- Video URL -->
                <div class="space-y-2">
                  <Label for="video_url">Video URL (YouTube)</Label>
                  <Input
                    id="video_url"
                    v-model="form.video_url"
                    type="url"
                    placeholder="https://www.youtube.com/watch?v=..."
                    :disabled="form.processing"
                  />
                  <p class="text-xs text-gray-500">
                    Paste a YouTube URL to embed the video
                  </p>
                  <p v-if="form.errors.video_url" class="text-sm text-red-600">
                    {{ form.errors.video_url }}
                  </p>
                </div>

                <!-- Content -->
                <div class="space-y-2">
                  <Label for="content">Lesson Content</Label>
                  <Textarea
                    id="content"
                    v-model="form.content"
                    placeholder="Additional lesson content, notes, or resources..."
                    rows="6"
                    :disabled="form.processing"
                  />
                  <p v-if="form.errors.content" class="text-sm text-red-600">
                    {{ form.errors.content }}
                  </p>
                </div>

                <!-- Duration -->
                <div class="space-y-2">
                  <Label for="duration">Duration (seconds) *</Label>
                  <Input
                    id="duration"
                    v-model.number="form.duration"
                    type="number"
                    min="0"
                    placeholder="600"
                    :disabled="form.processing"
                    required
                  />
                  <p class="text-xs text-gray-500">
                    Video duration in seconds (e.g., 600 for 10 minutes)
                  </p>
                  <p v-if="form.errors.duration" class="text-sm text-red-600">
                    {{ form.errors.duration }}
                  </p>
                </div>

                <!-- Order -->
                <div class="space-y-2">
                  <Label for="order">Order *</Label>
                  <Input
                    id="order"
                    v-model.number="form.order"
                    type="number"
                    min="0"
                    placeholder="0"
                    :disabled="form.processing"
                    required
                  />
                  <p class="text-xs text-gray-500">
                    The order in which this lesson appears in the module
                  </p>
                  <p v-if="form.errors.order" class="text-sm text-red-600">
                    {{ form.errors.order }}
                  </p>
                </div>

                <!-- Publishing Options -->
                <div class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                  <h3 class="font-medium text-gray-900">Publishing Options</h3>

                  <!-- Is Preview -->
                  <div class="flex items-center space-x-2">
                    <Checkbox
                      id="is_preview"
                      :checked="form.is_preview"
                      @update:checked="form.is_preview = $event"
                    />
                    <Label for="is_preview" class="cursor-pointer">
                      Free Preview (available without enrollment)
                    </Label>
                  </div>

                  <!-- Publish Now -->
                  <div class="flex items-center space-x-2">
                    <Checkbox
                      id="publish_now"
                      :checked="form.publish_now"
                      @update:checked="form.publish_now = $event"
                    />
                    <Label for="publish_now" class="cursor-pointer">
                      Publish immediately
                    </Label>
                  </div>

                  <!-- Scheduled Publishing Date -->
                  <div v-if="!form.publish_now" class="space-y-2">
                    <Label for="published_at">Schedule Publishing Date</Label>
                    <Input
                      id="published_at"
                      v-model="form.published_at"
                      type="datetime-local"
                      :disabled="form.processing"
                    />
                    <p class="text-xs text-gray-500">
                      Students will see this lesson but content will be locked until this date
                    </p>
                    <p v-if="form.errors.published_at" class="text-sm text-red-600">
                      {{ form.errors.published_at }}
                    </p>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4">
                  <Button
                    type="button"
                    variant="outline"
                    :disabled="form.processing"
                    as-child
                  >
                    <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons`">
                      Cancel
                    </a>
                  </Button>
                  <Button
                    type="submit"
                    :disabled="form.processing"
                  >
                    <Save class="mr-2 h-4 w-4" />
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>
        </div>

        <!-- Preview Sidebar -->
        <div class="space-y-6">
          <!-- Video Preview -->
          <Card>
            <CardContent class="p-4">
              <h3 class="mb-3 font-semibold text-gray-900">Video Preview</h3>
              <div class="aspect-video overflow-hidden rounded-lg bg-black">
                <iframe
                  v-if="getYouTubeEmbedUrl(form.video_url)"
                  :src="getYouTubeEmbedUrl(form.video_url)"
                  class="h-full w-full"
                  frameborder="0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen
                ></iframe>
                <div v-else class="flex h-full items-center justify-center">
                  <Play class="h-12 w-12 text-gray-600" />
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Current Settings -->
          <Card>
            <CardContent class="p-4">
              <h3 class="mb-3 font-semibold text-gray-900">Current Settings</h3>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-600">Status:</span>
                  <span class="font-medium">{{ form.is_published ? 'Published' : 'Draft' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Preview:</span>
                  <span class="font-medium">{{ form.is_preview ? 'Yes' : 'No' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Duration:</span>
                  <span class="font-medium">{{ Math.floor(form.duration / 60) }}:{{ (form.duration % 60).toString().padStart(2, '0') }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Order:</span>
                  <span class="font-medium">{{ form.order }}</span>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </InstructorLayout>
</template>
