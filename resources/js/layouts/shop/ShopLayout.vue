<template>
    <Head :title="store.name" />

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <header
            class="sticky top-0 z-40 border-b bg-background/95 backdrop-blur"
        >
            <nav
                class="container mx-auto flex h-16 items-center justify-between px-4"
            >
                <!-- Logo -->
                <div class="flex items-center gap-6">
                    <a
                        :href="`/store/${store.slug}`"
                        class="flex items-center space-x-2"
                    >
                        <div class="weave-icon h-8 w-8"></div>
                        <span class="font-semibold">{{ store.name }}</span>
                    </a>
                </div>

                <!-- Right Side Navigation -->
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative hidden md:flex">
                        <input
                            type="search"
                            placeholder="Search products..."
                            class="h-9 w-64 rounded-md bg-input px-3 py-2 text-sm"
                        />
                    </div>

                    <!-- Cart -->
                    <Link
                        :href="route('cart.index', { storeSlug: store.slug })"
                        class="relative inline-flex items-center justify-center rounded-md p-2 text-foreground/80 hover:text-foreground"
                    >
                        <ShoppingCart class="h-5 w-5" />
                        <span
                            v-if="cartItemsCount"
                            class="absolute -top-1 -right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-primary px-1 text-xs text-primary-foreground"
                        >
                            {{ cartItemsCount }}
                        </span>
                    </Link>

                    <CustomerAuthMenu
                        :customer="customer"
                        :cart-items-count="cartItemsCount"
                        :store="store"
                    />
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t bg-background">
            <div class="container mx-auto px-4 py-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                    <!-- Brand -->
                    <div class="space-y-4">
                        <div class="weave-icon h-10 w-10"></div>
                        <h3 class="font-semibold">EpochFlow Store</h3>
                        <p class="text-sm text-foreground/70">
                            Your one-stop shop for everything you need.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="mb-4 font-semibold">Quick Links</h4>
                        <ul class="space-y-2">
                            <li>
                                <Link
                                    href="/shop"
                                    class="text-sm text-foreground/70 hover:text-foreground"
                                >
                                    Shop
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/categories"
                                    class="text-sm text-foreground/70 hover:text-foreground"
                                >
                                    Categories
                                </Link>
                            </li>
                            <li>
                                <Link
                                    :href="
                                        route('cart.index', {
                                            storeSlug: store.slug,
                                        })
                                    "
                                    class="text-sm text-foreground/70 hover:text-foreground"
                                >
                                    Cart
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Customer Service -->
                    <div>
                        <h4 class="mb-4 font-semibold">Customer Service</h4>
                        <ul class="space-y-2">
                            <li>
                                <Link
                                    href="/contact"
                                    class="text-sm text-foreground/70 hover:text-foreground"
                                >
                                    Contact Us
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/shipping"
                                    class="text-sm text-foreground/70 hover:text-foreground"
                                >
                                    Shipping Information
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/returns"
                                    class="text-sm text-foreground/70 hover:text-foreground"
                                >
                                    Returns & Exchanges
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Newsletter -->
                    <div>
                        <h4 class="mb-4 font-semibold">Stay Updated</h4>
                        <p class="mb-4 text-sm text-foreground/70">
                            Subscribe to our newsletter for updates and
                            exclusive offers.
                        </p>
                        <form class="flex gap-2">
                            <input
                                type="email"
                                placeholder="Enter your email"
                                class="h-9 flex-1 rounded-md bg-input px-3 py-2 text-sm"
                            />
                            <button type="submit" class="btn-primary">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Bottom Footer -->
                <div class="mt-8 border-t pt-8 text-sm text-foreground/70">
                    <p>&copy; 2025 {{ store.name }} . All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';
import CustomerAuthMenu from '../../components/shop/CustomerAuthMenu.vue';

defineProps({
    customer: {
        type: Object,
        default: null,
    },
    cartItemsCount: {
        type: Number,
        default: 0,
    },
    store: {
        type: Object,
        required: true,
    },
});
</script>
