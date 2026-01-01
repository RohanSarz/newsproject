<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
  description: string;
  order: number;
  is_published: boolean;
  published_at: string | null;
}

interface Props {
  course: Course;
  module: Module;
}

const { props } = usePage<Props>();
const { course, module } = props;

const form = useForm({
  title: module.title,
  description: module.description || '',
  order: module.order,
  publish_now: false,
  published_at: module.published_at ? module.published_at.slice(0, 16) : '',
});

const submitForm = () => {
  form.put(`/instructor/courses/${course.slug}/modules/${module.id}`, {
    onSuccess: () => {
      // Module updated successfully
    }
  });
};
</script>

<template>
  <Head title="Edit Module | CodeRept" />
  <InstructorLayout
    :breadcrumbs="[
      { title: 'Home', href: '/' },
      { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
      { title: 'Courses', href: '/instructor/courses' },
      { title: course.title, href: `/instructor/courses/${course.slug}` },
      { title: 'Edit Module', href: '#' }
    ]"
  >
    <div class="mx-auto max-w-3xl p-6 md:p-8">
      <!-- Header -->
      <div class="mb-6">
        <Button
          variant="ghost"
          size="sm"
          class="mb-4"
          as-child
        >
          <a :href="`/instructor/courses/${course.slug}/modules`">
            <ArrowLeft class="mr-2 h-4 w-4" />
            Back to Modules
          </a>
        </Button>

        <h1 class="text-2xl font-semibold text-gray-900 md:text-3xl">
          Edit Module
        </h1>
        <p class="mt-2 text-sm text-gray-600">
          Update module information for {{ course.title }}
        </p>
      </div>

      <!-- Form -->
      <Card>
        <CardContent class="p-6">
          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Title -->
            <div class="space-y-2">
              <Label for="title">Module Title *</Label>
              <Input
                id="title"
                v-model="form.title"
                placeholder="e.g., Introduction to Laravel"
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
                placeholder="Brief overview of what students will learn in this module..."
                rows="3"
                :disabled="form.processing"
              />
              <p v-if="form.errors.description" class="text-sm text-red-600">
                {{ form.errors.description }}
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
                The order in which this module appears in the course
              </p>
              <p v-if="form.errors.order" class="text-sm text-red-600">
                {{ form.errors.order }}
              </p>
            </div>

            <!-- Publishing Options -->
            <div class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
              <h3 class="font-medium text-gray-900">Publishing Options</h3>

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
                  Students will see this module but content will be locked until this date
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
                <a :href="`/instructor/courses/${course.slug}/modules`">
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
  </InstructorLayout>
</template>
