<script setup lang="ts">
import { computed } from 'vue';
import { route } from 'ziggy-js';
import ToastLayout from '../../../../layouts/ToastLayout.vue';
import Header from '../components/Header.vue';
import StoreFooter from '../components/StoreFooter.vue';
import bannerJpg from '../images/banner.jpg';
import bannerWebp from '../images/banner2.webp';
import type { Customer, Store } from '../types';

const props = defineProps<{
    store: Store;
    customer?: Customer | null;
    cartItemsCount?: number;
}>();

const headerStyle = computed(() => ({
    '--header-image': `url(${props.store?.banner_url || bannerWebp || bannerJpg})`,
}));

const navigation = [
    { name: 'Home', href: route('stores.show', { slug: props.store.slug }) },
    {
        name: 'Categories',
        href: route('stores.show', { slug: props.store.slug }) + '#categories',
    },
    {
        name: 'Cart',
        href: route('cart.index', { storeSlug: props.store.slug }),
    },
];
</script>

<template>
    <ToastLayout>
        <!-- Hero with header overlay -->
        <header class="relative" :style="headerStyle">
            <div
                class="absolute inset-0 bg-gradient-to-r from-gray-900/75 to-gray-900/50 backdrop-blur-sm"
            >
                <div
                    class="bg-grid-white/[0.05] absolute inset-0 bg-[size:60px_60px]"
                ></div>
            </div>

            <Header
                :store="props.store"
                :customer="props.customer"
                :cart-items-count="props.cartItemsCount || 0"
            />

            <section class="mt-6 pb-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 md:items-center"
                    >
                        <div class="z-30 text-center md:text-left">
                            <h2 class="text-xl font-semibold text-white">
                                {{ props.store?.name }}
                            </h2>
                            <p
                                v-if="props.store?.description"
                                class="mt-2 max-w-2xl text-sm text-white/80"
                            >
                                {{ props.store.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </header>

        <main class="relative">
            <slot />
        </main>

        <StoreFooter :store="props.store" :navigation="navigation" />
    </ToastLayout>
</template>
<style>
header {
    background-image: var(--header-image);
    background-size: cover;
    background-position: center;
    min-height: 10vh;
}

.bg-grid-white {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(255 255 255 / 0.05)'%3e%3cpath d='M0 .5H31.5V32'/%3e%3c/svg%3e");
}
</style>
