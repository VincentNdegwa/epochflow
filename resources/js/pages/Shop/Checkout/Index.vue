<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import { Customer, Store } from '../../../types';
import ShopLayout from '../../../layouts/Shop/ShopLayout.vue';
import axios from 'axios';
import { useToast } from '../../../composables/useToast';

interface CartItem {
    id: number;
    quantity: number;
    product: any;
}

const props = defineProps<{
    cartItems: CartItem[];
    total: number;
    store: Store;
    customer: Customer;
}>();

const coupon = ref('');
const shippingMethod = ref('standard');

const SHIPPING_COSTS: Record<string, number> = {
    none: 0.0,
    standard: 5.0,
    express: 12.0,
};

const { toast } = useToast();
const isProcessing = ref(false);

const form = useForm({
    ...props.customer,
    address: props.customer?.shipping_address || '',
    city: props.customer?.shipping_city || '',
    state: props.customer?.shipping_state || '',
    zip_code: props.customer?.shipping_zip_code || '',
    country: props.customer?.shipping_country || '',
    notes: '',
    payment_method: 'paypal',
    coupon: '',
    shipping_method: shippingMethod.value,
    shipping_cost: SHIPPING_COSTS[shippingMethod.value],
});

const localErrors = ref<Record<string, string>>({});

function validate(): boolean {
    localErrors.value = {};

    const requiredFields = [
        'address',
        'city',
        'state',
        'zip_code',
        'country',
    ];

    requiredFields.forEach((f) => {
        if (!String((form as any)[f] || '').trim()) {
            localErrors.value[f] = 'This field is required.';
        }
    });

    if (!form.payment_method) {
        localErrors.value.payment_method = 'Please select a payment method.';
    }

    console.log(Object.keys(localErrors.value).length);

    return Object.keys(localErrors.value).length === 0;
}

// Keep form.shipping_cost in sync with shippingMethod
watch(shippingMethod, (val: string) => {
    form.shipping_method = val;
    form.shipping_cost = SHIPPING_COSTS[val] ?? 0;
});

const itemsTotal = computed(() => {
    return (props.cartItems || []).reduce((sum: number, item: CartItem) => {
        return sum + Number(item.product?.price || 0) * item.quantity;
    }, 0);
});

const shippingCost = computed(() => SHIPPING_COSTS[shippingMethod.value] || 0);

const couponDiscount = computed(() => {
    // placeholder: simple coupon logic for demo (e.g., 'SAVE10' => 10% off)
    if (!coupon.value) return 0;
    if (coupon.value.toUpperCase() === 'SAVE10') return itemsTotal.value * 0.1;
    return 0;
});

const grandTotal = computed(() => {
    return Math.max(
        0,
        itemsTotal.value - couponDiscount.value + shippingCost.value,
    );
});

async function placeOrder() {
    if (isProcessing.value) return;

    form.coupon = coupon.value;
    form.shipping_method = shippingMethod.value;
    form.shipping_cost = shippingCost.value;

    // Set both billing and shipping to the same address

    const isValid = validate();
    if (!isValid) {
        return;
    }

    isProcessing.value = true;

    try {
        const response = await axios.post(route('checkout.store', { storeSlug: props.store?.slug }), form.data());

        if (!response.data.success) {
            throw new Error(response.data.message || 'Failed to create order');
        }

        const { order, next_action } = response.data;

        if (next_action === 'process_payment' && form.payment_method === 'paypal') {
            try {
                const paypalResponse = await axios.post(route('payments.paypal.create', {
                    storeSlug: props.store?.slug,
                    order: order.id
                }));

                if (paypalResponse.data.approval_url) {
                    window.location.href = paypalResponse.data.approval_url;
                } else {
                    throw new Error('PayPal approval URL not found');
                }
            } catch (error: any) {
                toast.error('Failed to initiate PayPal payment: ' + (error.response?.data?.message || error.message));
            }
        } else if (next_action === 'complete_order') {
            window.location.href = route('customer.orders.show', {
                storeSlug: props.store?.slug,
                slug: order.slug
            });
        } else {
            throw new Error('Invalid next action');
        }
    } catch (error: any) {
        const errorMessage = error.response?.data?.message || error.message;
        toast.error('Failed to process order: ' + errorMessage);
    } finally {
        isProcessing.value = false;
    }
}
</script>

