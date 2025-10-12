<script setup lang="ts">
withDefaults(defineProps<{
  customClass?: string;
  align?: 'left' | 'center' | 'right';
}>(), {
  customClass: '',
  align: 'left',
});
</script>

<template>
  <th
    :class="[
      'h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0',
      align === 'center' && 'text-center',
      align === 'right' && 'text-right',
      customClass,
    ]"
  >
    <slot />
  </th>
</template>