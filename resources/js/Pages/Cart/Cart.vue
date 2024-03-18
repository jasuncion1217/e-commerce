<script setup>
import WelcomeLayout from '@/Layouts/WelcomeLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.css';
import { useToast } from 'vue-toastification';

const toast = useToast();
const props = defineProps({
  cartItems: Object,
  flash: Object,
});

const getPostImageUrl = (imageName) => {
  return imageName ? `/storage/${imageName}` : 'https://hinacreates.com/wp-content/uploads/2021/06/dummy2-450x341.png';
};

const formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'php',
});

const removeItem = (cartId) => {
  Swal.fire({
  title: "Are you sure you want to remove this item from your cart?",
  text: "You won't be able to revert this!",
  icon: "warning",
  showCancelButton: true,
  confirmButtonColor: "#3085d6",
  cancelButtonColor: "#d33",
  confirmButtonText: "Yes, delete it!"
}).then((result) => {
  if (result.isConfirmed) {
    router.delete(`/Cart/` + cartId, {
                onSuccess: () => {
                    if (props.flash.successMessage) {
                        toast.success(props.flash.successMessage);
                    } else {
                        toast.error(props.flash.errorMessage);
                    }
                },
            });
  }
});
}

const sum = (input) => {
  if (!Array.isArray(input)) return false;
  let total = 0;
  input.forEach(item => {
    if (!isNaN(item.total_price)) {
      total += parseFloat(item.total_price);
    }
  });
  return total;
}

// onMounted(() => {

// });
</script>
<template>

  <Head title="Cart" />
  <WelcomeLayout>
    <div class="bg-gray-100">
      <h1 class="mb-10 text-center text-2xl font-bold">Cart Items</h1>
      <div class="mx-auto max-w-5xl justify-center px-6 md:flex md:space-x-6 xl:px-0">
        <div class="rounded-lg md:w-2/3">
          <div v-if="props.cartItems.length > 0">
          <div v-for="cart in cartItems" :key="cart.cart_id" class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:flex sm:justify-start">
            <img
              :src="getPostImageUrl(cart.product_img)"
              alt="product-image" class="w-full rounded-lg sm:w-40" />
            <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between">
              <div class="mt-5 sm:mt-0">
                <h2 class="text-lg font-bold text-gray-900">{{ cart.product_name }}</h2>
              </div>
              <div class="mt-4 flex-col justify-center sm:space-y-6 sm:mt-0 sm:block sm:space-x-6">
                <div class="flex justify-start sm:justify-end border-gray-100 mb-2">
                  <input class="h-12 w-12 border bg-white text-center text-xs outline-none" type="number" :value="cart.quantity"
                    min="1" disabled/>
                </div>
                <div class="flex-col justify-end">
                  <p class="text-sm">{{ formatter.format(cart.total_price) }}</p>
                  <button type="button" @click="removeItem(cart.cart_id)" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-2 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Remove item</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else>
          <div class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:flex sm:justify-start">
            <img
              src="https://www.seensil.com/assets/images/cart-empty.jpg"
              alt="product-image" class="w-full rounded-lg sm:w-40" />
            <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between">
              <div class="mt-5 sm:mt-0">
                <h2 class="text-lg font-bold text-gray-900">Your cart is empty</h2>
              </div>
              <div class="mt-4 flex-col justify-center sm:space-y-6 sm:mt-0 sm:block sm:space-x-6">
                <div class="flex justify-start sm:justify-end border-gray-100 mb-2">
                  <input class="h-12 w-12 border bg-white text-center text-xs outline-none" type="number"
                    min="1" disabled/>
                </div>
                <div class="flex-col justify-end">
                  <p class="text-sm"></p>
                  <Link :href="route('welcome.index')" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Fill up your cart</Link>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
        <!-- Sub total -->
        <div class="mt-6 h-full rounded-lg border bg-white p-6 shadow-md md:mt-0 md:w-1/3">
          <div class="flex justify-between">
            <p class="text-lg font-bold">Total</p>
            <div class="">
              <p class="mb-1 text-lg font-bold">{{ formatter.format(sum(props.cartItems)) }}</p>
            </div>
          </div>
          <button class="mt-6 w-full rounded-md bg-blue-500 py-1.5 font-medium text-blue-50 hover:bg-blue-600">Check
            out</button>
        </div>
      </div>
    </div>
  </WelcomeLayout>
</template>