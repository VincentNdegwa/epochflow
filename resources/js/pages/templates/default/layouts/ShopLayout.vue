<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import type { Customer, Store } from '../types';

import { ShoppingCart } from 'lucide-vue-next';
import ToastLayout from '../../../../layouts/ToastLayout.vue';
import Header from '../components/Header.vue';
import StoreFooter from '../components/StoreFooter.vue';
import bannerJpg from '../images/banner.jpg';
import bannerWebp from '../images/banner2.webp';

interface Props {
    store: Store;
    cartItemsCount: number;
    customer?: Customer | null;
    initialSearch?: string | null;
}

const props = defineProps<Props>();

// Header (nav / mobile / search) is handled by the `Header` component.

const headerStyle = computed(() => ({
    '--header-image': `url(${props.store.banner_url || bannerWebp || bannerJpg})`,
}));

const navigation = [
    { name: 'Home', href: route('stores.show', { slug: props.store.slug }) },
    {
        name: 'Categories',
        href: route('stores.show', { slug: props.store.slug }) + '#categories',
    },
    {
        name: 'New Arrivals',
        href:
            route('stores.show', { slug: props.store.slug }) + '#new-arrivals',
    },
    {
        name: 'Best Sellers',
        href:
            route('stores.show', { slug: props.store.slug }) + '#best-sellers',
    },
];

const shortName = computed(() => {
    const n = props.store?.name ?? '';
    return n.split(' ')[0] || n;
});

const initials = computed(() => {
    const parts = (props.store?.name ?? '').split(' ').filter(Boolean);
    return (
        parts
            .map((p) => p[0])
            .slice(0, 2)
            .join('') || (props.store?.name ?? '').slice(0, 2)
    ).toUpperCase();
});

const rating = computed(() => (props.store as any)?.rating ?? '4.8');
const deliveryEstimate = computed(
    () =>
        ((props.store as any)?.delivery_estimate ??
            (props.store as any)?.delivery) ||
        '2-4 days',
);
const locationText = computed(() => (props.store as any)?.location ?? '');
</script>

<template>
    <ToastLayout>
        <Head :title="store.name" />

        <div class="min-h-screen">
            <!-- Hero (store banner & intro) -->
            <header class="relative" :style="headerStyle">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-gray-900/75 to-gray-900/50 backdrop-blur-sm"
                >
                    <div
                        class="bg-grid-white/[0.05] absolute inset-0 bg-[size:60px_60px]"
                    ></div>
                </div>

                <!-- Top navigation (componentized) -->
                <Header
                    :store="store"
                    :customer="customer"
                    :cart-items-count="cartItemsCount"
                />

                <section id="store-hero" class="mt-12 pb-12">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div
                            class="grid grid-cols-1 gap-8 md:grid-cols-2 md:items-center"
                        >
                            <div class="z-30 text-center md:text-left">
                                <p
                                    class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-white/90"
                                >
                                    <span class="mr-2 font-semibold">{{
                                        shortName
                                    }}</span>
                                    <span class="text-white/70"
                                        >— curated store</span
                                    >
                                </p>

                                <h1
                                    class="mt-4 text-3xl leading-tight font-extrabold tracking-tight text-white sm:text-4xl md:text-5xl"
                                >
                                    {{ store.name }}
                                </h1>

                                <p
                                    v-if="store.description"
                                    class="mx-auto mt-4 max-w-2xl text-lg text-white/80 md:mx-0"
                                >
                                    {{ store.description }}
                                </p>

                                <div
                                    class="mt-6 flex flex-col items-center gap-3 sm:flex-row md:items-start"
                                >
                                    <a
                                        href="#products"
                                        class="inline-flex items-center justify-center rounded-md bg-primary px-5 py-3 text-sm font-semibold text-white shadow hover:bg-primary/90"
                                        >Shop collection</a
                                    >
                                    <a
                                        v-if="
                                            store.contact_phone ||
                                            store.contact_email
                                        "
                                        href="#contact"
                                        class="inline-flex items-center justify-center rounded-md border border-white/10 bg-white/5 px-5 py-3 text-sm font-semibold text-white/90 hover:bg-white/10"
                                        >Contact store</a
                                    >
                                </div>

                                <!-- Trust row -->
                                <div
                                    class="mt-6 flex flex-wrap items-center justify-center gap-3 md:justify-start"
                                >
                                    <div
                                        class="inline-flex items-center gap-2 rounded-md bg-white/5 px-3 py-2 text-sm text-white/90"
                                    >
                                        <span class="font-semibold text-accent"
                                            >⭐</span
                                        >
                                        <span>{{ rating ?? '4.8' }}</span>
                                        <span class="text-white/60"
                                            >(based on reviews)</span
                                        >
                                    </div>

                                    <div
                                        class="inline-flex items-center gap-2 rounded-md bg-white/5 px-3 py-2 text-sm text-white/90"
                                    >
                                        <svg
                                            class="h-4 w-4 text-white/90"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 7h18M3 11h18M3 15h18"
                                            />
                                        </svg>
                                        <span>{{ deliveryEstimate }}</span>
                                    </div>

                                    <div
                                        v-if="locationText"
                                        class="inline-flex items-center gap-2 rounded-md bg-white/5 px-3 py-2 text-sm text-white/90"
                                    >
                                        <svg
                                            class="h-4 w-4 text-white/90"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 11a3 3 0 100-6 3 3 0 000 6zM12 21s8-4.5 8-10a8 8 0 10-16 0c0 5.5 8 10 8 10z"
                                            />
                                        </svg>
                                        <span>{{ locationText }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right column: hero image / floating product -->
                            <div class="flex items-center justify-center">
                                <div class="relative w-full max-w-md">
                                    <div
                                        class="aspect-[16/10] w-full overflow-hidden rounded-2xl bg-white/5 shadow-lg"
                                    >
                                        <img
                                            :src="
                                                store.banner_url ||
                                                store.logo_url ||
                                                bannerJpg ||
                                                '/images/placeholder.png'
                                            "
                                            alt="Store banner"
                                            class="h-full w-full object-cover"
                                        />
                                    </div>

                                    <!-- Floating badge (cart count) -->
                                    <div
                                        class="absolute right-4 -bottom-3 flex items-center gap-2 rounded-full bg-accent px-3 py-1 text-sm font-medium text-white shadow"
                                    >
                                        <ShoppingCart />
                                        <span
                                            >{{ cartItemsCount ?? 0 }} in
                                            cart</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </header>

            <main class="relative">
                <!-- Page content -->
                <slot />
            </main>

            <StoreFooter :store="store" :navigation="navigation" />
        </div>
    </ToastLayout>
</template>

<style>
header {
    background-image: var(--header-image);
    background-size: cover;
    background-position: center;
    min-height: 40vh;
}

.bg-grid-white {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(255 255 255 / 0.05)'%3e%3cpath d='M0 .5H31.5V32'/%3e%3c/svg%3e");
}
</style>
