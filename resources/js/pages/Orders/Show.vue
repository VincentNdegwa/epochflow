<template>
    <AppLayout :title="`Order #${order.order_number}`">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">
                            Order #{{ order.order_number }}
                        </h1>
                        <p class="text-foreground/70">
                            Placed on
                            {{
                                new Date(order.created_at).toLocaleDateString()
                            }}
                        </p>
                    </div>
                    <div>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                            :class="{
                                'bg-yellow-500/10 text-yellow-500':
                                    order.status === 'pending',
                                'bg-blue-500/10 text-blue-500':
                                    order.status === 'processing',
                                'bg-green-500/10 text-green-500':
                                    order.status === 'completed',
                                'bg-red-500/10 text-red-500':
                                    order.status === 'cancelled',
                            }"
                        >
                            {{ order.status }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <!-- Order Items -->
                <div class="md:col-span-2">
                    <div class="overflow-hidden rounded-lg bg-card">
                        <div class="border-b p-6">
                            <h2 class="text-lg font-medium">Order Items</h2>
                        </div>

                        <div class="divide-y">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="flex gap-4 p-6"
                            >
                                <!-- Product Image -->
                                <div
                                    class="h-20 w-20 flex-shrink-0 overflow-hidden rounded bg-muted"
                                >
                                    <img
                                        :src="item.product.primary_image"
                                        :alt="item.product.name"
                                        class="h-full w-full object-cover object-center"
                                    />
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <div>
                                            <Link
                                                :href="`/shop/products/${item.product.slug}`"
                                                class="font-medium hover:text-primary"
                                            >
                                                {{ item.product.name }}
                                            </Link>
                                            <div
                                                class="text-sm text-foreground/70"
                                            >
                                                {{ item.product.category.name }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium">
                                                ${{ item.price }}
                                            </div>
                                            <div
                                                class="text-sm text-foreground/70"
                                            >
                                                ${{ item.price }} ×
                                                {{ item.quantity }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Info -->
                <div class="space-y-6">
                    <!-- Summary -->
                    <div class="rounded-lg bg-card p-6">
                        <h2 class="mb-4 text-lg font-medium">Order Summary</h2>

                        <div class="space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-foreground/70">Subtotal</span>
                                <span>${{ order.total_amount }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-foreground/70">Shipping</span>
                                <span>Free</span>
                            </div>
                            <div
                                class="flex justify-between border-t pt-4 font-medium"
                            >
                                <span>Total</span>
                                <span>${{ order.total_amount }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="rounded-lg bg-card p-6">
                        <h2 class="mb-4 text-lg font-medium">Customer</h2>

                        <div class="space-y-4">
                            <div>
                                <div class="font-medium">
                                    {{ order.customer.name }}
                                </div>
                                <div class="text-sm text-foreground/70">
                                    {{ order.customer.email }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="rounded-lg bg-card p-6">
                        <h2 class="mb-4 text-lg font-medium">
                            Shipping Address
                        </h2>

                        <div class="space-y-1 text-sm">
                            <div>{{ order.shipping_address }}</div>
                            <div>
                                {{ order.shipping_city }},
                                {{ order.shipping_state }}
                                {{ order.shipping_zip_code }}
                            </div>
                            <div>{{ order.shipping_country }}</div>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="rounded-lg bg-card p-6">
                        <h2 class="mb-4 text-lg font-medium">
                            Billing Address
                        </h2>

                        <div class="space-y-1 text-sm">
                            <div>{{ order.billing_address }}</div>
                            <div>
                                {{ order.billing_city }},
                                {{ order.billing_state }}
                                {{ order.billing_zip_code }}
                            </div>
                            <div>{{ order.billing_country }}</div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div v-if="order.status === 'pending'">
                        <button
                            @click="cancelOrder"
                            class="btn-primary w-full bg-destructive hover:bg-destructive/90"
                        >
                            Cancel Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import type { OrdertWithCustomer } from '../../types';

const props = defineProps<{ order: OrdertWithCustomer }>();

const cancelOrder = () => {
    if (
        confirm(
            `Are you sure you want to cancel order #${props.order.order_number}?`,
        )
    ) {
        router.post(
            `/orders/${props.order.id}/cancel`,
            {},
            {
                preserveScroll: true,
            },
        );
    }
};
</script>
