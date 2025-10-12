<!-- Default template shop layout -->
<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { Customer, Store } from '../types'
import CustomerAuthMenu from '../components/CustomerAuthMenu.vue'

interface Props {
    store: Store;
    cartItemsCount: number;
    customer?: Customer | null;
    initialSearch?: string | null;
}

const props = defineProps<Props>()

const mobileMenuOpen = ref(false)
const showSearch = ref(false)
const searchQuery = ref(props.initialSearch ?? '')
let searchTimeout: ReturnType<typeof setTimeout> | null = null

function performSearch(q?: string) {
    if (searchTimeout) clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        showSearch.value = false
        router.get(
            route('stores.show', { slug: props.store.slug }),
            { search: q ?? searchQuery.value },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    const el = document.getElementById('products')
                    if (el) el.scrollIntoView({ behavior: 'smooth' })
                },
            }
        )
    }, 300)
}

function submitSearch() {
    if (searchTimeout) clearTimeout(searchTimeout)
    showSearch.value = false
    mobileMenuOpen.value = false
    router.get(
        route('stores.show', { slug: props.store.slug }),
        { search: searchQuery.value },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                const el = document.getElementById('products')
                if (el) el.scrollIntoView({ behavior: 'smooth' })
            },
        }
    )
}

const headerStyle = computed(() => ({
    '--header-image': `url(${props.store.banner_url || 'https://media.istockphoto.com/id/1542614865/photo/man-shopping-vegetables-in-groceries-store.jpg?s=612x612&w=0&k=20&c=e4cvmZDGoWJjzfzzAX4CMwU-xmxjUxJ1cRa5GUU3yFo='})`,
}))

