<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
    products: {
        type: Array,
        required: true,
    },
});

const quantityInputs = ref({});

const addToCart = (productId) => {
    const quantity = quantityInputs.value[productId] || 1;

    router.post(
        route("cart.add"),
        {
            product_id: productId,
            quantity: parseInt(quantity),
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                quantityInputs.value[productId] = 1;
            },
        }
    );
};
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Products
                </h2>
                <Link
                    :href="route('products.create')"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-gray-800 rounded-md border border-transparent transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                >
                    Add Product
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    v-if="$page.props.flash?.success"
                    class="p-4 mb-4 bg-green-50 rounded-md"
                >
                    <p class="text-sm font-medium text-green-800">
                        {{ $page.props.flash.success }}
                    </p>
                </div>

                <div
                    v-if="Object.keys($page.props.errors || {}).length > 0"
                    class="p-4 mb-4 bg-red-50 rounded-md"
                >
                    <p class="text-sm font-medium text-red-800">
                        {{ Object.values($page.props.errors)[0] }}
                    </p>
                </div>

                <div
                    class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="overflow-hidden bg-white rounded-lg shadow-md transition-shadow hover:shadow-lg"
                    >
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ product.name }}
                            </h3>
                            <p class="mt-2 text-2xl font-bold text-gray-900">
                                ${{ parseFloat(product.price).toFixed(2) }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                Stock: {{ product.stock_quantity }}
                            </p>

                            <div class="flex gap-2 items-center mt-4">
                                <input
                                    v-model.number="quantityInputs[product.id]"
                                    type="number"
                                    min="1"
                                    :max="product.stock_quantity"
                                    :placeholder="
                                        quantityInputs[product.id] || 1
                                    "
                                    class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <PrimaryButton
                                    @click="addToCart(product.id)"
                                    :disabled="product.stock_quantity === 0"
                                    class="flex-1"
                                >
                                    {{
                                        product.stock_quantity === 0
                                            ? "Out of Stock"
                                            : "Add to Cart"
                                    }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="products.length === 0"
                    class="p-6 text-center bg-white rounded-lg shadow"
                >
                    <p class="text-gray-500">No products available.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
