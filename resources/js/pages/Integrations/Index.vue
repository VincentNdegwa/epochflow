<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { CreditCard, Check, ExternalLink } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';

interface Integration {
    id: string;
    name: string;
    description: string;
    icon: string;
    enabled: boolean;
    configured: boolean;
    settings: Record<string, string>;
}

interface IntegrationCategory {
    id: string;
    name: string;
    description: string;
    icon: string;
    integrations: Integration[];
}

const props = defineProps<{
    categories: IntegrationCategory[];
}>();

const breadcrumbs = computed(
    () =>
        [
            { title: 'Integrations' },
        ] as BreadcrumbItem[],
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Integrations" />

        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold">Integrations</h1>
                <div class="text-sm text-muted-foreground">
                    Configure and manage your store integrations
                </div>
            </div>

        <!-- Categories -->
            <!-- Integration Categories -->
            <div class="grid gap-8">
                <section v-for="category in categories" :key="category.id" class="rounded-lg bg-card">
                    <div class="border-b p-6">
                        <div class="flex items-center gap-3">
                            <CreditCard v-if="category.icon === 'credit-card'" class="h-6 w-6 text-primary" />
                            <h2 class="text-xl font-semibold">{{ category.name }}</h2>
                        </div>
                        <p class="mt-1 text-sm text-muted-foreground">{{ category.description }}</p>
                    </div>

                    <div class="divide-y">
                        <div
                            v-for="integration in category.integrations"
                            :key="integration.id"
                            class="p-6"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0">
                                            <img
                                                v-if="integration.icon === 'stripe'"
                                                src="/images/stripe_logo.webp"
                                                alt="Stripe"
                                                class="h-8"
                                            />
                                            <img
                                                v-else-if="integration.icon === 'paypal'"
                                                src="/images/paypal_logo.png"
                                                alt="PayPal"
                                                class="h-8"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-lg font-semibold">{{ integration.name }}</h3>
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                                                    :class="{
                                                        'bg-green-500/10 text-green-500': integration.configured,
                                                        'bg-gray-500/10 text-gray-500': !integration.configured
                                                    }"
                                                >
                                                    <Check v-if="integration.configured" class="mr-1 h-3 w-3" />
                                                    {{ integration.configured ? 'Configured' : 'Not Configured' }}
                                                </span>
                                            </div>
                                            <p class="mt-1 text-sm text-muted-foreground">{{ integration.description }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex items-center justify-between">
                                        <button

                                            @click="router.visit(route('integrations.payments.configure', { provider: integration.id }))"
                                            class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
                                        >
                                            Configure Settings
                                            <ExternalLink class="h-4 w-4" />
                                        </button>

                                        <!-- Toggle Switch -->
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm text-muted-foreground">{{ integration.enabled ? 'Enabled' : 'Disabled' }}</span>
                                            <button
                                                type="button"
                                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                                :class="integration.enabled ? 'bg-primary' : 'bg-gray-200'"
                                                role="switch"
                                                :aria-checked="integration.enabled"
                                                @click="$emit('toggle', integration)"
                                            >
                                                <span
                                                    class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                    :class="integration.enabled ? 'translate-x-5' : 'translate-x-0'"
                                                />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.text-primary {
    color: var(--color-primary);
}
.bg-primary {
    background-color: var(--color-primary);
}
.ring-primary {
    --tw-ring-color: var(--color-primary);
}
</style>