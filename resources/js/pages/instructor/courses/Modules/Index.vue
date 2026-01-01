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
}

interface Props {
  course: Course;
  modules: Module[];
}

const { props } = usePage<Props>();
const { course, modules } = props;

// Format date function
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

// Handle module deletion
const deleteModule = (moduleId: number) => {
  if (confirm('Are you sure you want to delete this module? This will also delete all lessons in this module.')) {
    router.delete(`/instructor/courses/${course.slug}/modules/${moduleId}`, {
      onSuccess: () => {
        console.log('Module deleted successfully');
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

// Toggle module status
const toggleModuleStatus = (module: Module) => {
  // For published_at, we'll toggle between setting a date and null
  const newPublishedAt = module.published_at ? null : new Date().toISOString().slice(0, 16);
  router.put(`/instructor/courses/${course.slug}/modules/${module.id}`, {
    title: module.title,
    description: module.description,
    order: module.order,
    published_at: newPublishedAt
  }, {
    onSuccess: () => {
      // Update the module status in the UI
      module.published_at = newPublishedAt;
    },
    onError: (errors) => {
      console.error('Error updating module status:', errors);
    }
  });
};

// Toggle lesson status
const toggleLessonStatus = (lesson: Lesson, module: Module) => {
  // For published_at, we'll toggle between setting a date and null
  const newPublishedAt = lesson.published_at ? null : new Date().toISOString().slice(0, 16);
  router.put(`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}`, {
    title: lesson.title,
    description: lesson.description || '',
    content: lesson.content || '',
    video_url: lesson.video_url || '',
    duration: lesson.duration || 0,
    order: lesson.order,
    is_preview: lesson.is_preview,
    published_at: newPublishedAt
  }, {
    onSuccess: () => {
      // Update the lesson status in the UI
      lesson.published_at = newPublishedAt;
    },
    onError: (errors) => {
      console.error('Error updating lesson status:', errors);
    }
  });
};

// Get YouTube ID from URL
const getYouTubeId = (url: string | undefined): string | null => {
  if (!url) return null;
  
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
  const match = url.match(regExp);
  
  return (match && match[2].length === 11) ? match[2] : null;
};
</script>

<template>
  <InstructorLayout 
    :breadcrumbs="[
      { title: 'Home', href: '/' },
      { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
      { title: 'Courses', href: '/instructor/courses' },
      { title: course.title, href: `/instructor/courses/${course.slug}` },
      { title: 'Modules', href: `/instructor/courses/${course.slug}/modules` }
    ]"
  >
    <Head :title="`${course.title} - Modules`" />
    
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Course Modules</h1>
          <p class="text-muted-foreground mt-2">
            Manage modules for {{ course.title }}
          </p>
        </div>
        
        <Button as-child>
          <a :href="`/instructor/courses/${course.slug}/modules/create`">
            <Plus class="h-4 w-4 mr-2" />
            Add Module
          </a>
        </Button>
      </div>

      <!-- Modules Table -->
      <Card>
        <CardContent class="p-0 mt-4">
          <Table v-if="modules.length > 0">
            <TableHeader>
              <TableRow>
                <TableHead>Module</TableHead>
                <TableHead>Lessons</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Created</TableHead>
                <TableHead class="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-for="module in modules" :key="module.id">
                <TableRow>
                  <TableCell class="font-medium">
                    <div class="flex items-center space-x-4">
                      <div class="flex-1">
                        <div class="font-medium">
                          <a :href="`/instructor/courses/${course.slug}/modules/${module.id}`" class="hover:text-blue-600 transition-colors">
                            {{ module.title }}
                          </a>
                        </div>
                        <div class="text-sm text-muted-foreground line-clamp-1">
                          {{ module.description }}
                        </div>
                      </div>
                    </div>
                  </TableCell>
                  <TableCell>
                    <span class="font-medium">{{ module.lesson_count }}</span> lessons
                  </TableCell>
                  <TableCell>
                    <div v-if="module.published_at">
                      <div class="font-medium text-green-600">Published</div>
                      <div class="text-xs text-gray-500">{{ formatDate(module.published_at) }}</div>
                    </div>
                    <div v-else class="text-yellow-600">Scheduled</div>
                  </TableCell>
                  <TableCell>{{ formatDate(module.created_at) }}</TableCell>
                  <TableCell class="text-right">
                    <div class="flex justify-end space-x-2">
                      <Button variant="outline" size="sm" as-child>
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
                  </TableCell>
                </TableRow>
                
                <!-- Lessons for this module -->
                <TableRow v-for="lesson in module.lessons" :key="lesson.id" class="bg-muted/20">
                  <TableCell class="pl-12" :colspan="5">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center space-x-2">
                        <Play class="h-4 w-4 text-muted-foreground" />
                        <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}`" class="font-medium hover:text-blue-600 transition-colors">
                          {{ lesson.title }}
                        </a>
                        <Badge v-if="lesson.is_preview" variant="secondary" class="ml-2">
                          Preview
                        </Badge>
                        <Badge v-if="lesson.video_url" variant="outline" class="ml-2">
                          <Play class="h-3 w-3 mr-1" />
                          Video
                        </Badge>
                        <div v-if="lesson.published_at" class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                          <span>Published</span>
                          <span class="ml-1 text-xs text-green-600 dark:text-green-300">({{ formatDate(lesson.published_at) }})</span>
                        </div>
                        <div v-else class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
                          <span>Scheduled</span>
                        </div>
                      </div>
                      <div class="flex space-x-2">
                        <Button variant="ghost" size="sm" as-child>
                          <a :href="`/instructor/courses/${course.slug}/modules/${module.id}/lessons/${lesson.id}/edit`">
                            <Edit class="h-4 w-4 mr-1" />
                            Edit
                          </a>
                        </Button>
                      </div>
                    </div>
                  </TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>
          
          <div v-else class="text-center py-12">
            <BookOpen class="mx-auto h-12 w-12 text-muted-foreground" />
            <h3 class="mt-2 text-sm font-medium">No modules</h3>
            <p class="mt-1 text-sm text-muted-foreground">
              Get started by creating a new module.
            </p>
            <div class="mt-6">
              <Button as-child>
                <a :href="`/instructor/courses/${course.slug}/modules/create`">
                  <Plus class="h-4 w-4 mr-2" />
                  Add Module
                </a>
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </InstructorLayout>
</template>