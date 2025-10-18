<template>
    <Menu as="div" class="relative">
        <MenuButton
            class="flex items-center gap-2 rounded-full bg-background p-2 text-sm font-medium hover:bg-accent"
        >
            <UserCircle class="h-6 w-6" />
            <span class="hidden md:inline">{{ user.name }}</span>
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
                            href="/dashboard"
                            :class="[
                                active
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-foreground',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <LayoutDashboard
                                class="mr-2 h-4 w-4"
                                :class="
                                    active
                                        ? 'text-accent-foreground'
                                        : 'text-foreground/70'
                                "
                            />
                            Dashboard
                        </Link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <Link
                            href="/products"
                            :class="[
                                active
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-foreground',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <Package
                                class="mr-2 h-4 w-4"
                                :class="
                                    active
                                        ? 'text-accent-foreground'
                                        : 'text-foreground/70'
                                "
                            />
                            My Products
                        </Link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <Link
                            href="/orders"
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
                            href="/settings"
                            :class="[
                                active
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-foreground',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                            ]"
                        >
                            <Settings
                                class="mr-2 h-4 w-4"
                                :class="
                                    active
                                        ? 'text-accent-foreground'
                                        : 'text-foreground/70'
                                "
                            />
                            Settings
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

<script lang="ts" setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { Link, router } from '@inertiajs/vue3';
import {
    ChevronDown,
    LayoutDashboard,
    LogOut,
    Package,
    Settings,
    ShoppingBag,
    UserCircle,
} from 'lucide-vue-next';

defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const logout = () => {
    router.post('/logout');
};
</script>
