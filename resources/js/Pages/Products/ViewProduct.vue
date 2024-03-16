<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.css';
import { useToast } from "vue-toastification";
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';
import NavLink from '@/Components/NavLink.vue';

const editing = ref(false);
const toast = useToast();

const props = defineProps({
    product: Object,
    flash: Object,
});

const editProductSchema = yup.object({
    productName: yup.string().required('Product name is required'),
    productQuantity: yup.number().required('Product quantity is required'),
    productPrice: yup.string().required('Product price is required'),
});

const formatPrice = () => {
    const formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'PHP',
    });
    const inputValue = parseFloat(editForm.product_price); // Parse the input value to float
    if (!isNaN(inputValue)) { // Check if the input value is a valid number
        // Format the input value and update the v-model
        editForm.product_price = formatter.format(inputValue);
    }
};

const getPostImageUrl = (imageName) => {
    return imageName ? `/storage/${imageName}` : 'https://hinacreates.com/wp-content/uploads/2021/06/dummy2-450x341.png';
};

const editBook = () => {
    editing.value = true;
};

const cancelEdit = () => {
    editing.value = false;
}

const selectedImageUrl = ref('/storage/' + props.product.product_img);
const stringImg = JSON.stringify(selectedImageUrl);

const displaySelectedImage = (event) => {
    const fileInput = event.target;

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            stringImg.value = e.target?.result;

        };

        reader.readAsDataURL(fileInput.files[0]);
    }
};

const editForm = useForm({
    product_name: props.product.product_name,
    product_quantity: props.product.product_quantity,
    product_price: props.product.product_price,
});

const updateProduct = (productId) => {
    editing.value = true;

    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to update this book',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            editForm.put(route('products.update', { product_id: productId }), {
                onSuccess: () => {
                    editing.value = false;
                    toast.success(props.flash.successMessage);
                },
                onError: () => {
                    editing.value = false;
                    toast.error(props.flash.errorMessage);
                }
            });
        }
    });
};
</script>

<template>

    <Head title="View/Edit Product" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">View/Edit Product</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="editing === false" class="container-fluid">
                        <div class="grid grid-cols-2">
                            <div>
                                <h1> Product details </h1>

                                <div class="mb-3">
                                    <label for="category" class="col-form-label"><strong>Product Name:</strong></label>
                                    <span class="d-flex" id="view-book-title">
                                        {{ props.product.product_name }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <label for="category" class="col-form-label"><strong>Product Price:</strong></label>
                                    <span class="d-flex" id="view-book-category">
                                        {{ props.product.product_price }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <label for="category" class="col-form-label"><strong>Product
                                            Quantity:</strong></label>
                                    <span class="d-flex" id="view-book-author">
                                        {{ props.product.product_quantity }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="container-fluid mt-2 justify-items-center">
                                    <img :src="getPostImageUrl(product.product_img)" class="w-100 h-100" />
                                </div>
                            </div>
                            <div class="footer mr-auto mb-2">
                                <div>
                                    <button type="button" @click="editBook()" class="btn btn-primary">Edit
                                        Product</button>
                                    <NavLink :href="route('products.index')" class="btn btn-danger ml-2">
                                        Back
                                    </NavLink>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div v-else class="container-fluid">
                        <div class="grid grid-cols-2">
                            <div>
                                <h1> Product details </h1>
                                <Form ref="form" @submit="updateProduct(product.product_id)"
                                    :validation-schema="editProductSchema" v-slot="{ errors }">

                                    <div class="mb-3">
                                        <label for="category" class="col-form-label">Product Name:</label>
                                        <Field name="productName" type="text" id="disabledTextInput"
                                            class="form-control" :class="{ 'is-invalid': errors.productName }"
                                            v-model="editForm.product_name" />
                                        <span class="invalid-feedback">{{ errors.productName }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category" class="col-form-label">Product Price:</label>
                                        <Field name="productPrice" step="0.01" type="text" id="disabledTextInput"
                                            class="form-control" @change="formatPrice"
                                            :class="{ 'is-invalid': errors.productPrice }"
                                            v-model="editForm.product_price" />
                                        <span class="invalid-feedback">{{ errors.productPrice }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category" class="col-form-label">Product Quantity:</label>
                                        <Field name="productQuantity" type="number" id="disabledTextInput"
                                            class="form-control" :class="{ 'is-invalid': errors.productQuantity }"
                                            v-model="editForm.product_quantity" />
                                        <span class="invalid-feedback">{{ errors.productQuantity }}</span>
                                    </div>
                                    <div class="footer mr-auto mb-2">
                                        <button type="submit" class="btn btn-primary">Update Product</button>
                                        <button type="button" @click.prevent="cancelEdit()" class="btn btn-danger ml-2">
                                            Cancel
                                        </button>
                                    </div>
                                </Form>
                            </div>
                            <div>
                                <div class="container-fluid mt-2 justify-items-center">
                                    <div>
                                        <div class="mb-4 d-flex justify-content-center">
                                            <img id="selectedImage" :src="selectedImageUrl" alt="example placeholder"
                                                style="width: 300px;" />
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <div class="btn btn-primary btn-rounded">
                                                <label class="form-label text-white m-1 hover:cursor-pointer"
                                                    for="customFile1">
                                                    Choose file
                                                </label>
                                                <input type="file" class="form-control d-none" id="customFile1"
                                                    @change="displaySelectedImage" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>