<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import type { Store } from '../types';

interface NavItem {
    name: string;
    href: string;
}

defineProps<{
    store: Store;
    navigation: NavItem[];
}>();
</script>

<template>
    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 sm:py-24 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Store info -->
                <div class="space-y-4">
                    <Link
                        :href="route('stores.show', { slug: store.slug })"
                        class="text-xl font-bold"
                    >
                        {{ store.name }}
                    </Link>
                    <p v-if="store.description" class="text-sm text-gray-600">
                        {{ store.description }}
                    </p>
                </div>

                <!-- Navigation -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">
                        Navigation
                    </h3>
                    <ul class="mt-4 space-y-3">
                        <li v-for="item in navigation" :key="item.name">
                            <a
                                :href="item.href"
                                class="text-sm text-gray-600 hover:text-gray-900"
                            >
                                {{ item.name }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div
                    v-if="
                        store.contact_email ||
                        store.contact_phone ||
                        store.address
                    "
                >
                    <h3 class="text-sm font-semibold text-gray-900">Contact</h3>
                    <ul class="mt-4 space-y-3">
                        <li v-if="store.contact_email">
                            <a
                                :href="'mailto:' + store.contact_email"
                                class="text-sm text-gray-600 hover:text-gray-900"
                            >
                                {{ store.contact_email }}
                            </a>
                        </li>
                        <li v-if="store.contact_phone">
                            <a
                                :href="'tel:' + store.contact_phone"
                                class="text-sm text-gray-600 hover:text-gray-900"
                            >
                                {{ store.contact_phone }}
                            </a>
                        </li>
                        <li v-if="store.address" class="text-sm text-gray-600">
                            {{ store.address }}
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">
                        Newsletter
                    </h3>
                    <p class="mt-4 text-sm text-gray-600">
                        Subscribe to our newsletter for updates and exclusive
                        offers.
                    </p>
                    <form class="mt-4">
                        <input
                            type="email"
                            placeholder="Enter your email"
                            class="block w-full rounded-md border-0 bg-gray-50 px-3 py-2 text-gray-900 ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-primary sm:text-sm"
                        />
                        <button
                            type="submit"
                            class="mt-3 w-full rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                        >
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-16 border-t border-gray-900/10 pt-8">
                <p class="text-center text-sm leading-5 text-gray-500">
                    &copy; {{ new Date().getFullYear() }} {{ store.name }}. All
                    rights reserved.
                </p>
            </div>
        </div>
    </footer>
</template>

<style scoped>
/* keep footer spacing styles local if needed */
</style>
