<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    price: "",
    stock_quantity: "",
});

const submit = () => {
    form.post(route("products.store"), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Add Product" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Add New Product
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Product Name" />

                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="block mt-1 w-full"
                                    required
                                    autofocus
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.name"
                                />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="price" value="Price" />

                                <TextInput
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="block mt-1 w-full"
                                    required
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.price"
                                />
                            </div>

                            <div class="mt-4">
                                <InputLabel
                                    for="stock_quantity"
                                    value="Stock Quantity"
                                />

                                <TextInput
                                    id="stock_quantity"
                                    v-model="form.stock_quantity"
                                    type="number"
                                    min="0"
                                    class="block mt-1 w-full"
                                    required
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.stock_quantity"
                                />
                            </div>

                            <div class="flex gap-4 items-center mt-6">
                                <PrimaryButton :disabled="form.processing">
                                    Create Product
                                </PrimaryButton>

                                <a
                                    :href="route('products.index')"
                                    class="text-sm text-gray-600 underline hover:text-gray-900"
                                >
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
