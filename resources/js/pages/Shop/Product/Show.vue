<template>
    <ShopLayout :user="user" :cart-items-count="cartItemsCount">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <!-- Product Images -->
                <div class="space-y-4">
                    <div
                        class="aspect-square overflow-hidden rounded-lg bg-muted"
                    >
                        <img
                            :src="selectedImage || product.primary_image"
                            :alt="product.name"
                            class="h-full w-full object-cover object-center"
                        />
                    </div>
                    <!-- Thumbnail Images -->
                    <div class="grid grid-cols-4 gap-4">
                        <button
                            v-for="image in product.images"
                            :key="image.id"
                            @click="selectedImage = image.path"
                            class="aspect-square overflow-hidden rounded-md bg-muted"
                            :class="{
                                'ring-2 ring-primary':
                                    selectedImage === image.path,
                            }"
                        >
                            <img
                                :src="image.path"
                                :alt="product.name"
                                class="h-full w-full object-cover object-center"
                            />
                        </button>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="space-y-6">
                    <div>
                        <Link
                            :href="`/shop?category=${product.category.slug}`"
                            class="text-sm font-medium text-foreground/70 hover:text-foreground"
                        >
                            {{ product.category.name }}
                        </Link>
                        <h1 class="mt-2 text-3xl font-bold">
                            {{ product.name }}
                        </h1>
                        <p class="mt-4 text-2xl font-medium">
                            ${{ product.price }}
                        </p>
                    </div>

                    <div class="prose prose-sm text-foreground/80">
                        <p>{{ product.description }}</p>
                    </div>

                    <!-- Stock Status -->
                    <div class="flex items-center gap-2">
                        <div
                            class="h-3 w-3 rounded-full"
                            :class="
                                product.in_stock ? 'bg-green-500' : 'bg-red-500'
                            "
                        ></div>
                        <span class="text-sm">
                            {{ product.in_stock ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    </div>

                    <!-- Add to Cart Form -->
                    <form
                        v-if="product.in_stock"
                        @submit.prevent="addToCart"
                        class="space-y-4"
                    >
                        <div>
                            <label
                                for="quantity"
                                class="mb-2 block text-sm font-medium"
                            >
                                Quantity
                            </label>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="quantity > 1 && quantity--"
                                    class="flex h-9 w-9 items-center justify-center rounded-md border"
                                >
                                    <Minus class="h-4 w-4" />
                                </button>
                                <input
                                    id="quantity"
                                    v-model="quantity"
                                    type="number"
                                    min="1"
                                    :max="product.stock"
                                    class="h-9 w-20 rounded-md border bg-background px-3 text-center"
                                />
                                <button
                                    type="button"
                                    @click="
                                        quantity < product.stock && quantity++
                                    "
                                    class="flex h-9 w-9 items-center justify-center rounded-md border"
                                >
                                    <Plus class="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="btn-primary w-full"
                            :disabled="form.processing"
                        >
                            <ShoppingCart
                                v-if="!form.processing"
                                class="h-5 w-5"
                            />
                            <span
                                v-else
                                class="h-5 w-5 animate-spin rounded-full border-2 border-current border-r-transparent"
                            ></span>
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- Related Products -->
            <section class="mt-16">
                <h2 class="mb-6 text-2xl font-bold">Related Products</h2>
                <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                    <ProductCard
                        v-for="product in relatedProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>
            </section>
        </div>
    </ShopLayout>
</template>

<script setup>
import ProductCard from '@/components/shop/ProductCard.vue';
import ShopLayout from '@/layouts/shop/ShopLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Minus, Plus, ShoppingCart } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    cartItemsCount: Number,
    product: Object,
    relatedProducts: Array,
});

const selectedImage = ref(props.product.primary_image);
const quantity = ref(1);

const form = useForm({
    product_id: props.product.id,
    quantity: quantity,
});

const addToCart = () => {
    form.post('/cart', {
        preserveScroll: true,
    });
};
</script>
