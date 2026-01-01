<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 40 40"
        :class="className"
        v-bind="$attrs"
    >
        <!-- Code bracket representation -->
        <path d="M10 10 L8 15 L8 25 L10 30" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
        <path d="M30 10 L32 15 L32 25 L30 30" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>

        <!-- Central code symbol -->
        <path d="M18 15 L22 20 L18 25" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>

        <!-- Repository symbol (folder-like) -->
        <path d="M12 12 L28 12 L30 15 L30 28 L10 28 L12 25 Z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</template>
