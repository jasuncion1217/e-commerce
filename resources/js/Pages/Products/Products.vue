<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Form, Field } from 'vee-validate';
import { onMounted } from 'vue';
import { useToast } from "vue-toastification";
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.css';

const toast = useToast();

// onMounted(() => {
//   Swal.fire(props.products);
// })


const props = defineProps({
  products: Object,
  flash: Object,
  errors: Object,
});

const formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'php',
});

const MAX_FILE_SIZE = 102400;

const validFileExtensions = { image: ['jpg', 'gif', 'png', 'jpeg', 'svg', 'webp'] };

function isValidFileType(fileName, fileType) {
  return fileName && validFileExtensions[fileType].indexOf(fileName.split('.').pop()) > -1;
}

const addProductForm = useForm({
  product_img: null,
  product_name: null,
  product_quantity: null,
  product_price: null,
});

const addProduct = () => {
  addProductForm.post(route('products.store'),
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success(props.flash.successMessage);
        addProductForm.reset();
        selectedImageUrl.value = 'https://hinacreates.com/wp-content/uploads/2021/06/dummy2-450x341.png';
        $('#addProduct').modal('hide');
      },
      onError: () => {
        toast.error('Something went wrong');
        selectedImageUrl.value = 'https://hinacreates.com/wp-content/uploads/2021/06/dummy2-450x341.png';
      },
    });
}


const getPostImageUrl = (imageName) => {
  return imageName ? `/storage/${imageName}` : 'https://hinacreates.com/wp-content/uploads/2021/06/dummy2-450x341.png';
};

const selectedImageUrl = ref('https://hinacreates.com/wp-content/uploads/2021/06/dummy2-450x341.png');

const displaySelectedImage = (event) => {
  const fileInput = event.target;

  if (fileInput.files && fileInput.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      // Assuming you want to display the selected image URL
      selectedImageUrl.value = e.target?.result;

    };

    reader.readAsDataURL(fileInput.files[0]);
  }
};

const deleteProduct = (productId, productName) => {
  Swal.fire({
    title: "Are you sure you want to delete this product '" + productName + "' ?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!"
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete('/products/' + productId, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          toast.success('Product:' + productName + ' has been deleted successfully');
        },
        onError: () => {
          toast.error('Something went wrong');
        },
      });
    }
  });
}

const searchTerm = ref('');

const search = () => {
  router.get('/Products/Products', {
    searchTerm: searchTerm.value,
  }, {
    preserveScroll: true,
    preserveState: true,
  });
}

const closeAddModal = () => {
  addProductForm.reset();
  selectedImageUrl.value = 'https://hinacreates.com/wp-content/uploads/2021/06/dummy2-450x341.png';
}
</script>

