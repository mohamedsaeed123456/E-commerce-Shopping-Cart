<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    cartItems: {
        type: Array,
        required: true,
    },
});

const quantityInputs = ref({});

// Initialize quantity inputs
props.cartItems.forEach(item => {
    quantityInputs.value[item.id] = item.quantity;
});

const updateQuantity = (cartItemId) => {
    const quantity = quantityInputs.value[cartItemId];
    
    router.put(route('cart.update', cartItemId), {
        quantity: parseInt(quantity),
    }, {
        preserveScroll: true,
        onError: () => {
            // Reset to original quantity on error
            const item = props.cartItems.find(i => i.id === cartItemId);
            if (item) {
                quantityInputs.value[cartItemId] = item.quantity;
            }
        },
    });
};

const removeItem = (cartItemId) => {
    if (confirm('Are you sure you want to remove this item?')) {
        router.delete(route('cart.remove', cartItemId), {
            preserveScroll: true,
        });
    }
};

const checkout = () => {
    if (confirm('Are you sure you want to proceed with checkout?')) {
        router.post(route('cart.checkout'), {}, {
            preserveScroll: true,
        });
    }
};

const total = computed(() => {
    return props.cartItems.reduce((sum, item) => {
        return sum + (parseFloat(item.product.price) * item.quantity);
    }, 0);
});
</script>

<template>
    <Head title="Shopping Cart" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Shopping Cart
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">
                        {{ $page.props.flash.success }}
                    </p>
                </div>

                <div v-if="Object.keys($page.props.errors || {}).length > 0" class="mb-4 rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">
                        {{ Object.values($page.props.errors)[0] }}
                    </p>
                </div>

                <div v-if="cartItems.length === 0" class="rounded-lg bg-white p-6 text-center shadow">
                    <p class="text-gray-500">Your cart is empty.</p>
                    <Link
                        :href="route('products.index')"
                        class="mt-4 inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                    >
                        Browse Products
                    </Link>
                </div>

                <div v-else class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="space-y-4">
                            <div
                                v-for="item in cartItems"
                                :key="item.id"
                                class="flex items-center justify-between border-b border-gray-200 pb-4 last:border-b-0 last:pb-0"
                            >
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ item.product.name }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        ${{ parseFloat(item.product.price).toFixed(2) }} each
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Stock: {{ item.product.stock_quantity }}
                                    </p>
                                </div>

                                <div class="mx-4 flex items-center gap-2">
                                    <label class="text-sm text-gray-600">Qty:</label>
                                    <input
                                        v-model.number="quantityInputs[item.id]"
                                        type="number"
                                        min="1"
                                        :max="item.product.stock_quantity"
                                        @change="updateQuantity(item.id)"
                                        class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">
                                        ${{ (parseFloat(item.product.price) * item.quantity).toFixed(2) }}
                                    </p>
                                </div>

                                <div class="ml-4">
                                    <DangerButton
                                        @click="removeItem(item.id)"
                                        type="button"
                                    >
                                        Remove
                                    </DangerButton>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-xl font-bold text-gray-900">Total:</span>
                                <span class="text-2xl font-bold text-gray-900">
                                    ${{ total.toFixed(2) }}
                                </span>
                            </div>
                            <div class="flex justify-end">
                                <PrimaryButton
                                    @click="checkout"
                                    class="px-6 py-3"
                                >
                                    Checkout
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
