<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import type { Customer, Store } from '../types';
import CustomerAuthMenu from './shop/CustomerAuthMenu.vue';

interface Props {
    store: Store;
    cartItemsCount: number;
    customer?: Customer | null;
    initialSearch?: string | null;
}

const props = defineProps<Props>();

const mobileMenuOpen = ref(false);


const navigation = [
    { name: 'Back To Store', href: route('stores.show', { slug: props.store.slug }) },

];
</script>

<template>
    <nav
        class="relative mx-auto max-w-7xl bg-transparent px-4 py-4 sm:px-6 lg:px-8"
    >
        <div
            class="flex items-center justify-between gap-x-8 border-b border-white/10 pb-4"
        >
            <div class="flex lg:flex-1">
                <Link
                    :href="route('stores.show', { slug: store.slug })"
                    class="text-2xl font-bold tracking-tight text-white hover:text-white/80"
                >
                    {{ store.name }}
                </Link>
            </div>

            <div class="hidden lg:flex lg:gap-x-8">
                <a
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    class="text-sm font-medium text-white/80 transition hover:text-white"
                >
                    {{ item.name }}
                </a>
            </div>

            <div class="flex items-center gap-x-4 lg:gap-x-6">


                <Link
                    :href="route('cart.index', { storeSlug: store.slug })"
                    class="group relative hidden p-2 text-white/80 transition hover:text-white sm:block"
                >
                    <ShoppingCart />
                    <span
                        v-if="cartItemsCount > 0"
                        class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-accent text-xs font-medium text-white"
                        >{{ cartItemsCount }}</span
                    >
                </Link>

                <CustomerAuthMenu
                    class="hidden sm:block"
                    :customer="customer ?? null"
                    :cart-items-count="cartItemsCount"
                    :store="store"
                />

                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-white/80 transition hover:text-white lg:hidden"
                    @click="mobileMenuOpen = true"
                >
                    <span class="sr-only">Open menu</span>
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div
            v-if="mobileMenuOpen"
            class="fixed inset-0 z-50 lg:hidden"
            @click="mobileMenuOpen = false"
        >
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm"></div>
            <div
                class="fixed inset-y-0 right-0 w-full max-w-xs bg-white p-6 sm:ring-1 sm:ring-gray-900/10"
            >
                <div class="flex items-center justify-between">
                    <Link
                        :href="route('stores.show', { slug: store.slug })"
                        class="-m-1.5 p-1.5 text-lg font-bold"
                        >{{ store.name }}</Link
                    >
                    <button
                        type="button"
                        class="-m-2.5 rounded-md p-2.5 text-gray-700"
                        @click="mobileMenuOpen = false"
                    >
                        <span class="sr-only">Close menu</span
                        ><svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-4 px-2 py-4">
                            <div class="mt-3">
                                <a
                                    v-for="item in navigation"
                                    :key="item.name"
                                    :href="item.href"
                                    class="-mx-3 block rounded-lg px-3 py-2 text-base leading-7 font-semibold text-gray-900 hover:bg-gray-50"
                                    @click="mobileMenuOpen = false"
                                    >{{ item.name }}</a
                                >
                            </div>
                        </div>

                        <div class="px-2 py-4">
                            <Link
                                :href="
                                    route('cart.index', {
                                        storeSlug: store.slug,
                                    })
                                "
                                class="-mx-3 flex items-center rounded-lg px-3 py-2.5 text-base leading-7 font-semibold text-gray-900 hover:bg-gray-50"
                            >
                                <ShoppingCart class="mr-5" />
                                Cart
                                <span
                                    v-if="cartItemsCount > 0"
                                    class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-accent text-xs font-medium text-white"
                                    >{{ cartItemsCount }}</span
                                >
                            </Link>
                        </div>

                        <div class="px-0 py-2">
                            <CustomerAuthMenu
                                :customer="customer ?? null"
                                :cart-items-count="cartItemsCount"
                                :store="store"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </nav>
</template>

<style scoped></style>
