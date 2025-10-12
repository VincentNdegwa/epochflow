<script setup lang="ts">
import ShopLayout from '../../layouts/ShopLayout.vue';
const props = defineProps<{ order: any; store: any; customer: any }>();
</script>

<template>
    <ShopLayout
        :store="props.store"
        :customer="props.customer"
        :cart-items-count="0"
    >
        <div class="mx-auto max-w-7xl px-4 py-12">
            <h1 class="mb-6 text-2xl font-bold">
                Order {{ props.order.order_number }}
            </h1>
            <div class="rounded-lg bg-white p-6">
                <div class="mb-4">Status: {{ props.order.status }}</div>
                <div class="space-y-2">
                    <div
                        v-for="item in props.order.items"
                        :key="item.id"
                        class="flex justify-between"
                    >
                        <div>{{ item.product.name }} x {{ item.quantity }}</div>
                        <div>
                            ${{
                                (Number(item.price) * item.quantity).toFixed(2)
                            }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
