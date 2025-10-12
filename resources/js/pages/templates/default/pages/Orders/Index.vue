<script setup lang="ts">
import { route } from 'ziggy-js';
import ShopLayout from '../../layouts/ShopLayout.vue';
import { Store } from '../../../../../types';

interface Paginated<T> {
    data: T[];
}
const props = defineProps<{
    orders: Paginated<any>;
    store: Store;
    customer: any;
}>();
</script>

<template>
    <ShopLayout
        :store="props.store"
        :customer="props.customer"
        :cart-items-count="0"
    >
        <div class="mx-auto max-w-7xl px-4 py-12">
            <h1 class="mb-6 text-2xl font-bold">My Orders</h1>
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="order in props.orders.data"
                    :key="order.id"
                    class="rounded-lg bg-white p-4"
                >
                    <div class="flex justify-between">
                        <div>
                            <div class="font-semibold">
                                {{ order.order_number }}
                            </div>
                            <div class="text-sm text-gray-600">
                                Status: {{ order.status }}
                            </div>
                        </div>
                        <div>
                            <a
                                :href="
                                    route('customer.orders.show', {
                                        storeSlug: props.store.slug,
                                        order: order.id,
                                    })
                                "
                                class="text-primary"
                                >View</a
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
