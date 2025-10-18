<!-- Default template product card component -->
<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { Eye, Heart, ShoppingCart } from 'lucide-vue-next';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import { Product } from '../types';

interface Props {
    product: Product;
    storeSlug: string;
}

const props = defineProps<Props>();

const formattedPrice = computed(() => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(props.product.price);
});

const imageSrc = computed(
    () =>
        props.product.primary_image ||
        props.product.image_url ||
        '/images/placeholder.png',
);

const form = useForm({
    product_id: props.product.id,
    quantity: 1,
});

function addToCart() {
    form.post(route('cart.store', { storeSlug: props.storeSlug }), {
        preserveScroll: true,
        onStart: () => {
            // optional: could set a loading flag here
        },
        onSuccess: () => {
            // optional: show UI feedback, Inertia flash will carry server message
        },
        onError: () => {
            // optional: handle validation errors
        },
    });
}
</script>

<template>
    <div
        class="group relative flex h-full flex-col rounded-2xl bg-white p-4 shadow-sm transition hover:shadow-lg"
    >
        <div class="relative">
            <div class="w-full overflow-hidden rounded-xl bg-gray-100">
                <div
                    class="h-56 w-full overflow-hidden sm:h-64 md:h-52 lg:h-56"
                >
                    <img
                        :src="imageSrc"
                        :alt="product.name"
                        class="h-full w-full object-cover object-center"
                    />
                </div>
            </div>

            <!-- Quick action icons -->
            <div
                class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 transition-opacity group-hover:opacity-100"
            >
                <button
                    aria-label="Quick view"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-gray-700 ring-1 ring-gray-200 hover:bg-primary hover:text-white"
                >
                    <Eye class="h-4 w-4" />
                </button>
                <button
                    aria-label="Add to wishlist"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-gray-700 ring-1 ring-gray-200 hover:text-red-500"
                >
                    <Heart class="h-4 w-4" />
                </button>
            </div>
        </div>

        <div class="mt-4 flex-1">
            <h3 class="line-clamp-2 text-sm font-semibold text-gray-800">
                <Link
                    :href="
                        route('store.products.show', {
                            storeSlug: storeSlug,
                            slug: product.slug,
                        })
                    "
                    class="hover:underline"
                >
                    {{ product.name }}
                </Link>
            </h3>
            <p class="mt-2 line-clamp-3 text-xs text-gray-500">
                {{ product.description }}
            </p>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <p class="text-sm font-bold text-gray-900">
                    {{ formattedPrice }}
                </p>
                <span
                    class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700"
                >
                    In stock
                </span>
            </div>

            <button
                @click="addToCart"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 focus:outline-none"
            >
                <ShoppingCart class="h-4 w-4" />
                Add
            </button>
        </div>
    </div>
</template>
