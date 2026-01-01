<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { useForm } from '@inertiajs/vue3';
import routes from '@/routes/instructor/courses';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Define types
interface Course {
  id: number;
  title: string;
  slug: string;
  description: string;
  overview: string;
  level: string;
  price: number | string;
  status: string;
  published_at?: string;
}

interface Props {
  course: Course;
}

interface FormData {
  title: string;
  description: string;
  overview: string;
  level: string;
  price: string;
  status: string;
  publish_now: boolean;
  published_at: string;
}

const { props } = usePage<Props>();
const { course } = props;

// Format published_at for datetime-local input (YYYY-MM-DDTHH:mm)
const formatPublishedAt = (dateString?: string) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const formData = ref<FormData>({
  title: course.title,
  description: course.description || '',
  overview: course.overview || '',
  level: course.level,
  price: course.price.toString(),
  status: course.status,
  publish_now: false,
  published_at: formatPublishedAt(course.published_at)
});

const errors = ref<Record<string, string>>({});

const submitForm = () => {
  errors.value = {};

  router.put(routes.update({ course: course.slug }).url, {
    ...formData.value,
    price: parseFloat(formData.value.price)
  }, {
    onError: (errs) => {
      errors.value = errs;
    },
    onSuccess: () => {
      // Redirect to courses index on success
      router.visit('/instructor/courses');
    }
  });
};

// Quick create module form
const moduleForm = useForm({
  title: '',
  description: '',
  order: 0,
});
</script>

