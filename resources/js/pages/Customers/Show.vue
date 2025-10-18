<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Customer - ${customer.name}`" />

        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">{{ customer.name }}</h1>
                    <div class="text-sm text-muted-foreground">
                        Customer details
                    </div>
                </div>
                <div>
                    <Link
                        href="/customers"
                        class="text-sm text-foreground/70 hover:text-foreground"
                        >Back to customers</Link
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <!-- Customer Info -->
                <div class="space-y-6">
                    <div class="rounded-lg bg-card p-6">
                        <h2 class="mb-4 text-lg font-medium">
                            Customer Information
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="text-sm text-foreground/70"
                                    >Email</label
                                >
                                <div>{{ customer.email }}</div>
                            </div>
                            <div>
                                <label class="text-sm text-foreground/70"
                                    >Phone</label
                                >
                                <div>{{ customer.phone }}</div>
                            </div>
                            <div>
                                <label class="text-sm text-foreground/70"
                                    >Address</label
                                >
                                <div>{{ customer.address }}</div>
                                <div>
                                    {{ customer.city }}, {{ customer.state }}
                                    {{ customer.zip_code }}
                                </div>
                                <div>{{ customer.country }}</div>
                            </div>
                            <div>
                                <label class="text-sm text-foreground/70"
                                    >Member Since</label
                                >
                                <div>
                                    {{
                                        new Date(
                                            customer.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order History -->
                <div class="md:col-span-2">
                    <div class="rounded-lg bg-card">
                        <div class="border-b p-6">
                            <h2 class="text-lg font-medium">Order History</h2>
                        </div>

                        <div class="divide-y">
                            <div
                                v-for="order in customer.orders"
                                :key="order.id"
                                class="p-6"
                            >
                                <div
                                    class="mb-4 flex items-center justify-between"
                                >
                                    <div>
                                        <div class="font-medium">
                                            Order #{{ order.order_number }}
                                        </div>
                                        <div class="text-sm text-foreground/70">
                                            {{
                                                new Date(
                                                    order.created_at,
                                                ).toLocaleDateString()
                                            }}
                                        </div>
                                    </div>
                                    <div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                            :class="{
                                                'bg-yellow-500/10 text-yellow-500':
                                                    order.status === 'pending',
                                                'bg-blue-500/10 text-blue-500':
                                                    order.status ===
                                                    'processing',
                                                'bg-green-500/10 text-green-500':
                                                    order.status ===
                                                    'completed',
                                                'bg-red-500/10 text-red-500':
                                                    order.status ===
                                                    'cancelled',
                                            }"
                                        >
                                            {{ order.status }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-sm">
                                    <div class="font-medium">
                                        ${{ order.total_amount }}
                                    </div>
                                    <div class="text-foreground/70">
                                        {{ order.items.length }} items
                                    </div>
                                </div>

                                <div class="mt-4 flex justify-end">
                                    <Link
                                        :href="`/orders/${order.id}`"
                                        class="text-sm text-foreground/70 hover:text-foreground"
                                    >
                                        View Order Details →
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{ customer: any }>();

const breadcrumbs = computed(
    () =>
        [
            { title: 'Customers', href: '/customers' },
            { title: props.customer?.name ?? 'Customer' },
        ] as BreadcrumbItem[],
);
</script>
