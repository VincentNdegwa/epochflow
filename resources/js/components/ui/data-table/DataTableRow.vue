<script setup lang="ts">
withDefaults(defineProps<{
  customClass?: string;
}>(), {
  customClass: '',
});
</script>

<template>
  <tr
    :class="[
      'border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted',
      customClass,
    ]"
  >
    <slot />
  </tr>
</template>