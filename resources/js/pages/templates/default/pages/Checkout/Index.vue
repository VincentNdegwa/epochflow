<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import { Customer, Store } from '../../../../../types';
import ShopLayout from '../../layouts/ShopLayout.vue';
import axios from 'axios';
import { useToast } from '../../../../../composables/useToast';

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

const sameAsBilling = ref(false);
const coupon = ref('');
const shippingMethod = ref('none');

const SHIPPING_COSTS: Record<string, number> = {
    none: 0.0,
    standard: 5.0,
    express: 12.0,
};

const { toast } = useToast();
const isProcessing = ref(false);

const form = useForm({
    ...props.customer,
    notes: '',
    payment_method: 'cod',
    coupon: '',
    shipping_method: shippingMethod.value,
    shipping_cost: SHIPPING_COSTS[shippingMethod.value],
});

const localErrors = ref<Record<string, string>>({});

function validate(): boolean {
    localErrors.value = {};

    const requiredFields = [
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_zip_code',
        'billing_country',
    ];

    const shippingFields = [
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip_code',
        'shipping_country',
    ];

    requiredFields.forEach((f) => {
        if (!String((form as any)[f] || '').trim()) {
            localErrors.value[f] = 'This field is required.';
        }
    });

    shippingFields.forEach((f) => {
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

watch(shippingMethod, (val) => {
    form.shipping_method = val;
    form.shipping_cost = SHIPPING_COSTS[val] ?? 0;
});

watch(sameAsBilling, (val) => {
    if (val) {
        form.shipping_address = form.billing_address;
        form.shipping_city = form.billing_city;
        form.shipping_state = form.billing_state;
        form.shipping_zip_code = form.billing_zip_code;
        form.shipping_country = form.billing_country;
    }
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

    if (sameAsBilling.value) {
        form.shipping_address = form.billing_address;
        form.shipping_city = form.billing_city;
        form.shipping_state = form.billing_state;
        form.shipping_zip_code = form.billing_zip_code;
        form.shipping_country = form.billing_country;
    }

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

            <div class="rounded-lg bg-white p-6">
                <h2 class="font-semibold">Order Summary</h2>
                <div class="mt-4 space-y-2">
                    <div
                        v-for="item in props.cartItems || []"
                        :key="item.id"
                        class="flex justify-between"
                    >
                        <div>
                            {{ item.product?.name }} x {{ item.quantity }}
                        </div>
                        <div>
                            ${{
                                (
                                    Number(item.product?.price || 0) *
                                    item.quantity
                                ).toFixed(2)
                            }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex justify-between">
                    <div>Items</div>
                    <div>${{ itemsTotal.toFixed(2) }}</div>
                </div>
                <div class="mt-2 flex justify-between">
                    <div>Shipping ({{ shippingMethod }})</div>
                    <div>${{ shippingCost.toFixed(2) }}</div>
                </div>
                <div class="mt-2 flex justify-between">
                    <div>Discount</div>
                    <div>-${{ couponDiscount.toFixed(2) }}</div>
                </div>
                <div class="mt-4 text-right font-bold">
                    Grand Total: ${{ grandTotal.toFixed(2) }}
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-white p-6">
                <h2 class="font-semibold">Shipping & Billing</h2>
                <div class="mt-4 grid grid-cols-1 gap-4">
                    <label class="flex flex-col">
                        <span class="text-sm font-medium"
                            >Billing address
                            <span class="text-red-600">*</span></span
                        >
                        <input
                            v-model="form.billing_address"
                            placeholder="Billing address"
                            class="input"
                            required
                        />
                        <div
                            v-if="localErrors.billing_address"
                            class="text-sm text-red-600"
                        >
                            {{ localErrors.billing_address }}
                        </div>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex flex-col">
                            <span class="text-sm"
                                >Billing city
                                <span class="text-red-600">*</span></span
                            >
                            <input
                                v-model="form.billing_city"
                                placeholder="Billing city"
                                class="input"
                                required
                            />
                            <div
                                v-if="localErrors.billing_city"
                                class="text-sm text-red-600"
                            >
                                {{ localErrors.billing_city }}
                            </div>
                        </label>
                        <label class="flex flex-col">
                            <span class="text-sm"
                                >Billing zip
                                <span class="text-red-600">*</span></span
                            >
                            <input
                                v-model="form.billing_zip"
                                placeholder="Billing zip"
                                class="input"
                                required
                            />
                            <div
                                v-if="localErrors.billing_zip_code"
                                class="text-sm text-red-600"
                            >
                                {{ localErrors.billing_zip_code }}
                            </div>
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex flex-col">
                            <span class="text-sm"
                                >Billing state
                                <span class="text-red-600">*</span></span
                            >
                            <input
                                v-model="form.billing_state"
                                placeholder="Billing state"
                                class="input"
                                required
                            />
                            <div
                                v-if="localErrors.billing_state"
                                class="text-sm text-red-600"
                            >
                                {{ localErrors.billing_state }}
                            </div>
                        </label>
                        <label class="flex flex-col">
                            <span class="text-sm"
                                >Billing country
                                <span class="text-red-600">*</span></span
                            >
                            <input
                                v-model="form.billing_country"
                                placeholder="Billing country"
                                class="input"
                                required
                            />
                            <div
                                v-if="localErrors.billing_country"
                                class="text-sm text-red-600"
                            >
                                {{ localErrors.billing_country }}
                            </div>
                        </label>
                    </div>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" v-model="sameAsBilling" />
                        <span>Shipping address same as billing</span>
                    </label>
                    <label class="flex flex-col">
                        <span class="text-sm"
                            >Shipping address
                            <span class="text-red-600">*</span></span
                        >
                        <input
                            v-model="form.shipping_address"
                            placeholder="Shipping address"
                            class="input"
                            required
                        />
                        <div
                            v-if="localErrors.shipping_address"
                            class="text-sm text-red-600"
                        >
                            {{ localErrors.shipping_address }}
                        </div>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex flex-col">
                            <span class="text-sm"
                                >Shipping city
                                <span class="text-red-600">*</span></span
                            >
                            <input
                                v-model="form.shipping_city"
                                placeholder="Shipping city"
                                class="input"
                                required
                            />
                            <div
                                v-if="localErrors.shipping_city"
                                class="text-sm text-red-600"
                            >
                                {{ localErrors.shipping_city }}
                            </div>
                        </label>
                        <label class="flex flex-col">
                            <span class="text-sm"
                                >Shipping zip
                                <span class="text-red-600">*</span></span
                            >
                            <input
                                v-model="form.shipping_zip"
                                placeholder="Shipping zip"
                                class="input"
                                required
                            />
                            <div
                                v-if="localErrors.shipping_zip_code"
                                class="text-sm text-red-600"
                            >
                                {{ localErrors.shipping_zip_code }}
                            </div>
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex flex-col">
                            <span class="text-sm"
                                >Shipping state
                                <span class="text-red-600">*</span></span
                            >
                            <input
                                v-model="form.shipping_state"
                                placeholder="Shipping state"
                                class="input"
                                required
                            />
                            <div
                                v-if="localErrors.shipping_state"
                                class="text-sm text-red-600"
                            >
                                {{ localErrors.shipping_state }}
                            </div>
                        </label>
                        <label class="flex flex-col">
                            <span class="text-sm"
                                >Shipping country
                                <span class="text-red-600">*</span></span
                            >
                            <input
                                v-model="form.shipping_country"
                                placeholder="Shipping country"
                                class="input"
                                required
                            />
                            <div
                                v-if="localErrors.shipping_country"
                                class="text-sm text-red-600"
                            >
                                {{ localErrors.shipping_country }}
                            </div>
                        </label>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="mb-2 block font-medium"
                        >Shipping method</label
                    >
                    <select v-model="shippingMethod" class="input">
                        <option value="none">
                            No Shipping (${{ SHIPPING_COSTS.none.toFixed(2) }})
                        </option>

                        <option value="standard">
                            Standard (${{ SHIPPING_COSTS.standard.toFixed(2) }})
                        </option>
                        <option value="express">
                            Express (${{ SHIPPING_COSTS.express.toFixed(2) }})
                        </option>
                    </select>
                </div>
                <div class="mt-4">
                    <label class="mb-2 block font-medium">Coupon</label>
                    <div class="flex gap-2">
                        <input
                            v-model="coupon"
                            placeholder="Coupon code"
                            class="input"
                        />
                        <button
                            @click.prevent="() => {}"
                            class="btn bg-primary/60"
                        >
                            Apply
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-white p-6">
                <h2 class="font-semibold">Payment</h2>
                <div class="mt-4 gap-4 space-y-4">
                    <label class="mx-2 inline-flex items-center gap-2">
                        <input
                            type="radio"
                            v-model="form.payment_method"
                            value="cod"
                        />
                        <span>Cash on Delivery</span>
                    </label>

                    <label class="mx-2 inline-flex items-center gap-2">
                        <input
                            type="radio"
                            v-model="form.payment_method"
                            value="paypal"
                        />
                        <span class="flex items-center gap-2">
                            PayPal
                            <img src="/images/paypal_logo.png" alt="PayPal" class="h-15" />
                        </span>
                    </label>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
                        @click="placeOrder"
                        :disabled="isProcessing"
                    >
                        <template v-if="isProcessing">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </template>
                        <template v-else>
                            Confirm & Pay ${{ grandTotal.toFixed(2) }}
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
