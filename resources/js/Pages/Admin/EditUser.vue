<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.css';
import { useToast } from "vue-toastification";
import { Form, Field } from 'vee-validate';
import NavLink from '@/Components/NavLink.vue';

const toast = useToast();

const props = defineProps({
    user: Object,
    flash: Object,
    errors: Object,
});


const editForm = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    confirm_password: '',
});

const updateUser = (userId) => {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to update this user details',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            editForm.put(route('user.update', { id: userId }), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    toast.success(props.flash.successMessage);
                },
                onError: () => {
                    toast.error('Something went wrong');
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
                    <div class="container-fluid">
                        <div class="grid grid-cols-2">
                            <div class="col-span-2 sm:col-span-1">
                                <h1> User details </h1>
                                <Form ref="form" @submit="updateUser(user.id)">
                                    <div class="mb-3">
                                        <label for="category" class="col-form-label">User Name:</label>
                                        <Field name="userName" type="text" id="disabledTextInput"
                                            class="form-control" :class="{ 'is-invalid': errors.name }"
                                            v-model="editForm.name" />
                                        <span v-if="props.errors.name" class="invalid-feedback">{{
                                    props.errors.name }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category" class="col-form-label">User email:</label>
                                        <Field name="userEmail" step="0.01" type="email" id="disabledTextInput"
                                            class="form-control" :class="{ 'is-invalid': errors.email }"
                                            v-model="editForm.email" />
                                        <span v-if="props.errors.email" class="invalid-feedback">{{
                                    props.errors.email }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category" class="col-form-label">User password:</label>
                                        <Field name="userPass" type="password" id="disabledTextInput"
                                            class="form-control" :class="{ 'is-invalid': errors.password }"
                                            v-model="editForm.password" />
                                        <span v-if="props.errors.password" class="invalid-feedback">{{
                                            props.errors.password }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category" class="col-form-label">Confirm user password:</label>
                                        <Field name="userConfirmPass" type="password" id="disabledTextInput"
                                            class="form-control" :class="{ 'is-invalid': errors.confirm_password }"
                                            v-model="editForm.confirm_password" />
                                        <span v-if="props.errors.confirm_password" class="invalid-feedback">{{
                                            props.errors.confirm_password }}</span>
                                    </div>
                                    <div class="footer mr-auto mb-2">
                                        <button type="submit" class="btn btn-primary">Update User</button>
                                        <NavLink :href="route('users.index')" class="btn btn-danger ml-2">
                                            Back
                                        </NavLink>
                                    </div>
                                </Form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
