<template>
    <Menu as="div" class="relative">
        <MenuButton
            class="flex items-center gap-2 rounded-full bg-background p-2 text-sm font-medium hover:bg-accent"
        >
            <UserCircle class="h-6 w-6" />
            <span class="hidden md:inline">{{ customer.name }}</span>
            <ChevronDown class="h-4 w-4" />
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
                class="ring-opacity-5 absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-card shadow-lg ring-1 ring-black focus:outline-none"
            >
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <Link
                            :href="route('orders.index')"
                            :class="[
                                active
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-foreground',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <ShoppingBag
                                class="mr-2 h-4 w-4"
                                :class="
                                    active
                                        ? 'text-accent-foreground'
                                        : 'text-foreground/70'
                                "
                            />
                            My Orders
                        </Link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <Link
                            :href="
                                route('cart.index', {
                                    storeSlug: props.store.slug,
                                })
                            "
                            :class="[
                                active
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-foreground',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <ShoppingCart
                                class="mr-2 h-4 w-4"
                                :class="
                                    active
                                        ? 'text-accent-foreground'
                                        : 'text-foreground/70'
                                "
                            />
                            Shopping Cart
                            <span
                                v-if="cartItemsCount > 0"
                                class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-primary-foreground"
                            >
                                {{ cartItemsCount }}
                            </span>
                        </Link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <Link
                            :href="route('profile.edit')"
                            :class="[
                                active
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-foreground',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <User
                                class="mr-2 h-4 w-4"
                                :class="
                                    active
                                        ? 'text-accent-foreground'
                                        : 'text-foreground/70'
                                "
                            />
                            Profile
                        </Link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <Link
                            :href="route('password.edit')"
                            :class="[
                                active
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-foreground',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <Lock
                                class="mr-2 h-4 w-4"
                                :class="
                                    active
                                        ? 'text-accent-foreground'
                                        : 'text-foreground/70'
                                "
                            />
                            Change Password
                        </Link>
                    </MenuItem>
                    <div class="my-1 h-px bg-border" />
                    <MenuItem v-slot="{ active }">
                        <button
                            @click="logout"
                            :class="[
                                active
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-foreground',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <LogOut
                                class="mr-2 h-4 w-4"
                                :class="
                                    active
                                        ? 'text-accent-foreground'
                                        : 'text-foreground/70'
                                "
                            />
                            Log Out
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>

<script setup lang="ts">
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { Link, router } from '@inertiajs/vue3';
import {
    ChevronDown,
    Lock,
    LogOut,
    ShoppingBag,
    ShoppingCart,
    User,
    UserCircle,
} from 'lucide-vue-next';
import { route } from 'ziggy-js';

interface Customer {
    id: number;
    name: string;
    email: string;
}

interface Props {
    customer: Customer;
    cartItemsCount: number;
    store: {
        slug: string;
    };
}

const props = defineProps<Props>();

const logout = () => {
    router.post(route('customer.logout', { storeSlug: props.store.slug }));
};
</script>
