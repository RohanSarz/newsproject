<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Play } from 'lucide-vue-next';

interface Module {
  id: number;
  title: string;
}

interface Course {
  id: number;
  title: string;
  slug: string;
}

interface FormData {
  title: string;
  description: string;
  content: string;
  video_url: string;
  duration: number;
  order: number;
  is_preview: boolean;
  publish_now: boolean;
  published_at: string;
}

interface Props {
  course: Course;
  module: Module;
}

const { props } = usePage<Props>();
const { course, module } = props;

const formData = ref<FormData>({
  title: '',
  description: '',
  content: '',
  video_url: '',
  duration: 0,
  order: 0,
  is_preview: false,
  publish_now: false,
  published_at: ''
});

const errors = ref<Record<string, string>>({});
const videoPreview = ref<HTMLIFrameElement | null>(null);

const submitForm = () => {
  errors.value = {};
  
  router.post(`/instructor/courses/${course.id}/modules/${module.id}/lessons`, {
    title: formData.value.title,
    description: formData.value.description,
    content: formData.value.content,
    video_url: formData.value.video_url,
    duration: formData.value.duration,
    order: formData.value.order,
    is_preview: formData.value.is_preview,
    publish_now: formData.value.publish_now,
    published_at: formData.value.published_at
  }, {
    onError: (errs) => {
      errors.value = errs;
    },
    onSuccess: () => {
      // Redirect to lessons index on success
      router.visit(`/instructor/courses/${course.id}/modules/${module.id}/lessons`);
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

// Update duration based on YouTube video
const updateDuration = () => {
  // In a real implementation, we would fetch the video duration from YouTube API
  // For now, we'll just show a placeholder
  if (formData.value.video_url) {
    // This is a simplified example - in a real app, you'd call the YouTube API
    // to get the actual video duration
    console.log("Would fetch duration for video:", getYouTubeId(formData.value.video_url));
  }
};
</script>

<template>
  <InstructorLayout 
    :breadcrumbs="[
      { title: 'Home', href: '/' },
      { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
      { title: 'Courses', href: '/instructor/courses' },
      { title: course.title, href: `/instructor/courses/${course.id}` },
      { title: module.title, href: `/instructor/courses/${course.id}/modules/${module.id}` },
      { title: 'Lessons', href: `/instructor/courses/${course.id}/modules/${module.id}/lessons` },
      { title: 'Create', href: `/instructor/courses/${course.id}/modules/${module.id}/lessons/create` }
    ]"
  >
    <Head :title="`Create Lesson - ${module.title}`" />
    
    <div class="max-w-4xl mx-auto space-y-6">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Create Lesson</h1>
        <p class="text-muted-foreground mt-2">
          Add a new lesson to {{ module.title }}
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Lesson Details</CardTitle>
          <CardDescription>
            Fill in the information for your new lesson
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
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
              :class="{ 'border-red-500': errors.description }"
            />
            <p v-if="errors.description" class="text-sm text-red-500">{{ errors.description }}</p>
          </div>

          <Tabs defaultValue="content" class="w-full">
            <TabsList class="grid w-full grid-cols-2">
              <TabsTrigger value="content">Content</TabsTrigger>
              <TabsTrigger value="video">Video</TabsTrigger>
            </TabsList>
            <TabsContent value="content" class="space-y-4">
              <div class="space-y-2">
                <Label for="content">Lesson Content</Label>
                <Textarea 
                  id="content" 
                  v-model="formData.content" 
                  placeholder="Enter lesson content (text, HTML, etc.)"
                  rows="8"
                  :class="{ 'border-red-500': errors.content }"
                />
                <p v-if="errors.content" class="text-sm text-red-500">{{ errors.content }}</p>
              </div>
            </TabsContent>
            <TabsContent value="video" class="space-y-4">
              <div class="space-y-2">
                <Label for="video_url">YouTube Video URL</Label>
                <Input 
                  id="video_url" 
                  v-model="formData.video_url" 
                  placeholder="https://www.youtube.com/watch?v=..."
                  @input="updateDuration"
                  :class="{ 'border-red-500': errors.video_url }"
                />
                <p v-if="errors.video_url" class="text-sm text-red-500">{{ errors.video_url }}</p>
                <p class="text-sm text-muted-foreground">
                  Paste a YouTube URL to embed the video in this lesson
                </p>
              </div>

              <div v-if="getYouTubeEmbedUrl(formData.video_url)" class="space-y-2">
                <Label>Video Preview</Label>
                <div class="aspect-video bg-muted rounded-lg overflow-hidden">
                  <iframe
                    :src="getYouTubeEmbedUrl(formData.video_url)"
                    class="w-full h-full"
                    frameborder="0"
                    allowfullscreen
                  ></iframe>
                </div>
              </div>
            </TabsContent>
          </Tabs>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                  id="is_preview"
                  v-model:checked="formData.is_preview"
                />
                <Label for="is_preview">Preview Lesson</Label>
              </div>
              <p class="text-sm text-muted-foreground">
                Allow students to view without enrollment
              </p>
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
          </div>

          <!-- Publishing Options -->
          <div v-if="!formData.publish_now" class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <h3 class="font-medium text-gray-900">Publishing Options</h3>

            <!-- Scheduled Publishing Date -->
            <div class="space-y-2">
              <Label for="published_at">Schedule Publishing Date</Label>
              <Input
                id="published_at"
                v-model="formData.published_at"
                type="datetime-local"
                :class="{ 'border-red-500': errors.published_at }"
              />
              <p class="text-xs text-gray-500">
                Students will see this lesson but content will be locked until this date
              </p>
              <p v-if="errors.published_at" class="text-sm text-red-500">{{ errors.published_at }}</p>
            </div>
          </div>

          <div class="flex justify-end space-x-4 pt-4">
            <Button variant="outline" as-child>
              <a :href="`/instructor/courses/${course.id}/modules/${module.id}/lessons`">Cancel</a>
            </Button>
            <Button @click="submitForm">
              <Play class="h-4 w-4 mr-2" />
              Create Lesson
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </InstructorLayout>
</template>