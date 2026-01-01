<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <tr :class="cn('h-24 border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted', props.class)">
    <td :class="cn('p-4 align-middle text-center text-muted-foreground', props.class)" colspan="100%">
      <slot />
    </td>
  </tr>
</template>