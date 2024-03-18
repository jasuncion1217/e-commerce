<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Form, Field } from 'vee-validate';
import { useToast } from 'vue-toastification';
import WelcomeLayout from '@/Layouts/WelcomeLayout.vue';

const toast = useToast();
const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    auth: Object,
    products: Object,
    flash: Object,
    cart: String,
    errorMessage: String,
    errors: Object,
});

const getPostImageUrl = (imageName) => {
    return imageName ? `/storage/${imageName}` : 'https://hinacreates.com/wp-content/uploads/2021/06/dummy2-450x341.png';
};

const searchTerm = ref('');
const getProductId = ref('');
const maxQuantity = ref('');
const selectedProductName = ref('');

const showCartModal = (productId, productQuantity, productName) => {
    getProductId.value = productId;
    selectedProductName.value = productName;
    maxQuantity.value = productQuantity;
}

const closeCartModal = () => {
    addtoCartForm.reset();
}

const search = () => {
    router.get('/', {
        searchTerm: searchTerm.value,
    }, {
        preserveScroll: true,
        preserveState: true,
    });
}

const addtoCartForm = useForm({
    product_quantity: null,
});

const addtoCart = (productId) => {
    addtoCartForm.post(route('cart.store', {
        product_id: productId
    }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                    toast.success(props.flash.successMessage);
                    addtoCartForm.reset();
            },
            onError: () => {
                if(props.errors.error){
                    toast.error(props.errors.error);
                } else {
                    toast.error('Something went wrong');
                }        
            }
        });
}

const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'php',
});

// onMounted (()=>{
//     console.log(props.products.links[0])
// })
</script>

<template>

    <Head title="Welcome" />
    <WelcomeLayout>
        <div
            class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-center selection:bg-red-500 selection:text-white">
            <div>
                <div class="d-flex">
                    <div class="d-flex ml-auto">
                        <div class="d-flex">
                            <input name="search" type="text" class="form-control mb-2" v-model="searchTerm"
                                placeholder="Search..." @keyup="search" />
                        </div>
                    </div>
                </div>
                <div v-if="products.data.length > 0">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div v-for="product in products.data" :key="product.product_id">
                            <div class="max-w-sm rounded overflow-hidden shadow-lg">
                                <img class="img w-full" :src="getPostImageUrl(product.product_img)"
                                    alt="Sunset in the mountains">
                                <div class="flex-col">
                                    <div class="px-6 py-4">
                                        <div class="font-bold text-xl mb-2">{{ product.product_name }}</div>
                                        <p class="text-gray-700 text-base">
                                        <div class=" text-xl mb-2"><span class="font-bold">Available:
                                            </span>
                                            {{ product.product_quantity }}
                                        </div>
                                        <div class=" text-xl mb-2"><span class="font-bold">Price:
                                            </span>
                                            {{ formatter.format(product.product_price) }}
                                        </div>
                                        </p>
                                    </div>
                                    <div class="px-6 mb-2">
                                        <button @click="showCartModal(product.product_id, product.product_quantity, product.product_name)"
                                            type="button"
                                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm p-2 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                            data-bs-toggle="modal" data-bs-target="#cartModal">
                                            Add to cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <div class="max-w-sm rounded overflow-hidden shadow-lg">
                                <img class="img w-full"
                                    src="https://cdn.dribbble.com/users/3512533/screenshots/14168376/media/1357b33cb4057ecb3c6f869fc977561d.jpg?resize=400x300&vertical=center"
                                    alt="No results">
                                <div class="px-6 py-4">
                                    <div class="font-bold text-xl mb-2">No Result</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="max-w-sm rounded overflow-hidden shadow-lg">
                                <img class="img w-full"
                                    src="https://cdn.dribbble.com/users/3512533/screenshots/14168376/media/1357b33cb4057ecb3c6f869fc977561d.jpg?resize=400x300&vertical=center"
                                    alt="No results">
                                <div class="px-6 py-4">
                                    <div class="font-bold text-xl mb-2">No Result</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="max-w-sm rounded overflow-hidden shadow-lg">
                                <img class="img w-full"
                                    src="https://cdn.dribbble.com/users/3512533/screenshots/14168376/media/1357b33cb4057ecb3c6f869fc977561d.jpg?resize=400x300&vertical=center"
                                    alt="No results">
                                <div class="px-6 py-4">
                                    <div class="font-bold text-xl mb-2">No Result</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-center mt-2">
            <nav aria-label="Page navigation example justify-center">
                <ul class="inline-flex -space-x-px text-sm">
                    <li v-for="link in products.links">
                        <Link :href="link.url" v-if="link.url"
                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-gray-200 border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                        {{ link.label }}</Link>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Add to cart</h1>
                        <button @click="closeCartModal()" type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <Form @submit="addtoCart(getProductId)">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">How many <b>{{ selectedProductName }}</b>'s would you like to
                                    order?</label>
                                <Field name="productQuantity" v-model="addtoCartForm.product_quantity"
                                    :max="maxQuantity" min="0" type="number" class="form-control"
                                    :class="{ 'is-invalid': errors.product_quantity }" />
                                <span class="invalid-feedback">{{ errors.product_quantity }}</span>
                            </div>
                            <div class="modal-footer">
                                <button type="button" @click="closeCartModal()" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add to cart</button>
                            </div>
                        </Form>
                    </div>
                </div>
            </div>
        </div>
    </WelcomeLayout>
</template>
<style>
.img {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
    width: 500px;
    height: 300px;
}

.img:hover {
    box-shadow: 0 0 2px 1px rgba(0, 140, 186, 0.5);
}
</style>
