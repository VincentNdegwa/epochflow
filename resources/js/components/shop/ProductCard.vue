<template>
  <div class="group relative">
    <!-- Product Image -->
    <div class="aspect-square w-full overflow-hidden rounded-lg bg-muted">
      <img
        :src="product.primary_image"
        :alt="product.name"
        class="h-full w-full object-cover object-center transition-transform duration-300 group-hover:scale-105"
      />
      <div
        v-if="!product.in_stock"
        class="absolute inset-0 flex items-center justify-center bg-background/80 backdrop-blur-sm"
      >
        <span class="text-sm font-medium">Out of Stock</span>
      </div>
    </div>

    <!-- Product Info -->
    <div class="mt-4 flex justify-between">
      <div>
        <h3 class="text-sm font-medium">
          <Link :href="`/shop/products/${product.slug}`">
            {{ product.name }}
          </Link>
        </h3>
        <p class="mt-1 text-sm text-foreground/70">{{ product.category.name }}</p>
      </div>
      <p class="text-sm font-medium">${{ product.price }}</p>
    </div>

    <!-- Add to Cart -->
    <button
      v-if="product.in_stock"
      @click="addToCart"
      class="absolute right-4 top-4 rounded-full bg-background/90 p-2 text-foreground/80 opacity-0 backdrop-blur-sm transition-opacity group-hover:opacity-100 hover:text-foreground"
      :disabled="loading"
    >
      <ShoppingCart
        :class="loading ? 'animate-spin' : ''"
        class="h-5 w-5"
      />
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ShoppingCart } from 'lucide-vue-next'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const loading = ref(false)
const form = useForm({
  product_id: props.product.id,
  quantity: 1
})

const addToCart = () => {
  loading.value = true
  form.post('/cart', {
    preserveScroll: true,
    onSuccess: () => {
      loading.value = false
    },
    onError: () => {
      loading.value = false
    }
  })
}
</script>