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
          <table
            class="w-full rounded-lg table-bordered text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-gray-700 bg-gray-400 uppercase dark:text-gray-400">
              <tr>
                <th scope="col" class="px-6 py-3 text-gray-900">
                  Product Image
                </th>
                <th scope="col" class="px-6 py-3 text-gray-900">
                  Product Name
                </th>
                <th scope="col" class="px-6 py-3 text-gray-900">
                  Quantity
                </th>
                <th scope="col" class="px-6 py-3 text-gray-900">
                  Price
                </th>
                <th scope="col" class="px-6 py-3 text-gray-900">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in props.products.data" :key="product.product_id"
                class="border-b border-gray-200 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                  <img :src="getPostImageUrl(product.product_img)" class="img" alt="Product Image">
                </th>
                <td class="px-6 py-4 text-gray-900">
                  {{ product.product_name }}
                </td>
                <td class="px-6 py-4 text-gray-900">
                  {{ product.product_quantity }}
                </td>
                <td class="px-6 py-4 text-gray-900">
                  {{ formatter.format(product.product_price) }}
                </td>
                <td class="px-6 py-4 text-gray-900">
                  <div class="flex flex-column">
                    <Link type="button" :href="route('products.show', { product_id: product.product_id })"
                      class="text-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-2.5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    View
                    </Link>
                    <button type="button" @click="deleteProduct(product.product_id, product.product_name)"
                      class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-2.5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

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