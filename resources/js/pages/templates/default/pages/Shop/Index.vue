<!-- Default template shop index page -->
<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ChevronRight, LayoutGrid } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { route } from 'ziggy-js';
import ProductCard from '../../components/ProductCard.vue';
import ShopLayout from '../../layouts/ShopLayout.vue';
import { Category, Customer, Product, Store } from '../../types';

interface Props {
    store: Store;
    products: {
        data: Product[];
        links: Array<{
            url?: string;
            label: string;
            active: boolean;
        }>;
    };
    categories: Category[];
    featuredCategories: Category[];
    newArrivals: Product[];
    bestSellers: Product[];
    filters: {
        category?: number;
        sort: string;
        search?: string | null;
    };
    cartItemsCount: number;
    customer: Customer | null;
}

const props = defineProps<Props>();

const selectedCategory = ref(props.filters.category);
const selectedSort = ref(props.filters.sort);

function selectCategory(id?: number) {
    selectedCategory.value = id;
    const el = document.getElementById('products');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

const sortOptions = [
    { value: 'latest', label: 'Latest' },
    { value: 'price_asc', label: 'Price: Low to High' },
    { value: 'price_desc', label: 'Price: High to Low' },
];

watch([selectedCategory, selectedSort], ([category, sort]) => {
    router.get(
        route('stores.show', { slug: props.store.slug }),
        { category, sort },
        { preserveState: true, preserveScroll: true },
    );
});
</script>

<template>
    <ShopLayout
        :store="store"
        :customer="customer"
        :cart-items-count="cartItemsCount"
        :initial-search="filters.search"
    >
        <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
            <!-- Categories Section -->
            <section id="categories" class="bg-white py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2
                        class="mb-8 text-3xl font-bold tracking-tight text-gray-900"
                    >
                        Shop by Category
                    </h2>
                    <div
                        class="flex w-full gap-4 overflow-scroll [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    >
                        <div
                            v-for="category in featuredCategories"
                            :key="category.id"
                            class="relative"
                        >
                            <div
                                class="group block overflow-hidden rounded-xl bg-gradient-to-br from-primary/10 to-primary/5 p-4 transition hover:from-primary/20 hover:to-primary/10"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-lg bg-white/30"
                                    >
                                        <LayoutGrid
                                            class="h-6 w-6 text-primary"
                                        />
                                    </div>
                                    <div>
                                        <h3
                                            class="text-sm font-semibold text-gray-800"
                                        >
                                            {{ category.name }}
                                        </h3>
                                        <p class="text-xs text-gray-500">
                                            {{ category.products_count || 0 }}
                                            products
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button
                                        @click="selectCategory(category.id)"
                                        class="inline-flex items-center gap-2 text-sm text-primary hover:underline"
                                    >
                                        <ChevronRight class="h-4 w-4" />
                                        Browse
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- New Arrivals Section -->
            <section id="new-arrivals" class="py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-8 flex items-center justify-between">
                        <h2
                            class="text-3xl font-bold tracking-tight text-gray-900"
                        >
                            New Arrivals
                        </h2>
                        <p class="text-sm text-gray-500">
                            Fresh items just landed — hand-picked for you
                        </p>
                    </div>
                    <div
                        class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <ProductCard
                            v-for="product in newArrivals"
                            :key="product.id"
                            :product="product"
                            :store-slug="store.slug"
                        />
                    </div>
                </div>
            </section>

            <!-- Best Sellers Section -->
            <section id="best-sellers" class="bg-white py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-8 flex items-center justify-between">
                        <h2
                            class="text-3xl font-bold tracking-tight text-gray-900"
                        >
                            Best Sellers
                        </h2>
                        <p class="text-sm text-gray-500">
                            Most loved by customers
                        </p>
                    </div>
                    <div
                        class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <ProductCard
                            v-for="product in bestSellers"
                            :key="product.id"
                            :product="product"
                            :store-slug="store.slug"
                        />
                    </div>
                </div>
            </section>

            <!-- All Products Section -->
            <section id="products" class="py-16">
                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <!-- Decorative floating image -->
                    <div
                        class="pointer-events-none absolute -top-24 right-6 hidden lg:block"
                    >
                        <div
                            class="h-64 w-64 overflow-hidden rounded-full bg-white/80 shadow-xl"
                        >
                            <img
                                :src="
                                    store.banner_url ||
                                    '/storage/placeholder.png'
                                "
                                alt="banner"
                                class="h-full w-full object-cover object-center opacity-90"
                            />
                        </div>
                    </div>
                    <div class="rounded-2xl bg-white/60 p-6 backdrop-blur-sm">
                        <div
                            class="mb-8 flex flex-col items-center justify-between md:flex-row"
                        >
                            <h2
                                class="text-3xl font-bold tracking-tight text-gray-900"
                            >
                                All Products
                            </h2>

                            <!-- Filters -->
                            <div class="flex gap-4">
                                <select
                                    v-model="selectedCategory"
                                    class="rounded-sm border px-2 py-1 text-sm ring-primary"
                                >
                                    <option selected :value="undefined">
                                        All Categories
                                    </option>
                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>

                                <select
                                    v-model="selectedSort"
                                    class="rounded-sm border px-2 py-1 text-sm ring-primary"
                                >
                                    <option
                                        v-for="option in sortOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Product Grid -->
                        <div
                            class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4"
                        >
                            <ProductCard
                                v-for="product in products.data"
                                :key="product.id"
                                :product="product"
                                :store-slug="store.slug"
                            />
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8 flex justify-center gap-2">
                            <template
                                v-for="(link, i) in products.links"
                                :key="i"
                            >
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="rounded-lg px-4 py-2 text-sm font-medium transition-colors"
                                    :class="{
                                        'bg-primary text-white shadow-sm hover:bg-primary/90':
                                            link.active,
                                        'bg-white text-gray-700 shadow-sm hover:bg-gray-50':
                                            !link.active,
                                    }"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </ShopLayout>
</template>
