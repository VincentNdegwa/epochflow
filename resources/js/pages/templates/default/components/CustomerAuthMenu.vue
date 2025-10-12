<template>
    <div class="flex items-center gap-3">
        <template v-if="customer">
            <!-- Small avatar + name and dropdown -->
            <Menu as="div" class="relative">
                <MenuButton
                    class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-sm hover:bg-white/20"
                >
                    <div
                        class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-gray-200"
                    >
                        <User class="h-5 w-5 text-gray-500" />
                    </div>
                    <span
                        class="hidden text-sm font-medium text-white sm:inline"
                        >{{ customer.name }}</span
                    >
                </MenuButton>

                <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <MenuItems
                        class="ring-opacity-5 absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black focus:outline-none"
                    >
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                                <Link
                                    :href="
                                        route('orders.index', {
                                            storeSlug: store.slug,
                                        })
                                    "
                                    :class="[
                                        active ? 'bg-gray-100' : '',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                >
                                    <ShoppingBag
                                        class="mr-2 h-4 w-4 text-gray-600"
                                    />
                                    Orders
                                </Link>
                            </MenuItem>
                            <MenuItem v-slot="{ active }">
                                <Link
                                    :href="
                                        route('cart.index', {
                                            storeSlug: store.slug,
                                        })
                                    "
                                    :class="[
                                        active ? 'bg-gray-100' : '',
                                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                    ]"
                                >
                                    <ShoppingCart
                                        class="mr-2 h-4 w-4 text-gray-600"
                                    />
                                    Cart
                                    <span
                                        v-if="cartItemsCount > 0"
                                        class="ml-auto inline-flex items-center justify-center rounded-full bg-primary px-2 py-0.5 text-xs text-white"
                                        >{{ cartItemsCount }}</span
                                    >
                                </Link>
                            </MenuItem>
                            <div class="my-1 h-px bg-gray-100" />
                            <MenuItem v-slot="{ active }">
                                <button
                                    @click="logout"
                                    class="flex w-full items-center px-2 py-2 text-left text-sm"
                                >
                                    <LogOut
                                        class="mr-2 h-4 w-4 text-gray-600"
                                    />
                                    Log out
                                </button>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
        </template>

        <template v-else>
            <div class="flex items-center gap-2">
                <Link
                    :href="route('customer.login', { storeSlug: store.slug })"
                    class="text-sm text-white/90 hover:underline"
                    >Login</Link
                >
                <Link
                    :href="
                        route('customer.register', { storeSlug: store.slug })
                    "
                    class="ml-2 rounded-md bg-primary px-3 py-1 text-sm font-semibold text-white"
                    >Sign up</Link
                >
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, ShoppingBag, ShoppingCart, User } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { Customer } from '../types';

interface Props {
    customer: Customer | null;
    cartItemsCount: number;
    store: { slug: string };
}

const props = defineProps<Props>();

const { customer, cartItemsCount, store } = props;

function logout() {
    router.post(route('customer.logout', { storeSlug: store.slug }));
}
</script>
