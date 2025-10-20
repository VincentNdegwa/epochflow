<script setup lang="ts">
import { route } from 'ziggy-js';
import ShopLayout from '../../../layouts/Shop/ShopLayout.vue';
const props = defineProps<{ order: any; store: any; customer: any; cartItems: any[] }>();
</script>

<template>
    <ShopLayout
        :store="props.store"
        :customer="props.customer"
        :cart-items-count="(props.cartItems || []).length"
    >
        <div class="mx-auto max-w-7xl px-4 py-12">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        Order {{ props.order.order_number }}
                    </h1>
                    <div class="text-sm text-gray-500">
                        Placed
                        {{ new Date(props.order.created_at).toLocaleString() }}
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                            :class="{
                                'bg-yellow-100 text-yellow-800':
                                    props.order.status === 'pending',
                                'bg-green-100 text-green-800':
                                    props.order.status === 'paid',
                                'bg-red-100 text-red-800':
                                    props.order.status === 'cancelled' ||
                                    props.order.status === 'canceled',
                            }"
                        >
                            {{ props.order.status }}
                        </span>
                    </div>
                    <a
                        :href="
                            route('customer.orders.index', {
                                storeSlug: props.store.slug,
                            })
                        "
                        class="text-sm text-gray-600 hover:underline"
                    >
                        ← Back to orders
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Items -->
                <div class="rounded-lg bg-white p-6 lg:col-span-2">
                    <h2 class="mb-4 text-lg font-semibold">Items</h2>
                    <div class="space-y-4">
                        <div
                            v-for="item in props.order.items || []"
                            :key="item.id"
                            class="flex items-center justify-between gap-4 border-b pb-4"
                        >
                            <div class="flex items-center gap-4">
                                <img
                                    v-if="item.product?.primary_image"
                                    :src="item.product.primary_image"
                                    alt=""
                                    class="h-16 w-16 rounded object-cover"
                                />
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-medium">
                                        {{ item.product?.name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        SKU: {{ item.product?.id }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm">
                                    ${{ Number(item.price).toFixed(2) }} each
                                </div>
                                <div class="text-sm text-gray-500">
                                    Qty: {{ item.quantity }}
                                </div>
                                <div class="mt-2 font-semibold">
                                    ${{
                                        (
                                            Number(item.price) * item.quantity
                                        ).toFixed(2)
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary / Addresses -->
                <aside class="rounded-lg bg-white p-6">
                    <h3 class="text-lg font-semibold">Order Summary</h3>
                    <div class="mt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <div>Items total</div>
                            <div>
                                ${{
                                    Number(
                                        props.order.items?.reduce(
                                            (s: any, i: any) =>
                                                s +
                                                Number(i.price) * i.quantity,
                                            0,
                                        ) || 0,
                                    ).toFixed(2)
                                }}
                            </div>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <div>Shipping</div>
                            <div>
                                ${{
                                    Number(
                                        props.order.shipping_cost || 0,
                                    ).toFixed(2)
                                }}
                            </div>
                        </div>
                        <div class="flex justify-between font-semibold">
                            <div>Grand total</div>
                            <div>
                                ${{
                                    Number(
                                        props.order.total ||
                                            props.order.total_amount ||
                                            0,
                                    ).toFixed(2)
                                }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="font-medium">Billing</h4>
                        <div class="mt-1 text-sm text-gray-700">
                            {{ props.order.billing_address }}<br />
                            {{ props.order.billing_city }}
                            {{ props.order.billing_zip_code }}<br />
                            {{ props.order.billing_state }}<br />
                            {{ props.order.billing_country }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4 class="font-medium">Shipping</h4>
                        <div class="mt-1 text-sm text-gray-700">
                            {{ props.order.shipping_address }}<br />
                            {{ props.order.shipping_city }}
                            {{ props.order.shipping_zip_code }}<br />
                            {{ props.order.shipping_state }}<br />
                            {{ props.order.shipping_country }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4 class="font-medium">Notes</h4>
                        <div class="mt-1 text-sm text-gray-700">
                            {{ props.order.notes || '—' }}
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </ShopLayout>
</template>
