<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Form, Field } from 'vee-validate';
import { useToast } from 'vue-toastification';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.css';

const toast = useToast();
const props = defineProps({
    users: Object,
    errors: Object,
    flash: Object,
    auth: Object,
});

const searchTerm = ref('');

const search = () => {
    router.get('/users/users', {
        searchTerm: searchTerm.value,
    }, {
        preserveScroll: true,
        preserveState: true,
    });
}

const addUserForm = useForm({
    name: '',
    email: '',
    password: '',
    confirm_password: '',
});

const addUser = () => {
    addUserForm.post(route('users.store'),
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success(props.flash.successMessage);
                addUserForm.reset();
            },
            onError: () => {
                toast.error(props.flash.successMessage);
            },
        });
};

const deleteUser = (id, name) => {
    Swal.fire({
        title: "Are you sure you want to delete this user '" + name + "' ?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete('/users/user/' + id, {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success(props.flash.successMessage);
                },
                onError: () => {
                    toast.error(props.flash.errorMessage);
                },
            });
        }
    });
};
</script>
<template>

    <Head title="User" />

    <!-- component -->
    <AuthenticatedLayout>
        <template #header>
            <h2 class="m-auto font-semibold text-xl text-white leading-tight">Users</h2>
        </template>
        <div class="p-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="d-flex">
                    <button type="button"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                        data-bs-toggle="modal" data-bs-target="#addProduct">
                        Add User
                    </button>

                    <div class="d-flex ml-auto">
                        <Form ref="form">
                            <div class="d-flex">
                                <Field name="search" type="text" class="form-control mb-2" v-model="searchTerm"
                                    placeholder="Search..." @keyup="search" />
                            </div>
                        </Form>
                    </div>
                </div>

                <div class="bg-white p-2 overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="props.users.data.length > 0">
                        <div v-for="user in users.data" :key="users.id"
                            class="justify-between mb-6 rounded-lg bg-white p-2 shadow-md sm:flex sm:justify-start">
                            <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between">
                                <div class="mt-5 sm:mt-0">
                                    <p class="text-lg text-gray-900"><span class="font-bold">User name: </span>{{
                                    user.name }}
                                    </p>
                                    <p class="text-sm text-gray-500"><span class="font-bold text-gray-900">User email:
                                        </span>{{
                                    user.email }}</p>
                                    <p class="text-sm text-gray-500" v-for="(role, index) in user.roles" :key="index">
                                        <span class="font-bold text-gray-900">User role: </span>{{ role.name }}
                                    </p>
                                </div>
                                <div class="mt-4 flex-col justify-center sm:space-y-6 sm:mt-0 sm:block sm:space-x-6">
                                    <div class="flex-col justify-end" v-if="user.id != auth.user.id">
                                        <div class="grid gap-2 mt-4 grid-rows-2">
                                            <Link :href="route('user.get', { id: user.id })"
                                                class="text-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                            Edit
                                            User</Link>
                                            <button @click="deleteUser(user.id, user.name)" type="button"
                                                class="focus:outline-none text-center text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-2 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Delete
                                                User</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="justify-between mb-6 rounded-lg bg-white p-6 shadow-md sm:flex sm:justify-start">
                            <img src="https://i0.wp.com/digitalhealthskills.com/wp-content/uploads/2022/11/3da39-no-user-image-icon-27.png?fit=500%2C500&ssl=1"
                                alt="product-image" class="w-full rounded-lg sm:w-40" />
                            <div class="sm:ml-4 sm:flex sm:w-full sm:justify-between">
                                <div class="mt-5 sm:mt-0">
                                    <h2 class="text-lg font-bold text-gray-900">No users found</h2>
                                </div>
                                <div class="mt-4 flex-col justify-center sm:space-y-6 sm:mt-0 sm:block sm:space-x-6">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-f flex justify-end">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <li v-if="users.prev_page_url" class="page-item">
                                    <Link class="page-link" :href="users.prev_page_url">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-arrow-left-square-fill inline-block" viewBox="0 0 16 16">
                                        <path
                                            d="M16 14a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2zm-4.5-6.5H5.707l2.147-2.146a.5.5 0 1 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L5.707 8.5H11.5a.5.5 0 0 0 0-1" />
                                    </svg> Previous</Link>
                                </li>
                                <li v-if="users.next_page_url" class="page-item">
                                    <Link class="page-link" :href="users.next_page_url">
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
                        <Form @submit="addUser()">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">User Name</label>
                                <Field name="userName" v-model="addUserForm.name" type="text" class="form-control"
                                    :class="{ 'is-invalid': errors.name }" />
                                <span class="invalid-feedback">{{ errors.name }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">User Email</label>
                                <Field name="userEmail" id="productPrice" pattern="\d*" v-model="addUserForm.email"
                                    type="email" class="form-control" :class="{ 'is-invalid': errors.email }" />
                                <span class="invalid-feedback">{{ errors.email }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">User Password</label>
                                <Field name="userPass" v-model="addUserForm.password" type="password"
                                    class="form-control" :class="{ 'is-invalid': errors.password }" />
                                <span class="invalid-feedback">{{ errors.password }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Confirm user Password</label>
                                <Field name="userConfirmPass" v-model="addUserForm.confirm_password" type="password"
                                    class="form-control" :class="{ 'is-invalid': errors.confirm_password }" />
                                <span class="invalid-feedback">{{ errors.confirm_password }}</span>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </Form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>