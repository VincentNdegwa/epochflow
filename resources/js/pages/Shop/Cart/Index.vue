<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import type {
    CartItem as CartItemType,
    Customer,
    Store,
} from '../../../types';
import ShopLayout from '../../../layouts/Shop/ShopLayout.vue';

interface Props {
    cartItems: CartItemType[];
    total: number;
    customer: Customer | null;
    store: Store;
}

import { route } from 'ziggy-js';

const props = defineProps<Props>();

function removeItem(id: number) {
    if (!confirm('Remove item from cart?')) return;
    router.delete(
        route('cart.destroy', { cartItem: id, storeSlug: props.store?.slug }),
    );
}

function updateQuantity(id: number, qty: number) {
    if (qty < 1) return;
    router.put(
        route('cart.update', { cartItem: id, storeSlug: props.store?.slug }),
        { quantity: qty },
    );
}
</script>

<template>
    <ShopLayout
        :store="store"
        :customer="customer"
        :cart-items-count="props.cartItems.length"
    >
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white p-6 shadow">
                <h1 class="text-2xl font-bold">Your Cart</h1>

                <div
                    v-if="props.cartItems.length === 0"
                    class="mt-6 text-gray-600"
                >
                    Your cart is empty.
                </div>

                <div v-else class="mt-6 space-y-4">
                    <div
                        v-for="item in props.cartItems"
                        :key="item.id"
                        class="flex items-center gap-4 border-b pb-4"
                    >
                        <img
                            :src="
                                item.product.primary_image ||
                                '/images/placeholder.png'
                            "
                            alt=""
                            class="h-20 w-20 rounded-md object-cover"
                        />
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold">
                                        {{ item.product.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ item.product.category?.name }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium">
                                        ${{
                                            (
                                                Number(item.product.price) || 0
                                            ).toFixed(2)
                                        }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Qty: {{ item.quantity }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center gap-2">
                                <button
                                    @click="
                                        updateQuantity(
                                            item.id,
                                            item.quantity - 1,
                                        )
                                    "
                                    class="rounded border px-2 py-1"
                                >
                                    -
                                </button>
                                <span class="w-8 text-center">{{
                                    item.quantity
                                }}</span>
                                <button
                                    @click="
                                        updateQuantity(
                                            item.id,
                                            item.quantity + 1,
                                        )
                                    "
                                    class="rounded border px-2 py-1"
                                >
                                    +
                                </button>

                                <button
                                    @click="removeItem(item.id)"
                                    class="ml-4 text-sm text-red-600"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-600">Subtotal</div>
                            <div class="text-2xl font-bold">
                                ${{ props.total.toFixed(2) }}
                            </div>
                        </div>

                        <div>
                            <a
                                :href="
                                    route('checkout.create', {
                                        storeSlug: props.store.slug,
                                    })
                                "
                                class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white"
                                >Proceed to checkout</a
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </ShopLayout>
</template>
