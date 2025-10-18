<template>
    <ShopLayout :user="user" :cart-items-count="cartItems.length">
        <div class="container mx-auto px-4 py-8">
            <h1 class="mb-8 text-3xl font-bold">Checkout</h1>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <!-- Checkout Form -->
                <div class="md:col-span-2">
                    <form @submit.prevent="submitOrder" class="space-y-8">
                        <!-- Shipping Address -->
                        <div class="space-y-4">
                            <h2 class="text-xl font-semibold">
                                Shipping Address
                            </h2>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        for="shipping_address"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        Address
                                    </label>
                                    <input
                                        id="shipping_address"
                                        v-model="form.shipping_address"
                                        type="text"
                                        class="h-9 w-full rounded-md border bg-background px-3"
                                        required
                                    />
                                </div>
                                <div>
                                    <label
                                        for="shipping_city"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        City
                                    </label>
                                    <input
                                        id="shipping_city"
                                        v-model="form.shipping_city"
                                        type="text"
                                        class="h-9 w-full rounded-md border bg-background px-3"
                                        required
                                    />
                                </div>
                                <div>
                                    <label
                                        for="shipping_state"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        State
                                    </label>
                                    <input
                                        id="shipping_state"
                                        v-model="form.shipping_state"
                                        type="text"
                                        class="h-9 w-full rounded-md border bg-background px-3"
                                        required
                                    />
                                </div>
                                <div>
                                    <label
                                        for="shipping_zip_code"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        ZIP Code
                                    </label>
                                    <input
                                        id="shipping_zip_code"
                                        v-model="form.shipping_zip_code"
                                        type="text"
                                        class="h-9 w-full rounded-md border bg-background px-3"
                                        required
                                    />
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        for="shipping_country"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        Country
                                    </label>
                                    <select
                                        id="shipping_country"
                                        v-model="form.shipping_country"
                                        class="h-9 w-full rounded-md border bg-background px-3"
                                        required
                                    >
                                        <option value="">
                                            Select a country
                                        </option>
                                        <option value="US">
                                            United States
                                        </option>
                                        <option value="CA">Canada</option>
                                        <option value="GB">
                                            United Kingdom
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Address -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold">
                                    Billing Address
                                </h2>
                                <label class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        v-model="sameAsShipping"
                                        class="rounded border-border"
                                    />
                                    <span class="text-sm"
                                        >Same as shipping</span
                                    >
                                </label>
                            </div>

                            <div
                                v-if="!sameAsShipping"
                                class="grid grid-cols-1 gap-4 md:grid-cols-2"
                            >
                                <div>
                                    <label
                                        for="billing_address"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        Address
                                    </label>
                                    <input
                                        id="billing_address"
                                        v-model="form.billing_address"
                                        type="text"
                                        class="h-9 w-full rounded-md border bg-background px-3"
                                        required
                                    />
                                </div>
                                <div>
                                    <label
                                        for="billing_city"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        City
                                    </label>
                                    <input
                                        id="billing_city"
                                        v-model="form.billing_city"
                                        type="text"
                                        class="h-9 w-full rounded-md border bg-background px-3"
                                        required
                                    />
                                </div>
                                <div>
                                    <label
                                        for="billing_state"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        State
                                    </label>
                                    <input
                                        id="billing_state"
                                        v-model="form.billing_state"
                                        type="text"
                                        class="h-9 w-full rounded-md border bg-background px-3"
                                        required
                                    />
                                </div>
                                <div>
                                    <label
                                        for="billing_zip_code"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        ZIP Code
                                    </label>
                                    <input
                                        id="billing_zip_code"
                                        v-model="form.billing_zip_code"
                                        type="text"
                                        class="h-9 w-full rounded-md border bg-background px-3"
                                        required
                                    />
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        for="billing_country"
                                        class="mb-1 block text-sm font-medium"
                                    >
                                        Country
                                    </label>
                                    <select
                                        id="billing_country"
                                        v-model="form.billing_country"
                                        class="h-9 w-full rounded-md border bg-background px-3"
                                        required
                                    >
                                        <option value="">
                                            Select a country
                                        </option>
                                        <option value="US">
                                            United States
                                        </option>
                                        <option value="CA">Canada</option>
                                        <option value="GB">
                                            United Kingdom
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div>
                            <label
                                for="notes"
                                class="mb-1 block text-sm font-medium"
                            >
                                Order Notes (optional)
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="3"
                                class="w-full rounded-md border bg-background px-3 py-2"
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            class="btn-primary w-full"
                            :disabled="form.processing"
                        >
                            <span
                                v-if="form.processing"
                                class="h-5 w-5 animate-spin rounded-full border-2 border-current border-r-transparent"
                            ></span>
                            Place Order
                        </button>
                    </form>
                </div>

                <!-- Order Summary -->
                <div>
                    <div class="sticky top-20 space-y-6">
                        <div class="rounded-lg bg-card p-6">
                            <h2 class="mb-4 text-lg font-medium">
                                Order Summary
                            </h2>

                            <!-- Cart Items -->
                            <div class="mb-6 space-y-4">
                                <div
                                    v-for="item in cartItems"
                                    :key="item.id"
                                    class="flex justify-between text-sm"
                                >
                                    <span>
                                        {{ item.product.name }}
                                        <span class="text-foreground/70">
                                            × {{ item.quantity }}
                                        </span>
                                    </span>
                                    <span
                                        >${{
                                            (
                                                item.product.price *
                                                item.quantity
                                            ).toFixed(2)
                                        }}</span
                                    >
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-foreground/70"
                                        >Subtotal</span
                                    >
                                    <span>${{ subtotal.toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-foreground/70"
                                        >Shipping</span
                                    >
                                    <span>Free</span>
                                </div>
                                <div
                                    class="flex justify-between border-t pt-4 font-medium"
                                >
                                    <span>Total</span>
                                    <span>${{ subtotal.toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border p-4">
                            <h3 class="mb-2 font-medium">We Accept</h3>
                            <div class="flex gap-2">
                                <div
                                    v-for="card in [
                                        'visa',
                                        'mastercard',
                                        'amex',
                                    ]"
                                    :key="card"
                                    class="h-8 w-12 rounded bg-muted"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import ShopLayout from '@/layouts/shop/ShopLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    user: Object,
    cartItems: {
        type: Array,
        default: () => [],
    },
});

const sameAsShipping = ref(true);

const form = useForm({
    shipping_address: '',
    shipping_city: '',
    shipping_state: '',
    shipping_zip_code: '',
    shipping_country: '',
    billing_address: '',
    billing_city: '',
    billing_state: '',
    billing_zip_code: '',
    billing_country: '',
    notes: '',
});

const subtotal = computed(() => {
    return props.cartItems.reduce((total, item) => {
        return total + item.product.price * item.quantity;
    }, 0);
});

watch(sameAsShipping, (value) => {
    if (value) {
        form.billing_address = form.shipping_address;
        form.billing_city = form.shipping_city;
        form.billing_state = form.shipping_state;
        form.billing_zip_code = form.shipping_zip_code;
        form.billing_country = form.shipping_country;
    }
});

const submitOrder = () => {
    if (sameAsShipping.value) {
        form.billing_address = form.shipping_address;
        form.billing_city = form.shipping_city;
        form.billing_state = form.shipping_state;
        form.billing_zip_code = form.shipping_zip_code;
        form.billing_country = form.shipping_country;
    }

    form.post('/orders');
};
</script>
