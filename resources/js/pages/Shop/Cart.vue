<template>
  <ShopLayout :user="user" :cart-items-count="cartItems.length">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

      <div v-if="cartItems.length" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="md:col-span-2 space-y-4">
          <div
            v-for="item in cartItems"
            :key="item.id"
            class="flex gap-4 p-4 rounded-lg bg-card"
          >
            <!-- Product Image -->
            <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md bg-muted">
              <img
                :src="item.product.primary_image"
                :alt="item.product.name"
                class="h-full w-full object-cover object-center"
              />
            </div>

            <!-- Product Info -->
            <div class="flex flex-1 flex-col justify-between">
              <div class="flex justify-between">
                <div>
                  <h3 class="text-base font-medium">
                    <Link :href="`/shop/products/${item.product.slug}`">
                      {{ item.product.name }}
                    </Link>
                  </h3>
                  <p class="mt-1 text-sm text-foreground/70">
                    {{ item.product.category.name }}
                  </p>
                </div>
                <p class="text-sm font-medium">
                  ${{ (item.product.price * item.quantity).toFixed(2) }}
                </p>
              </div>

              <div class="flex items-center justify-between">
                <!-- Quantity Controls -->
                <div class="flex items-center gap-2">
                  <button
                    @click="updateQuantity(item, item.quantity - 1)"
                    :disabled="item.quantity <= 1"
                    class="h-8 w-8 rounded-md border flex items-center justify-center disabled:opacity-50"
                  >
                    <Minus class="h-4 w-4" />
                  </button>
                  <span class="w-8 text-center">{{ item.quantity }}</span>
                  <button
                    @click="updateQuantity(item, item.quantity + 1)"
                    :disabled="item.quantity >= item.product.stock"
                    class="h-8 w-8 rounded-md border flex items-center justify-center disabled:opacity-50"
                  >
                    <Plus class="h-4 w-4" />
                  </button>
                </div>

                <!-- Remove Button -->
                <button
                  @click="removeItem(item)"
                  class="text-sm text-destructive hover:text-destructive/80"
                >
                  Remove
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="space-y-6">
          <div class="rounded-lg bg-card p-6">
            <h2 class="text-lg font-medium mb-4">Order Summary</h2>
            
            <div class="space-y-4">
              <div class="flex justify-between text-sm">
                <span class="text-foreground/70">Subtotal</span>
                <span>${{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-foreground/70">Shipping</span>
                <span>Calculated at checkout</span>
              </div>
              <div class="border-t pt-4 flex justify-between font-medium">
                <span>Total</span>
                <span>${{ subtotal.toFixed(2) }}</span>
              </div>
            </div>

            <button
              @click="checkout"
              class="btn-primary w-full mt-6"
            >
              Proceed to Checkout
            </button>
          </div>

          <div class="rounded-lg border p-4">
            <h3 class="font-medium mb-2">We Accept</h3>
            <div class="flex gap-2">
              <div v-for="card in ['visa', 'mastercard', 'amex']" :key="card" class="h-8 w-12 rounded bg-muted"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty Cart -->
      <div
        v-else
        class="text-center py-12"
      >
        <ShoppingCart class="h-12 w-12 mx-auto text-foreground/30" />
        <h2 class="mt-4 text-lg font-medium">Your cart is empty</h2>
        <p class="mt-2 text-sm text-foreground/70">
          Start shopping to add items to your cart
        </p>
        <Link href="/shop" class="btn-primary mt-6">
          Continue Shopping
        </Link>
      </div>
    </div>
  </ShopLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ShoppingCart, Plus, Minus } from 'lucide-vue-next'
import ShopLayout from '@/layouts/shop/ShopLayout.vue'

const props = defineProps({
  user: Object,
  cartItems: {
    type: Array,
    default: () => []
  }
})

const subtotal = computed(() => {
  return props.cartItems.reduce((total, item) => {
    return total + (item.product.price * item.quantity)
  }, 0)
})

const updateQuantity = (item, quantity) => {
  router.put(`/cart/${item.id}`, { quantity }, {
    preserveScroll: true
  })
}

const removeItem = (item) => {
  router.delete(`/cart/${item.id}`, {
    preserveScroll: true
  })
}

const checkout = () => {
  router.get('/checkout')
}
</script>