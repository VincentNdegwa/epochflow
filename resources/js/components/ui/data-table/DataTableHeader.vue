<script setup lang="ts">
withDefaults(defineProps<{
  customClass?: string;
}>(), {
  customClass: '',
});
</script>

<template>
  <thead :class="['[&_tr]:border-b', customClass]">
    <slot />
  </thead>
</template>