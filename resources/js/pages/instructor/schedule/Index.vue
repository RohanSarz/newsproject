<script setup lang="ts">
import InstructorLayout from '@/layouts/InstructorLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Calendar, Clock, BookOpen, Video, ChevronRight } from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface ScheduledContent {
  id: number;
  title: string;
  type: 'module' | 'lesson';
  module?: string;
  course: string;
  course_slug: string;
  published_at: string;
  days_until: number;
}

interface Props {
  scheduledContent: ScheduledContent[];
}

const { props } = usePage<Props>();
const { scheduledContent } = props;

const groupedByTime = (content: ScheduledContent[]) => {
  const groups: Record<string, ScheduledContent[]> = {
    'Today': [],
    'Tomorrow': [],
    'This Week': [],
    'Later': [],
  };

  content.forEach((item) => {
    if (item.days_until === 0) {
      groups['Today'].push(item);
    } else if (item.days_until === 1) {
      groups['Tomorrow'].push(item);
    } else if (item.days_until <= 7) {
      groups['This Week'].push(item);
    } else {
      groups['Later'].push(item);
    }
  });

  return groups;
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  });
};

const getTypeColor = (type: string) => {
  return type === 'module'
    ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-100'
    : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100';
};

const getTypeIcon = (type: string) => {
  return type === 'module' ? BookOpen : Video;
};
</script>

<template>
  <Head title="Content Schedule | CodeRept" />
  <InstructorLayout
    :breadcrumbs="[
      { title: 'Home', href: '/' },
      { title: 'Instructor Dashboard', href: '/instructor/dashboard' },
      { title: 'Schedule', href: '/instructor/schedule' }
    ]"
  >
    <div class="mx-auto max-w-6xl p-6 md:p-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">
          Content Schedule
        </h1>
        <p class="mt-2 text-muted-foreground">
          Manage your upcoming content releases
        </p>
      </div>

      <!-- Empty State -->
      <div
        v-if="scheduledContent.length === 0"
        class="rounded-lg border border-dashed border-gray-300 bg-gray-50 py-16 text-center"
      >
        <Calendar class="mx-auto mb-4 h-16 w-16 text-gray-400" />
        <h3 class="text-lg font-medium text-gray-900">
          No scheduled content
        </h3>
        <p class="mt-1 text-sm text-gray-500">
          You haven't scheduled any modules or lessons for future release.
        </p>
      </div>

      <!-- Scheduled Content -->
      <div v-else class="space-y-8">
        <div
          v-for="(group, groupName) in groupedByTime(scheduledContent)"
          :key="groupName"
        >
          <div v-if="group.length > 0">
            <h2 class="mb-4 text-xl font-semibold text-gray-900">
              {{ groupName }}
            </h2>

            <div class="space-y-3">
              <Card
                v-for="item in group"
                :key="item.id"
                class="hover:shadow-md transition-shadow"
              >
                <CardContent class="flex items-center gap-4 p-4">
                  <!-- Icon -->
                  <div
                    class="flex h-12 w-12 items-center justify-center rounded-full"
                    :class="getTypeColor(item.type)"
                  >
                    <component :is="getTypeIcon(item.type)" class="h-6 w-6" />
                  </div>

                  <!-- Content -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                      <h3 class="font-semibold text-gray-900">
                        {{ item.title }}
                      </h3>
                      <Badge :class="getTypeColor(item.type)" class="text-xs">
                        {{ item.type }}
                      </Badge>
                    </div>

                    <div class="mt-1 flex flex-wrap items-center gap-3 text-sm text-gray-600">
                      <span>{{ item.course }}</span>
                      <span v-if="item.module" class="flex items-center gap-1">
                        <ChevronRight class="h-3 w-3" />
                        {{ item.module }}
                      </span>
                    </div>
                  </div>

                  <!-- Date -->
                  <div class="text-right">
                    <div class="text-sm font-medium text-gray-900">
                      {{ formatDate(item.published_at) }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ item.days_until === 0 ? 'Today' : `in ${item.days_until} days` }}
                    </div>
                  </div>

                  <!-- Actions -->
                  <div>
                    <Link
                      :href="`/instructor/courses/${item.course_slug}`"
                      class="text-sm text-blue-600 hover:text-blue-700"
                    >
                      View
                    </Link>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </div>
    </div>
  </InstructorLayout>
</template>