<template>

  <Head title="Products" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="m-auto font-semibold text-xl text-white leading-tight">Products</h2>
    </template>

    <div class="p-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="d-flex">
          <button type="button"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
            data-bs-toggle="modal" data-bs-target="#addProduct">
            Add product
          </button>

          <div class="d-flex ml-auto">
            <Form ref="form">
              <div class="d-flex">
                <Field name="search" type="text" class="form-control mb-2" v-model="searchTerm" placeholder="Search..."
                  @keyup="search" />
              </div>
            </Form>
          </div>
        </div>

        <div class="bg-white p-2 overflow-hidden shadow-sm sm:rounded-lg">
          <div v-if="props.products.data.length > 0">
            <div v-for="product in products.data" :key="product.product_id"
              class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:flex sm:justify-start">
              <img :src="getPostImageUrl(product.product_img)" alt="product-image" class="w-full rounded-lg sm:w-40" />
              <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between">
                <div class="mt-5 sm:mt-0">
                  <h2 class="text-lg font-bold text-gray-900">{{ product.product_name }}</h2>
                </div>
                <div class="mt-4 flex-col justify-center sm:space-y-6 sm:mt-0 sm:block sm:space-x-6">
                  <div class="flex-col justify-end">
                    <p class="text-sm"><span class="font-bold">Product Quantity: </span>{{ product.product_quantity }}
                    </p>
                    <p class="text-sm"><span class="font-bold">Product Price: </span>{{
                  formatter.format(product.product_price) }}</p>
                    <div class="grid grid-cols-2 gap-2">
                      <Link :href="route('products.show', { product_id: product.product_id })"
                        class="text-white text-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                      Edit</Link>
                      <button type="button" @click="deleteProduct(product.product_id, product.product_name)"
                        class="focus:outline-none text-center text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-2 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Delete
                        item</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else>
            <div class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:flex sm:justify-start">
              <img src="data:image/png;base64,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" alt="product-image"
                class="w-full rounded-lg sm:w-40" />
              <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between">
                <div class="mt-5 sm:mt-0">
                  <h2 class="text-lg font-bold text-gray-900">No products found</h2>
                </div>
                <div class="mt-4 flex-col justify-center sm:space-y-6 sm:mt-0 sm:block sm:space-x-6">
                </div>
              </div>
            </div>
          </div>

          <div class="p-f flex justify-end">
            <nav aria-label="Page navigation example">
              <ul class="pagination">
                <li v-if="products.prev_page_url" class="page-item">
                  <Link class="page-link" :href="products.prev_page_url">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-arrow-left-square-fill inline-block" viewBox="0 0 16 16">
                    <path
                      d="M16 14a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2zm-4.5-6.5H5.707l2.147-2.146a.5.5 0 1 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L5.707 8.5H11.5a.5.5 0 0 0 0-1" />
                  </svg> Previous</Link>
                </li>
                <li v-if="products.next_page_url" class="page-item">
                  <Link class="page-link" :href="products.next_page_url">
                  Next
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-arrow-right-square-fill inline-block" viewBox="0 0 16 16">
                    <path
                      d="M0 14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2zm4.5-6.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5a.5.5 0 0 1 0-1" />
                  </svg></Link>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="addProduct" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Add Product</h1>
            <button type="button" @click="closeAddModal()" class="btn-close" data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <Form @submit="addProduct()">
              <div class="mb-3">
                <div class="flex justify-items-center">
                  <img id="selectedImage" :src="selectedImageUrl" alt="example placeholder" class="h-20" />
                </div>
                <label for="exampleInputEmail1" class="form-label">Product Image</label>
                <Field name="productImage" v-model="addProductForm.product_img" type="file"
                  @input="addProductForm.product_img = $event.target.files[0]" @change="displaySelectedImage"
                  class="form-control" :class="{ 'is-invalid': errors.product_img }" />
                <span class="invalid-feedback">{{ errors.product_img }}</span>
              </div>
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Product Name</label>
                <Field name="productName" v-model="addProductForm.product_name" type="text" class="form-control"
                  :class="{ 'is-invalid': errors.product_name }" />
                <span class="invalid-feedback">{{ errors.product_name }}</span>
              </div>
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Product Price (₱)</label>
                <Field name="productPrice" id="productPrice" pattern="\d*" v-model="addProductForm.product_price"
                  type="number" class="form-control" :class="{ 'is-invalid': errors.product_price }" />
                <span class="invalid-feedback">{{ errors.product_price }}</span>
              </div>
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Product Quantity</label>
                <Field name="productQuantity" v-model="addProductForm.product_quantity" type="number"
                  class="form-control" :class="{ 'is-invalid': errors.product_quantity }" />
                <span class="invalid-feedback">{{ errors.product_quantity }}</span>
              </div>
              <div class="modal-footer">
                <button type="button" @click="closeAddModal()" class="btn btn-secondary"
                  data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </Form>
          </div>
        </div>
      </div>
    </div>

  </AuthenticatedLayout>
</template>
<style>
.img {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
  width: 150px;
  height: 150px;
}

.img:hover {
  box-shadow: 0 0 2px 1px rgba(0, 140, 186, 0.5);
}
</style>