<template>
  <InstructorLayout 
    :breadcrumbs="[
      { title: 'Home', href: '/' },
      { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
      { title: 'Courses', href: '/instructor/courses' },
      { title: course.title, href: `/instructor/courses/${course.slug}` },
      { title: 'Edit', href: `/instructor/courses/${course.slug}/edit` }
    ]"
  >
    <Head title="Edit Course" />
    
    <div class="max-w-3xl mx-auto space-y-6">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Edit Course</h1>
        <p class="text-muted-foreground mt-2">
          Update the information for your course
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Course Details</CardTitle>
          <CardDescription>
            Update the information for your course
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="space-y-2">
            <Label for="title">Course Title *</Label>
            <Input 
              id="title" 
              v-model="formData.title" 
              placeholder="Enter course title"
              :class="{ 'border-red-500': errors.title }"
            />
            <p v-if="errors.title" class="text-sm text-red-500">{{ errors.title }}</p>
          </div>

          <div class="space-y-2">
            <Label for="description">Description</Label>
            <Textarea 
              id="description" 
              v-model="formData.description" 
              placeholder="Brief description of the course"
              :class="{ 'border-red-500': errors.description }"
            />
            <p v-if="errors.description" class="text-sm text-red-500">{{ errors.description }}</p>
          </div>

          <div class="space-y-2">
            <Label for="overview">Overview</Label>
            <Textarea 
              id="overview" 
              v-model="formData.overview" 
              placeholder="Detailed overview of the course content"
              rows="5"
              :class="{ 'border-red-500': errors.overview }"
            />
            <p v-if="errors.overview" class="text-sm text-red-500">{{ errors.overview }}</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="level">Level *</Label>
              <Select v-model="formData.level" :class="{ 'border-red-500': errors.level }">
                <SelectTrigger id="level">
                  <SelectValue placeholder="Select level" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="beginner">Beginner</SelectItem>
                  <SelectItem value="intermediate">Intermediate</SelectItem>
                  <SelectItem value="advanced">Advanced</SelectItem>
                </SelectContent>
              </Select>
              <p v-if="errors.level" class="text-sm text-red-500">{{ errors.level }}</p>
            </div>

            <div class="space-y-2">
              <Label for="price">Price ($)</Label>
              <Input 
                id="price" 
                v-model="formData.price" 
                type="number" 
                step="0.01"
                min="0"
                placeholder="0.00"
                :class="{ 'border-red-500': errors.price }"
              />
              <p v-if="errors.price" class="text-sm text-red-500">{{ errors.price }}</p>
            </div>
          </div>

          <div class="space-y-2">
            <Label for="status">Status</Label>
            <Select v-model="formData.status" :class="{ 'border-red-500': errors.status }">
              <SelectTrigger id="status">
                <SelectValue placeholder="Select status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="draft">Draft</SelectItem>
                <SelectItem value="published">Published</SelectItem>
                <SelectItem value="archived">Archived</SelectItem>
              </SelectContent>
            </Select>
            <p v-if="errors.status" class="text-sm text-red-500">{{ errors.status }}</p>
          </div>

          <!-- Publishing Options -->
          <div
            v-if="formData.status === 'published'"
            class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
          >
            <h3 class="font-medium">Publishing Options</h3>

            <!-- Publish Now -->
            <div class="flex items-center space-x-2">
              <Checkbox
                id="publish_now"
                :checked="formData.publish_now"
                @update:checked="formData.publish_now = $event"
              />
              <Label for="publish_now" class="cursor-pointer">
                Publish immediately
              </Label>
            </div>

            <!-- Scheduled Publishing Date -->
            <div
              v-if="formData.status === 'published' && !formData.publish_now"
              class="space-y-2"
            >
              <Label for="published_at">Schedule Publishing Date</Label>
              <Input
                id="published_at"
                v-model="formData.published_at"
                type="datetime-local"
                :class="{ 'border-red-500': errors.published_at }"
              />
              <p class="text-xs text-muted-foreground">
                Students will see this course but content will be locked until this date
              </p>
              <p v-if="errors.published_at" class="text-sm text-red-500">
                {{ errors.published_at }}
              </p>
            </div>
          </div>

          <div class="flex justify-end space-x-4 pt-4">
            <Button variant="outline" as-child>
              <a :href="`/instructor/courses/${course.slug}`">Cancel</a>
            </Button>
            <Button @click="submitForm">Update Course</Button>
          </div>
        </CardContent>
      </Card>

      <!-- Quick Create Module -->
      <Card>
        <CardHeader>
          <CardTitle>Quick Add Module</CardTitle>
          <CardDescription>
            Quickly create a new module for this course
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="moduleForm.post(`/instructor/courses/${course.slug}/modules`, {
            onError: (errs) => {
              errors.value = errs;
            },
            onSuccess: () => {
              moduleForm.reset();
            }
          })" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="space-y-2">
                <Label for="module_title">Module Title *</Label>
                <Input
                  id="module_title"
                  v-model="moduleForm.title"
                  placeholder="e.g., Introduction to Laravel"
                  :disabled="moduleForm.processing"
                />
                <p v-if="moduleForm.errors.title" class="text-sm text-red-500">
                  {{ moduleForm.errors.title }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="module_order">Order *</Label>
                <Input
                  id="module_order"
                  v-model.number="moduleForm.order"
                  type="number"
                  min="0"
                  placeholder="0"
                  :disabled="moduleForm.processing"
                />
                <p v-if="moduleForm.errors.order" class="text-sm text-red-500">
                  {{ moduleForm.errors.order }}
                </p>
              </div>
            </div>

            <div class="space-y-2">
              <Label for="module_description">Description</Label>
              <Textarea
                id="module_description"
                v-model="moduleForm.description"
                placeholder="Brief overview of this module..."
                rows="2"
                :disabled="moduleForm.processing"
              />
            </div>

            <div class="flex items-center gap-4 pt-2">
              <Button
                type="submit"
                :disabled="moduleForm.processing"
                class="w-full md:w-auto"
              >
                {{ moduleForm.processing ? 'Creating...' : 'Create Module' }}
              </Button>

              <Button
                variant="outline"
                type="button"
                as-child
                class="w-full md:w-auto"
              >
                <Link :href="`/instructor/courses/${course.slug}/modules`">
                  View All Modules
                </Link>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </InstructorLayout>
</template>