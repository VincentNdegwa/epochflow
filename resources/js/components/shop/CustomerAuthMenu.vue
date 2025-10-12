<template>
    <div>
        <CustomerMenu v-if="customer" :customer="customer" :cart-items-count="cartItemsCount" :store="store" />

        <template v-else>
            <Menu as="div" class="relative inline-block text-left">
                <MenuButton
                    class="inline-flex items-center gap-2 rounded-md bg-background/50 px-3 py-2 text-sm hover:bg-background">
                    <User class="h-5 w-5" />
                    <span class="hidden sm:inline">Account</span>
                    <ChevronDown class="h-4 w-4" />
                </MenuButton>

                <transition enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                    <MenuItems
                        class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-card shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                        <div class="px-1 py-1">
                            <MenuItem v-slot="{ active }">
                            <Link :href="route('customer.login', { storeSlug: store.slug })" :class="[
                                active ? 'bg-accent text-accent-foreground' : 'text-foreground',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm'
                            ]">
                            <LogIn class="mr-2 h-4 w-4"
                                :class="active ? 'text-accent-foreground' : 'text-foreground/70'" />
                            Login
                            </Link>
                            </MenuItem>

                            <MenuItem v-slot="{ active }">
                            <Link :href="route('customer.register', { storeSlug: store.slug })" :class="[
                                active ? 'bg-accent text-accent-foreground' : 'text-foreground',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm'
                            ]">
                            <UserPlus class="mr-2 h-4 w-4"
                                :class="active ? 'text-accent-foreground' : 'text-foreground/70'" />
                            Register
                            </Link>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>
        </template>
    </div>
</template>

<script setup lang="ts">
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import CustomerMenu from './CustomerMenu.vue'
import { User, UserPlus, LogIn, ChevronDown } from 'lucide-vue-next'

interface Props {
    customer: any | null
    cartItemsCount: number
    store: { slug: string }
}

const props = defineProps<Props>()

const { customer, cartItemsCount, store } = props
</script>