<template>
    <ShopLayout
        :store="props.store"
        :customer="props.customer"
        :cart-items-count="(props.cartItems || []).length"
    >
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <h1 class="mb-6 text-2xl font-bold">Checkout</h1>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left: Inputs -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-4">Shipping & Billing</h2>
                        <div class="space-y-4">
                            <label class="flex flex-col">
                                <span class="text-sm font-medium">Full address <span class="text-red-600">*</span></span>
                                <input v-model="form.address" class="input mt-1" placeholder="Street address, suite, etc." />
                            </label>

                            <div class="grid grid-cols-2 gap-4">
                                <input v-model="form.city" class="input" placeholder="City" />
                                <input v-model="form.zip_code" class="input" placeholder="ZIP / Postal code" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <input v-model="form.state" class="input" placeholder="State / Province" />
                                <input v-model="form.country" class="input" placeholder="Country" />
                            </div>

                            <label class="flex flex-col">
                                <span class="text-sm font-medium">Order notes (optional)</span>
                                <textarea v-model="form.notes" rows="3" class="input mt-1" placeholder="Any delivery instructions"></textarea>
                            </label>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-4">Payment</h2>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-3 border rounded-md cursor-pointer" :class="{ 'border-primary': form.payment_method === 'paypal' }">
                                <input type="radio" v-model="form.payment_method" value="paypal" />
                                <div>
                                    <div class="font-medium">PayPal</div>
                                    <div class="text-sm text-gray-500">Fast checkout with PayPal</div>
                                </div>
                                <img src="/images/paypal_logo.png" alt="PayPal" class="h-8 ml-auto" />
                            </label>

                            <label class="flex items-center gap-3 p-3 border rounded-md cursor-pointer" :class="{ 'border-primary': form.payment_method === 'cod' }">
                                <input type="radio" v-model="form.payment_method" value="cod" />
                                <div>
                                    <div class="font-medium">Cash on Delivery</div>
                                    <div class="text-sm text-gray-500">Pay when you receive the order</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right: Order summary & checkout -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-lg p-6 sticky top-6">
                        <h2 class="text-lg font-semibold mb-4">Order Summary</h2>

                        <div class="space-y-4">
                            <div v-for="item in props.cartItems || []" :key="item.id" class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ item.product?.name }}</div>
                                    <div class="text-sm text-gray-500">Qty: {{ item.quantity }}</div>
                                </div>
                                <div class="font-medium">${{ (Number(item.product?.price || 0) * item.quantity).toFixed(2) }}</div>
                            </div>
                        </div>

                        <div class="border-t my-4"></div>

                        <div class="space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Items</span>
                                <span>${{ (itemsTotal ?? 0).toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Shipping</span>
                                <span>${{ (shippingCost ?? 0).toFixed(2) }}</span>
                            </div>
                            <div v-if="couponDiscount > 0" class="flex justify-between text-sm text-green-600">
                                <span>Discount</span>
                                <span>- ${{ (couponDiscount ?? 0).toFixed(2) }}</span>
                            </div>
                        </div>

                        <div class="border-t my-4"></div>

                        <div class="flex justify-between items-center text-lg font-semibold">
                            <span>Total</span>
                            <span>${{ (grandTotal ?? 0).toFixed(2) }}</span>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium mb-2">Shipping method</label>
                            <select v-model="shippingMethod" class="input w-full">
                                <option value="none">No Shipping {{ (SHIPPING_COSTS.none ?? 0).toFixed(2) }}</option>
                                <option value="standard">Standard {{ (SHIPPING_COSTS.standard ?? 0).toFixed(2) }}</option>
                                <option value="express">Express {{ (SHIPPING_COSTS.express ?? 0).toFixed(2) }}</option>
                            </select>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium mb-2">Coupon</label>
                            <div class="flex gap-2">
                                <input v-model="coupon" class="input flex-1" placeholder="Coupon code" />
                                <button @click.prevent class="btn bg-primary/60 px-4">Apply</button>
                            </div>
                        </div>

                        <button
                            @click="placeOrder"
                            :disabled="isProcessing"
                            class="mt-6 w-full bg-primary text-white py-3 rounded-md hover:bg-primary/90 disabled:opacity-50"
                        >
                            <template v-if="isProcessing">
                                Processing...
                            </template>
                            <template v-else>
                                Place Order — ${{ (grandTotal ?? 0).toFixed(2) }}
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
