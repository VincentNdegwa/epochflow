<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted } from 'vue';

const props = defineProps<{
    paypalUrl: string;
}>();

onMounted(() => {
    const script = document.createElement('script');
    script.async = true;
    script.src = 'https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js';
    document.head.appendChild(script);


    window.addEventListener('message', handlePayPalMessage);
});


const handlePayPalMessage = (event: MessageEvent) => {
    
}
</script>

<template>
    <AppLayout>
        <Head title="PayPal Onboarding" />

        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col items-center justify-center">
                <h1 class="text-2xl font-semibold mb-6">Connect PayPal Account</h1>
                <p class="text-muted-foreground mb-8">Click the button below to connect your PayPal account</p>
                
                <a 
                    :href="paypalUrl" 
                    data-paypal-button="true" 
                    target="PPFrame"
                    class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90"
                >
                    Sign up for PayPal
                </a>
            </div>
        </div>
    </AppLayout>
</template>