<script setup lang="ts">
import type { TableProps } from './types';
import { computed } from 'vue';

const props = withDefaults(defineProps<{
  customClass?: string;
}>(), {
  customClass: '',
});

const tableClass = computed(() => [
  'w-full caption-bottom text-sm',
  props.customClass,
]);
</script>

<template>
  <div class="relative w-full overflow-auto">
    <table :class="tableClass">
      <slot />
    </table>
  </div>
</template>