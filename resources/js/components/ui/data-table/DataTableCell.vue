<script setup lang="ts">
withDefaults(defineProps<{
  customClass?: string;
}>(), {
  customClass: '',
});
</script>

<template>
  <td :class="['p-4 align-middle [&:has([role=checkbox])]:pr-0', customClass]">
    <slot />
  </td>
</template>