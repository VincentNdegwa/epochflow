<script setup lang="ts">
import { route } from 'ziggy-js';
import { Order, Store } from '../../../../../types';
import NormalLayout from '../../layouts/NormalLayout.vue';

interface Paginated<T> {
    data: T[];
}
const props = defineProps<{
    orders: Paginated<Order>;
    store: Store;
    customer: any;
}>();
</script>

<template>
    <NormalLayout
        :store="props.store"
        :customer="props.customer"
        :cart-items-count="0"
        :title="'My Orders'"
        :description="'View and manage your orders at ' + props.store.name"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-bold">My Orders</h1>

            <div class="space-y-4">
                <div
                    v-for="order in props.orders.data"
                    :key="order.id"
                    class="rounded-lg bg-white p-4 shadow-sm"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Order</div>
                            <div class="mt-1 text-lg font-semibold">
                                {{ order.order_number }}
                            </div>
                            <div class="text-sm text-gray-400">
                                Placed
                                {{
                                    new Date(order.created_at).toLocaleString()
                                }}
                            </div>
                        </div>

                        <div class="flex-1">
                            <ul class="flex gap-4 overflow-x-auto py-2">
                                <li
                                    v-for="item in (order.items || []).slice(
                                        0,
                                        3,
                                    )"
                                    :key="item.id"
                                    class="flex items-center gap-3 rounded-md border p-2"
                                >
                                    <img
                                        v-if="item.product?.primary_image"
                                        :src="item.product.primary_image"
                                        alt=""
                                        class="h-12 w-12 rounded object-cover"
                                    />
                                    <div class="min-w-0">
                                        <div
                                            class="truncate text-sm font-medium"
                                        >
                                            {{ item.product?.name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Qty: {{ item.quantity }} • ${{
                                                (
                                                    Number(item.price) *
                                                    item.quantity
                                                ).toFixed(2)
                                            }}
                                        </div>
                                    </div>
                                </li>
                                <li
                                    v-if="(order.items || []).length > 3"
                                    class="flex items-center text-sm text-gray-500"
                                >
                                    +{{ (order.items || []).length - 3 }} more
                                </li>
                            </ul>
                        </div>

                        <div class="text-right">
                            <div class="text-sm text-gray-500">Total</div>
                            <div class="mt-1 text-lg font-semibold">
                                ${{
                                    Number(
                                        (order as any).total ||
                                            (order as any).total_amount ||
                                            0,
                                    ).toFixed(2)
                                }}
                            </div>
                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                                    :class="{
                                        'bg-yellow-100 text-yellow-800':
                                            order.status === 'pending',
                                        'bg-green-100 text-green-800':
                                            order.status === 'paid',
                                        'bg-red-100 text-red-800':
                                            order.status === 'cancelled' ||
                                            order.status === 'canceled',
                                    }"
                                >
                                    {{ order.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            {{ (order.items || []).length }} items
                        </div>
                        <div>
                            <a
                                :href="
                                    route('customer.orders.show', {
                                        storeSlug: props.store.slug,
                                        slug: order.slug,
                                    })
                                "
                                class="inline-flex items-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white"
                            >
                                View order
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </NormalLayout>
</template>
