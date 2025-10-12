<template>
  <ShopLayout :user="user" :cart-items-count="cartItemsCount">
    <!-- Hero Section -->
    <section class="mb-12">
      <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-r from-primary to-primary-2 opacity-10"></div>
        <div class="relative container mx-auto px-4 py-16">
          <div class="max-w-2xl">
            <h1 class="text-4xl font-bold mb-4">Welcome to EpochFlow Store</h1>
            <p class="text-lg text-foreground/80 mb-8">
              Discover our curated collection of products
            </p>
            <Link href="#featured" class="btn-primary">
              Shop Now
            </Link>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section class="mb-12">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Categories</h2>
        <Link href="/categories" class="text-sm text-foreground/70 hover:text-foreground">
          View All
        </Link>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <Link
          v-for="category in categories"
          :key="category.id"
          :href="`/shop?category=${category.slug}`"
          class="group relative aspect-square overflow-hidden rounded-lg bg-muted"
        >
          <div class="absolute inset-0 bg-gradient-to-t from-background/80 to-transparent"></div>
          <div class="absolute inset-0 flex items-end p-4">
            <h3 class="text-lg font-medium group-hover:text-primary transition-colors">
              {{ category.name }}
            </h3>
          </div>
        </Link>
      </div>
    </section>

    <!-- Featured Products -->
    <section id="featured" class="mb-12">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Featured Products</h2>
        <div class="flex items-center gap-2">
          <!-- Filters -->
          <select
            v-model="filters.sort"
            class="h-9 rounded-md border bg-background px-3 text-sm"
          >
            <option value="latest">Latest</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
          </select>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in products.data"
          :key="product.id"
          :product="product"
        />
      </div>

      <!-- Pagination -->
      <div v-if="products.data.last_page > 1" class="mt-8">
        <Pagination :links="products.data.links" />
      </div>
    </section>
  </ShopLayout>
</template>

<script setup>
import { watch } from 'vue'
import { router } from '@inertiajs/vue3'
import ShopLayout from '@/layouts/shop/ShopLayout.vue'
import ProductCard from '@/components/shop/ProductCard.vue'
import Pagination from '@/components/Pagination.vue'

const props = defineProps({
  user: Object,
  cartItemsCount: Number,
  categories: Array,
  products: Object,
  filters: {
    type: Object,
    default: () => ({
      category: null,
      sort: 'latest'
    })
  }
})

watch(() => props.filters, (newFilters) => {
  router.get('/shop', newFilters, {
    preserveState: true,
    preserveScroll: true
  })
}, { deep: true })
</script>