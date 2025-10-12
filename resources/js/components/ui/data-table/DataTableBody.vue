<script setup lang="ts">
withDefaults(defineProps<{
  customClass?: string;
}>(), {
  customClass: '',
});
</script>

<template>
  <tbody :class="['divide-y border-b', customClass]">
    <slot />
  </tbody>
</template>