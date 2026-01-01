<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { 
  BookOpen, 
  Plus, 
  Edit, 
  Trash2, 
  Play,
  Eye
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from '@/components/ui/table';
import { BreadcrumbItem } from '@/types';

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
}

interface Course {
  id: number;
  title: string;
  slug: string;
}

interface Props {
  course: Course;
  module: Module;
  lessons: Lesson[];
}

const { props } = usePage<Props>();
const { course, module, lessons } = props;

// Format date function
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

// Format duration function
const formatDuration = (duration: number | undefined) => {
  if (!duration) return 'N/A';
  
  const hours = Math.floor(duration / 3600);
  const minutes = Math.floor((duration % 3600) / 60);
  const seconds = duration % 60;
  
  if (hours > 0) {
    return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  }
  
  return `${minutes}:${seconds.toString().padStart(2, '0')}`;
};

// Handle lesson deletion
const deleteLesson = (lessonId: number) => {
  if (confirm('Are you sure you want to delete this lesson?')) {
    router.delete(`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lessonId}`, {
      onSuccess: () => {
        console.log('Lesson deleted successfully');
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

// Get YouTube ID from URL
const getYouTubeId = (url: string | undefined): string | null => {
  if (!url) return null;
  
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
  const match = url.match(regExp);
  
  return (match && match[2].length === 11) ? match[2] : null;
};

// Generate YouTube embed URL
const getYouTubeEmbedUrl = (url: string | undefined): string | null => {
  const id = getYouTubeId(url);
  return id ? `https://www.youtube.com/embed/${id}` : null;
};
</script>

<template>
  <InstructorLayout 
    :breadcrumbs="[
      { title: 'Home', href: '/' },
      { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
      { title: 'Courses', href: '/instructor/courses' },
      { title: course.title, href: `/instructor/courses/${course.slug}` },
      { title: 'Modules', href: `/instructor/courses/${course.slug}/modules` },
      { title: module.title, href: `/instructor/courses/${course.slug}/modules/${module.id}` },
      { title: 'Lessons', href: `/instructor/courses/${course.slug}/modules/${module.id}/lessons` }
    ]"
  >
    <Head :title="`${module.title} - Lessons`" />
    
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Module Lessons</h1>
          <p class="text-muted-foreground mt-2">
            Manage lessons for {{ module.title }}
          </p>
        </div>
        
        <Button as-child>
          <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons/create`">
            <Plus class="h-4 w-4 mr-2" />
            Add Lesson
          </a>
        </Button>
      </div>

      <!-- Lessons Table -->
      <Card>
        <CardContent class="p-0 mt-4">
          <Table v-if="lessons.length > 0">
            <TableHeader>
              <TableRow>
                <TableHead>Lesson</TableHead>
                <TableHead>Duration</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Preview</TableHead>
                <TableHead>Created</TableHead>
                <TableHead class="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="lesson in lessons" :key="lesson.id">
                <TableCell class="font-medium">
                  <div class="flex items-center space-x-4">
                    <div class="flex-1">
                      <div class="font-medium flex items-center">
                        <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}`" class="hover:text-blue-600 transition-colors">
                          {{ lesson.title }}
                        </a>
                        <Play v-if="lesson.video_url" class="h-4 w-4 ml-2 text-muted-foreground" />
                      </div>
                      <div class="text-sm text-muted-foreground line-clamp-1">
                        {{ lesson.description }}
                      </div>
                    </div>
                  </div>
                </TableCell>
                <TableCell>
                  {{ formatDuration(lesson.duration) }}
                </TableCell>
                <TableCell>
                  <div v-if="lesson.published_at">
                    <div class="font-medium text-green-600">Published</div>
                    <div class="text-xs text-gray-500">{{ formatDate(lesson.published_at) }}</div>
                  </div>
                  <div v-else class="text-yellow-600">Scheduled</div>
                </TableCell>
                <TableCell>
                  <Badge v-if="lesson.is_preview" variant="secondary">
                    Preview
                  </Badge>
                  <span v-else>-</span>
                </TableCell>
                <TableCell>{{ formatDate(lesson.created_at) }}</TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end space-x-2">
                    <Button variant="outline" size="sm" as-child>
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
            <h3 class="mt-2 text-sm font-medium">No lessons</h3>
            <p class="mt-1 text-sm text-muted-foreground">
              Get started by creating a new lesson.
            </p>
            <div class="mt-6">
              <Button as-child>
                <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons/create`">
                  <Plus class="h-4 w-4 mr-2" />
                  Add Lesson
                </a>
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </InstructorLayout>
</template>