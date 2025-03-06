<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';

import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { loadStripe } from '@stripe/stripe-js';
import { ref } from 'vue';

const cardEl = ref(null);
const clientSecret = usePage().props.str_intent.client_secret;
const stripe = loadStripe(usePage().props.cashier_key);
const cardName = ref(null);
let cardElement = '';

stripe.then((instStripe) => {
    const elements = instStripe.elements({
        clientSecret: clientSecret,
        appearance: {
            rules: {
                '.Input': {
                    colorText: '#fff',
                    backgroundColor: 'white',
                },
            },
        },
    });
    cardElement = elements.create('card', {
        style: {
            base: {
                iconColor: '#c4f0ff',
                color: '#A3A3A3',
                fontSmoothing: 'antialiased',
                ':-webkit-autofill': {
                    color: '#A3A3A3',
                },
                '::placeholder': {
                    color: '#A3A3A3',
                },
            },
        },
    });
    cardElement.mount(cardEl.value);
});

const charge = () => {
    stripe.then(async (stripe) => {
        const { setupIntent, error } = await stripe.confirmCardSetup(clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: cardName.value,
                },
            },
        });
        if (error) {
            console.log(error);
            // document.querySelector('div#message').classList.remove('hidden');
            // document.querySelector('span#message-feedback').textContent = error.message;
        } else {
            router.post(route('checkout.charge'), {
                _token: usePage().props.csrf_token,
                paymentMethod: setupIntent.payment_method,
            });
        }
    });
};
</script>

<template>
    <AuthLayout title="Inicie Sua Assinatura" description="Assine para ter acesso aos nossos conteúdos!">
        <Head title="Checkout - Assinatura" />
        <Label>Nome Cartão</Label>
        <Input placeholder="Nome conforme seu cartão..." v-model="cardName" />
        <div id="card" ref="cardEl" class="mb-10 w-full rounded border border-gray-600 px-6 py-3"></div>
        <button
            @click="charge"
            class="rounded border border-green-900 bg-green-700 px-4 py-2 font-thin text-white transition duration-300 ease-in-out hover:bg-green-900"
        >
            Assinar
        </button>
    </AuthLayout>
</template>
