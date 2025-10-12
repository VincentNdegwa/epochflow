<!-- Default template product card component -->
<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { Product } from '../types'
import { Eye, Heart, ShoppingCart } from 'lucide-vue-next'

interface Props {
    product: Product
    storeSlug: string
}

const props = defineProps<Props>()
const hovered = ref(false)

const formattedPrice = computed(() => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(props.product.price)
})

const imageSrc = computed(() => props.product.primary_image || props.product.image_url || '/storage/placeholder.png')

function addToCart() {
    window.dispatchEvent(new CustomEvent('add-to-cart', { detail: { productId: props.product.id, quantity: 1 } }))
}
</script>

<template>
    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-lg transition p-4 flex flex-col h-full">
        <div class="relative">

            <div class="w-full overflow-hidden rounded-xl bg-gray-100">
                <div class="w-full h-56 sm:h-64 md:h-52 lg:h-56 overflow-hidden">
                    <img :src="imageSrc" :alt="product.name" class="w-full h-full object-cover object-center" />
                </div>
            </div>

            <!-- Quick action icons -->
            <div
                class="absolute right-3 top-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button aria-label="Quick view"
                    class="h-9 w-9 rounded-full bg-white/90 ring-1 ring-gray-200 flex items-center justify-center text-gray-700 hover:bg-primary hover:text-white">
                    <Eye class="h-4 w-4" />
                </button>
                <button aria-label="Add to wishlist"
                    class="h-9 w-9 rounded-full bg-white/90 ring-1 ring-gray-200 flex items-center justify-center text-gray-700 hover:text-red-500">
                    <Heart class="h-4 w-4" />
                </button>
            </div>
        </div>

        <div class="mt-4 flex-1">
            <h3 class="text-sm font-semibold text-gray-800 line-clamp-2">
                <Link :href="route('shop.products.show', { slug: storeSlug, productId: product.id })"
                    class="hover:underline">
                {{ product.name }}
                </Link>
            </h3>
            <p class="mt-2 text-xs text-gray-500 line-clamp-3">{{ product.description }}</p>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <p class="text-sm font-bold text-gray-900">{{ formattedPrice }}</p>
                <span
                    class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">
                    In stock
                </span>
            </div>

            <button @click="addToCart"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 focus:outline-none">
                <ShoppingCart class="h-4 w-4" />
                Add
            </button>
        </div>
    </div>
</template>