const navigation = [
    { name: 'Home', href: route('stores.show', { slug: props.store.slug }) },
    { name: 'Categories', href: '#categories' },
    { name: 'New Arrivals', href: '#new-arrivals' },
    { name: 'Best Sellers', href: '#best-sellers' },
]
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <header class="relative" :style="headerStyle">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900/75 to-gray-900/50 backdrop-blur-sm">
                <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:60px_60px]"></div>
            </div>

            <nav class="relative mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between gap-x-8 border-b border-white/10 pb-4">
                    <!-- Logo -->
                    <div class="flex lg:flex-1">
                        <Link :href="route('stores.show', { slug: store.slug })"
                            class="text-2xl font-bold tracking-tight text-white hover:text-white/80">
                        {{ store.name }}
                        </Link>
                    </div>

                    <!-- Desktop navigation -->
                    <div class="hidden lg:flex lg:gap-x-8">
                        <a v-for="item in navigation" :key="item.name" :href="item.href"
                            class="text-sm font-medium text-white/80 transition hover:text-white">
                            {{ item.name }}
                        </a>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Search -->
                        <button @click="showSearch = true"
                            class="group p-2 text-white/80 transition hidden sm:block hover:text-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <!-- Cart -->
                        <Link :href="route('cart.index')"
                            class="group hidden sm:block relative p-2 text-white/80 transition hover:text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span v-if="cartItemsCount > 0"
                            class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-accent text-xs font-medium text-white">
                            {{ cartItemsCount }}
                        </span>
                        </Link>

                        <CustomerAuthMenu class="hidden sm:block" :customer="customer ?? null"
                            :cart-items-count="cartItemsCount" :store="store" />

                        <!-- Mobile menu button -->
                        <button type="button"
                            class="inline-flex items-center justify-center rounded-md p-2 text-white/80 transition hover:text-white lg:hidden"
                            @click="mobileMenuOpen = true">
                            <span class="sr-only">Open menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Store info -->
                <div class="mt-16 pb-16 text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">
                        {{ store.name }}
                    </h1>
                    <p v-if="store.description" class="mt-4 max-w-2xl mx-auto text-xl text-white/80">
                        {{ store.description }}
                    </p>
                    <div class="mt-8 flex justify-center gap-x-6">
                        <a href="#products"
                            class="rounded-md bg-white/10 px-6 py-3 text-sm font-semibold text-white shadow-sm backdrop-blur-sm transition hover:bg-white/20">
                            Shop Now
                        </a>
                        <a v-if="store.contact_phone || store.contact_email" href="#contact"
                            class="rounded-md border border-white/10 px-6 py-3 text-sm font-semibold text-white shadow-sm backdrop-blur-sm transition hover:bg-white/10">
                            Contact Us
                        </a>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Mobile menu -->
        <div v-if="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden" @click="mobileMenuOpen = false">
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm"></div>
            <div class="fixed inset-y-0 right-0 w-full max-w-xs bg-white p-6 sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <Link :href="route('stores.show', { slug: store.slug })" class="-m-1.5 p-1.5 text-lg font-bold">
                    {{ store.name }}
                    </Link>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" @click="mobileMenuOpen = false">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-4 py-4 px-2">


                            <!-- Navigation links -->
                            <div class="mt-3">
                                <a v-for="item in navigation" :key="item.name" :href="item.href"
                                    class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50"
                                    @click="mobileMenuOpen = false">
                                    {{ item.name }}
                                </a>
                            </div>

                        </div>

                        <div class="py-4 px-2">
                            <Link :href="route('cart.index')"
                                class="-mx-3 flex items-center rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">
                            <svg class="mr-3 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Cart
                            <span v-if="cartItemsCount > 0"
                                class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-accent text-xs font-medium text-white">
                                {{ cartItemsCount }}
                            </span>
                            </Link>
                        </div>

                        <div class="py-2 px-0">
                            <!-- Customer auth/menu for mobile -->
                            <CustomerAuthMenu :customer="customer ?? null" :cart-items-count="cartItemsCount"
                                :store="store" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search overlay -->


        <div v-if="showSearch" class="fixed inset-0 z-50">
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm" @click="showSearch = false"></div>
            <div class="fixed inset-x-0 top-0 bg-white p-6 sm:ring-1 sm:ring-gray-900/10">
                <div class="mx-auto max-w-3xl" @click.stop>
                    <form @submit.prevent="submitSearch" class="flex items-center justify-between">
                        <div class="flex-1">
                            <label for="search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input id="search" type="search" name="search" v-model="searchQuery"
                                    @input="() => performSearch()"
                                    class="block w-full rounded-md border-0 bg-gray-50 py-3 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-primary sm:text-sm sm:leading-6"
                                    placeholder="Search products...">
                            </div>
                        </div>
                        <button type="button" class="ml-3 -m-2.5 rounded-md p-2.5 text-gray-700"
                            @click="showSearch = false">
                            <span class="sr-only">Close search</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <main class="relative">
            <!-- Page content -->
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200">
            <div class="mx-auto max-w-7xl px-6 py-16 sm:py-24 lg:px-8">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Store info -->
                    <div class="space-y-4">
                        <Link :href="route('stores.show', { slug: store.slug })" class="text-xl font-bold">
                        {{ store.name }}
                        </Link>
                        <p v-if="store.description" class="text-sm text-gray-600">
                            {{ store.description }}
                        </p>
                    </div>

                    <!-- Navigation -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Navigation</h3>
                        <ul class="mt-4 space-y-3">
                            <li v-for="item in navigation" :key="item.name">
                                <a :href="item.href" class="text-sm text-gray-600 hover:text-gray-900">
                                    {{ item.name }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div v-if="store.contact_email || store.contact_phone || store.address">
                        <h3 class="text-sm font-semibold text-gray-900">Contact</h3>
                        <ul class="mt-4 space-y-3">
                            <li v-if="store.contact_email">
                                <a :href="'mailto:' + store.contact_email"
                                    class="text-sm text-gray-600 hover:text-gray-900">
                                    {{ store.contact_email }}
                                </a>
                            </li>
                            <li v-if="store.contact_phone">
                                <a :href="'tel:' + store.contact_phone"
                                    class="text-sm text-gray-600 hover:text-gray-900">
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
                        <h3 class="text-sm font-semibold text-gray-900">Newsletter</h3>
                        <p class="mt-4 text-sm text-gray-600">
                            Subscribe to our newsletter for updates and exclusive offers.
                        </p>
                        <form class="mt-4">
                            <input type="email" placeholder="Enter your email"
                                class="block w-full rounded-md border-0 bg-gray-50 py-2 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-primary sm:text-sm">
                            <button type="submit"
                                class="mt-3 w-full rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-16 border-t border-gray-900/10 pt-8">
                    <p class="text-center text-sm leading-5 text-gray-500">
                        &copy; {{ new Date().getFullYear() }} {{ store.name }}. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
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