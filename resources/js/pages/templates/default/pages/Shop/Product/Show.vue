<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import { Product, Store } from '../../../../../../types';
import ShopLayout from '../../../layouts/ShopLayout.vue';
import { Heart, Share2, Truck, ShieldCheck, ArrowLeft, ArrowRight } from 'lucide-vue-next';

const props = defineProps<{
    product: Product;
    relatedProducts: Product[];
    store: Store;
    customer: any;
    cartItemsCount: number;
}>();

const price = computed(() => (props.product?.price ?? 0));
const quantity = ref(1);
const selectedImage = ref(props.product.primary_image || '/images/placeholder.png');
const isZoomed = ref(false);
const zoomPosition = ref({ x: 0, y: 0 });

const allImages = computed(() => {
    const images = [...(props.product.images || [])];
    if (props.product.primary_image && !images.find(img => img.path === props.product.primary_image)) {
        images.unshift({ id: 'primary', url: props.product.primary_image });
    }
    return images;
});

function updateZoom(event: MouseEvent) {
    const image = event.currentTarget as HTMLElement;
    const { left, top, width, height } = image.getBoundingClientRect();
    const x = ((event.clientX - left) / width) * 100;
    const y = ((event.clientY - top) / height) * 100;
    zoomPosition.value = { x, y };
}

function incrementQuantity() {
    if (props.product.stock && quantity.value < props.product.stock) {
        quantity.value++;
    }
}

function decrementQuantity() {
    if (quantity.value > 1) {
        quantity.value--;
    }
}
</script>

<template>
    <ShopLayout :store="store" :customer="customer" :cart-items-count="cartItemsCount">
        <Head :title="product.name" />

        <div class="mx-auto max-w-7xl px-4 py-12">
            <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-2">
                <!-- Image Gallery -->
                <div class="lg:row-span-2">
                    <div class="relative overflow-hidden rounded-2xl bg-gray-100">
                        <!-- Main Image with Zoom -->
                        <div
                            class="aspect-square cursor-zoom-in overflow-hidden"
                            @mousemove="updateZoom"
                            @mouseenter="isZoomed = true"
                            @mouseleave="isZoomed = false"
                        >
                            <img
                                :src="selectedImage"
                                :alt="product.name"
                                class="h-full w-full object-fit transition duration-500"
                                :class="{ 'scale-110': isZoomed }"
                                :style="isZoomed ? `transform-origin: ${zoomPosition.x}% ${zoomPosition.y}%` : ''"
                            />
                        </div>

                        <!-- Thumbnail Navigation -->
                        <div class="relative mt-4">
                            <div class="flex space-x-4 px-4">
                                <button
                                    v-for="image in allImages"
                                    :key="image.id"
                                    @click="selectedImage = image.path"
                                    class="relative flex-shrink-0"
                                    :class="{ 'ring-2 ring-primary': selectedImage === image.path }"
                                >
                                    <img
                                        :src="image.path"
                                        :alt="product.name"
                                        class="h-24 w-24 rounded-lg object-cover"
                                    />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="lg:max-w-lg">
                    <div class="rounded-2xl bg-white p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                                    {{ product.name }}
                                </h1>
                                <p v-if="product.category" class="mt-1 text-sm text-gray-500">
                                    {{ product.category.name }}
                                </p>
                            </div>
                            <div class="flex space-x-4">
                                <button class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                                    <Heart class="h-6 w-6" />
                                </button>
                                <button class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                                    <Share2 class="h-6 w-6" />
                                </button>
                            </div>
                        </div>

                        <div class="mt-8">
                            <div class="flex items-center justify-between">
                                <div class="flex items-baseline">
                                    <span class="text-4xl font-bold tracking-tight text-gray-900">${{ price }}</span>
                                </div>
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium',
                                        product.stock > 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
                                    ]"
                                >
                                    {{ product.stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </div>

                            <div class="mt-8 space-y-6">
                                <!-- Add to Cart Button -->
                                <div class="flex flex-col gap-4">
                                    <Link
                                        v-if="product.stock > 0 && product.is_active"
                                        :href="route('cart.store', { storeSlug: store.slug })"
                                        method="post"
                                        as="button"
                                        class="flex w-full items-center justify-center rounded-xl bg-primary px-8 py-4 text-base font-medium text-white shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                    >
                                        Add to Cart
                                    </Link>
                                    <button
                                        v-else
                                        disabled
                                        class="w-full cursor-not-allowed rounded-xl bg-gray-100 px-8 py-4 text-base font-medium text-gray-400"
                                    >
                                        {{ !product.is_active ? 'Product Unavailable' : 'Out of Stock' }}
                                    </button>
                                </div>

                            </div>
                        </div>

                        <!-- Product Description -->
                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900">Description</h3>
                            <div class="mt-4 prose prose-sm text-gray-500" v-html="product.description"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div v-if="relatedProducts.length > 0" class="mt-16 rounded-2xl bg-gray-50 p-8">
                <h2 class="text-2xl font-bold text-gray-900">You may also like</h2>
                <div class="mt-6 grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4 xl:gap-x-8">
                    <Link
                        v-for="p in relatedProducts"
                        :key="p.id"
                        :href="route('shop.products.show', { storeSlug: store.slug, product: p.slug })"
                        class="group"
                    >
                        <div class="aspect-square w-full overflow-hidden rounded-2xl bg-gray-100">
                            <img
                                :src="p.primary_image || '/images/placeholder.png'"
                                :alt="p.name"
                                class="h-full w-full object-cover object-center transition duration-300 group-hover:scale-105"
                            />
                        </div>
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-900">{{ p.name }}</h3>
                            <p class="mt-1 text-sm font-medium text-gray-900">${{ p.price }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<style scoped>
.bg-primary {
    background-color: var(--color-primary);
}
.text-primary {
    color: var(--color-primary);
}
.ring-primary {
    --tw-ring-color: var(--color-primary);
}
.hover\:bg-primary:hover {
    background-color: var(--color-primary);
}
</style>
