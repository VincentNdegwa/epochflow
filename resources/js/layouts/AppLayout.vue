<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import ToastLayout from './ToastLayout.vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const hasStore = usePage().props.hasStore as boolean;
if (!hasStore) {
    window.location.href = '/stores/create';
}
</script>

<template>
    <ToastLayout>
        <AppLayout :breadcrumbs="breadcrumbs">
            <slot />
        </AppLayout>
    </ToastLayout>
</template>
