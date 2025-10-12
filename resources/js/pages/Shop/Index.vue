<template>

  <ShopLayout :customer="customer" :store="store" :cart-items-count="cartItemsCount">
    <!-- Hero Section -->
    <section class="mb-12">
      <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-r from-primary to-primary-2 opacity-10"></div>
        <div class="relative container mx-auto px-4 py-16">
          <div class="max-w-2xl">
            <h1 class="text-4xl font-bold mb-4">{{ store.name }}</h1>
            <p class="text-lg text-foreground/80 mb-8">
              {{ store.description }}
            </p>
            <div class="flex items-center gap-4">
              <Link href="#featured" class="btn-primary">
              Shop Now
              </Link>
              <div class="text-sm text-foreground/70">
                <div v-if="store.contact_email">
                  Email: {{ store.contact_email }}
                </div>
                <div v-if="store.contact_phone">
                  Phone: {{ store.contact_phone }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section class="mb-12">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Categories</h2>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <Link v-for="category in categories" :key="category.id"
          :href="route('stores.show', { slug: store.slug }) + `?category=${category.id}`"
          class="group block overflow-hidden rounded-lg bg-muted">
        <div class="relative aspect-square">
          <div class="absolute inset-0 bg-gradient-to-t from-background/80 to-transparent"></div>
          <div class="absolute inset-0 flex items-end p-4">
            <h3 class="text-lg font-medium group-hover:text-primary transition-colors">
              {{ category.name }}
            </h3>
          </div>
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
          <select v-model="filters.sort" class="h-9 rounded-md border bg-background px-3 text-sm">
            <option value="latest">Latest</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
          </select>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
      </div>

      <!-- Pagination -->
      <div v-if="products.last_page > 1" class="mt-8">
        <Pagination :links="products.links" />
      </div>
    </section>
  </ShopLayout>
</template>

<script setup lang="ts">
import { watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import ShopLayout from '@/layouts/shop/ShopLayout.vue'
import ProductCard from '@/components/shop/ProductCard.vue'
import Pagination from '@/components/Pagination.vue'

interface Store {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  contact_email: string | null;
  contact_phone: string | null;
  address: string | null;
  is_active: boolean;
}

interface Product {
  id: number;
  name: string;
  slug: string;
  description: string;
  price: number;
  stock: number;
  is_active: boolean;
  primary_image: string;
  category: {
    id: number;
    name: string;
    slug: string;
  };
}

interface Category {
  id: number;
  name: string;
  slug: string;
}

interface Props {
  store: Store;
  customer: any;
  cartItemsCount: number;
  categories: Category[];
  products: {
    data: Product[];
    links: any[];
    last_page: number;
  };
  filters: {
    category: string | null;
    sort: 'latest' | 'price_asc' | 'price_desc';
  };
}

const props = withDefaults(defineProps<Props>(), {
  filters: () => ({
    category: null,
    sort: 'latest'
  })
})

watch(() => props.filters, (newFilters) => {
  router.get('/shop', newFilters, {
    preserveState: true,
    preserveScroll: true
  })
}, { deep: true })
</script>