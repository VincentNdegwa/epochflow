<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head, router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

declare global {
    function route(name: string, params?: Record<string, any>): string;
}

interface Props {
    store: {
        name: string;
        slug: string;
    };
    status?: string;
}

defineProps<Props>();
</script>

<template>
    <AuthBase
        :title="`Create your ${store.name} account`"
        :description="'Fill in the form below to create your customer account'"
    >
        <Head :title="`Sign up - ${store.name}`" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <Form
            :action="route('customer.register.attempt', { storeSlug: store.slug })"
            method="post"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Full Name</Label>
                    <Input
                        id="name"
                        type="text"
                        name="name"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        placeholder="John Doe"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        placeholder="Create a password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm Password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Phone Number (Optional)</Label>
                    <Input
                        id="phone"
                        type="tel"
                        name="phone"
                        :tabindex="5"
                        autocomplete="tel"
                        placeholder="+1 (555) 123-4567"
                    />
                    <InputError :message="errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="address">Delivery Address (Optional)</Label>
                    <Input
                        id="address"
                        type="text"
                        name="address"
                        :tabindex="6"
                        autocomplete="street-address"
                        placeholder="123 Main St, City, Country"
                    />
                    <InputError :message="errors.address" />
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="7"
                    :disabled="processing"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Create Account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink 
                    :href="route('customer.login', { storeSlug: store.slug })" 
                    :tabindex="8"
                >
                    Log in
                </TextLink>
            </div>
        </Form>
    </AuthBase>
</template>