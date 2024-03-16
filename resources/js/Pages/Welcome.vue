<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
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
    products: Object,
});

const getPostImageUrl = (imageName) => {
    return imageName ? `/storage/${imageName}` : 'https://hinacreates.com/wp-content/uploads/2021/06/dummy2-450x341.png';
};

const searchTerm = ref('');

const search = () => {
  router.get('/', {
    searchTerm: searchTerm.value,
  }, {
    preserveScroll: true,
    preserveState: true,
  });

}
</script>

<template>

    <Head title="Welcome" />

    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-center selection:bg-red-500 selection:text-white">
        <nav class="dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <Link href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo">
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Shoe Store</span>
                </Link>
                <div class="flex items-end space-x-3 md:space-x-0 rtl:space-x-reverse">
                    <Link v-if="$page.props.auth.user" :href="route('cart.index')"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor"
                        class="bi bi-archive-fill" viewBox="0 0 16 16">
                        <path
                            d="M12.643 15C13.979 15 15 13.845 15 12.5V5H1v7.5C1 13.845 2.021 15 3.357 15zM5.5 7h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1M.8 1a.8.8 0 0 0-.8.8V3a.8.8 0 0 0 .8.8h14.4A.8.8 0 0 0 16 3V1.8a.8.8 0 0 0-.8-.8z" />
                    </svg>
                    <span class="sr-only">Notifications</span>
                    <div v-if="$page.props.auth.reservationsCount"
                        class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -end-2 dark:border-gray-900">
                        {{ $page.props.auth.reservationsCount }} </div>
                    </Link>
                    <ul class="flex flex-wrap items-center justify-end space-x-4">
                        <div v-if="canLogin">
                            <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                            Dashboard
                            </Link>
                            <template v-else>
                                <Link :href="route('login')"
                                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                                Log in
                                </Link>
                                <span class="text-white">|</span>
                                <Link v-if="canRegister" :href="route('register')"
                                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                                Register
                                </Link>
                            </template>
                        </div>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="max-w-7xl mx-auto p-6 lg:p-8 mt-20">
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
                            <div class="px-6 py-4">
                                <div class="font-bold text-xl mb-2">{{ product.product_name }}</div>
                                <p class="text-gray-700 text-base">
                                <div class=" text-xl mb-2"><span class="font-bold">Available: </span>{{
                            product.product_quantity }}
                                </div>
                                <div class=" text-xl mb-2"><span class="font-bold">Price: </span>{{ product.product_price }}
                                </div>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <div class="max-w-sm rounded overflow-hidden shadow-lg">
                            <img class="img w-full" src="https://cdn.dribbble.com/users/3512533/screenshots/14168376/media/1357b33cb4057ecb3c6f869fc977561d.jpg?resize=400x300&vertical=center"
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
    <div class="flex items-center justify-center">
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
