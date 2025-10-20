<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { Customer, Store } from "../../types";

import ToastLayout from '../ToastLayout.vue';
import ShopHeader from '../../components/ShopHeader.vue';

interface Props {
    store: Store;
    cartItemsCount: number;
    customer?: Customer | null;
    initialSearch?: string | null;
}

defineProps<Props>();



</script>

<template>
    <ToastLayout>
        <Head :title="store.name" />

        <div class="min-h-screen">
            <header class="relative">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-foreground to-foreground/90 backdrop-blur-sm"
                >
                    <div
                        class="bg-grid-white/[0.05] absolute inset-0 bg-[size:60px_60px]"
                    ></div>
                </div>

                <!-- Top navigation (componentized) -->
                <ShopHeader
                    :store="store"
                    :customer="customer"
                    :cart-items-count="cartItemsCount"
                />

              
            </header>

            <main class="relative">
                <!-- Page content -->
                <slot />
            </main>

        </div>
    </ToastLayout>
</template>


