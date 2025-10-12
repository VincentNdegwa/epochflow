<template>
  <div class="min-h-screen bg-background">
    <!-- Header -->
    <header class="sticky top-0 z-40 border-b bg-background/95 backdrop-blur">
      <nav class="container mx-auto flex h-16 items-center justify-between px-4">
        <!-- Logo -->
        <div class="flex items-center gap-6">
          <a href="/" class="flex items-center space-x-2">
            <div class="weave-icon w-8 h-8"></div>
            <span class="font-semibold">EpochFlow Store</span>
          </a>
          <!-- Main Navigation -->
          <div class="hidden md:flex items-center space-x-6">
            <Link href="/shop" class="text-foreground/80 hover:text-foreground">
              Shop
            </Link>
            <Link href="/categories" class="text-foreground/80 hover:text-foreground">
              Categories
            </Link>
          </div>
        </div>

        <!-- Right Side Navigation -->
        <div class="flex items-center space-x-4">
          <!-- Search -->
          <div class="hidden md:flex relative">
            <input
              type="search"
              placeholder="Search products..."
              class="w-64 h-9 px-3 py-2 text-sm rounded-md bg-input"
            />
          </div>

          <!-- Cart -->
          <Link
            href="/cart"
            class="relative inline-flex items-center justify-center rounded-md p-2 text-foreground/80 hover:text-foreground"
          >
            <ShoppingCart class="h-5 w-5" />
            <span
              v-if="cartItemsCount"
              class="absolute -top-1 -right-1 h-4 min-w-4 rounded-full bg-primary text-xs text-primary-foreground flex items-center justify-center px-1"
            >
              {{ cartItemsCount }}
            </span>
          </Link>

          <!-- User Menu -->
          <UserMenu v-if="user" :user="user" />
          <template v-else>
            <Link
              href="/login"
              class="text-foreground/80 hover:text-foreground"
            >
              Login
            </Link>
            <Link
              href="/register"
              class="btn-primary"
            >
              Sign up
            </Link>
          </template>
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Brand -->
          <div class="space-y-4">
            <div class="weave-icon w-10 h-10"></div>
            <h3 class="font-semibold">EpochFlow Store</h3>
            <p class="text-sm text-foreground/70">
              Your one-stop shop for everything you need.
            </p>
          </div>

          <!-- Quick Links -->
          <div>
            <h4 class="font-semibold mb-4">Quick Links</h4>
            <ul class="space-y-2">
              <li>
                <Link href="/shop" class="text-sm text-foreground/70 hover:text-foreground">
                  Shop
                </Link>
              </li>
              <li>
                <Link href="/categories" class="text-sm text-foreground/70 hover:text-foreground">
                  Categories
                </Link>
              </li>
              <li>
                <Link href="/cart" class="text-sm text-foreground/70 hover:text-foreground">
                  Cart
                </Link>
              </li>
            </ul>
          </div>

          <!-- Customer Service -->
          <div>
            <h4 class="font-semibold mb-4">Customer Service</h4>
            <ul class="space-y-2">
              <li>
                <Link href="/contact" class="text-sm text-foreground/70 hover:text-foreground">
                  Contact Us
                </Link>
              </li>
              <li>
                <Link href="/shipping" class="text-sm text-foreground/70 hover:text-foreground">
                  Shipping Information
                </Link>
              </li>
              <li>
                <Link href="/returns" class="text-sm text-foreground/70 hover:text-foreground">
                  Returns & Exchanges
                </Link>
              </li>
            </ul>
          </div>

          <!-- Newsletter -->
          <div>
            <h4 class="font-semibold mb-4">Stay Updated</h4>
            <p class="text-sm text-foreground/70 mb-4">
              Subscribe to our newsletter for updates and exclusive offers.
            </p>
            <form class="flex gap-2">
              <input
                type="email"
                placeholder="Enter your email"
                class="flex-1 h-9 px-3 py-2 text-sm rounded-md bg-input"
              />
              <button type="submit" class="btn-primary">
                Subscribe
              </button>
            </form>
          </div>
        </div>

        <!-- Bottom Footer -->
        <div class="mt-8 pt-8 border-t text-sm text-foreground/70">
          <p>&copy; 2025 EpochFlow Store. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ShoppingCart } from 'lucide-vue-next'
import UserMenu from '@/components/UserMenu.vue'

defineProps({
  user: {
    type: Object,
    default: null
  },
  cartItemsCount: {
    type: Number,
    default: 0
  }
})
</script>