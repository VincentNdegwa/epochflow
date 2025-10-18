<template>
    <div class="flex items-center justify-between">
        <!-- Previous Page Link -->
        <Link
            v-if="!links[0].url"
            class="relative inline-flex cursor-default items-center px-4 py-2 text-sm font-medium text-foreground/50"
        >
            Previous
        </Link>
        <Link
            v-else
            :href="links[0].url"
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-foreground transition-colors hover:text-primary"
        >
            Previous
        </Link>

        <!-- Pagination Elements -->
        <div class="hidden md:flex">
            <template v-for="(link, key) in links.slice(1, -1)" :key="key">
                <div
                    v-if="link.url === null"
                    class="relative mx-1 inline-flex cursor-default items-center px-4 py-2 text-sm font-medium text-foreground/50"
                >
                    ...
                </div>
                <Link
                    v-else
                    :href="link.url"
                    class="relative mx-1 inline-flex items-center rounded-md px-4 py-2 text-sm font-medium"
                    :class="[
                        link.active
                            ? 'z-10 bg-primary text-primary-foreground'
                            : 'text-foreground transition-colors hover:text-primary',
                    ]"
                >
                    {{ link.label }}
                </Link>
            </template>
        </div>

        <!-- Next Page Link -->
        <Link
            v-if="!links[links.length - 1].url"
            class="relative inline-flex cursor-default items-center px-4 py-2 text-sm font-medium text-foreground/50"
        >
            Next
        </Link>
        <Link
            v-else
            :href="links[links.length - 1].url"
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-foreground transition-colors hover:text-primary"
        >
            Next
        </Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>
