<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    cartItems: Number,
    products: Number,
    revenue: String,
    users: Number,
    lineGraphData: Object,
    years: Object,
});

const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'php',
});

const data = {
    labels: props.lineGraphData.labels,
    datasets: [{
        label: 'Sales',
        data: props.lineGraphData.data,
        backgroundColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'
        ],
        hoverOffset: 4,
        tension: 0.1
    }]
};

const renderLineGraph = () => {
    const ctx = document.getElementById('myLineGraph');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: data,
        });
    }
};

const selectedYear = ref('');

const updateGraph = () => {
    router.get('/Dashboard', {
        selectedYear: selectedYear.value,
    }, {
        preserveScroll: false,
        preserveState: false,
    });
}

onMounted(() => {
    renderLineGraph();
});
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="m-auto font-semibold text-xl text-white leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="grid grid-cols-1 gap-4 px-4 mt-8 sm:grid-cols-3 sm:px-8">
                        <div
                            class="flex col-span-3 sm:col-span-1 items-center bg-white border rounded-sm overflow-hidden shadow">
                            <div class="p-4 bg-green-400"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                                    <path
                                        d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                                </svg></div>
                            <div class="px-4 text-gray-700">
                                <h3 class="text-sm tracking-wider">Total Users</h3>
                                <p class="text-3xl">{{ props.users }}</p>
                            </div>
                        </div>
                        <div
                            class="flex col-span-3 sm:col-span-1 items-center bg-white border rounded-sm overflow-hidden shadow">
                            <div class="p-4 bg-blue-400"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                                    <path
                                        d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z" />
                                    <path
                                        d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                                </svg></div>
                            <div class="px-4 text-gray-700">
                                <h3 class="text-sm tracking-wider">Total Orders</h3>
                                <p class="text-3xl">{{ props.cartItems }}</p>
                            </div>
                        </div>
                        <div
                            class="flex col-span-3 sm:col-span-1 items-center bg-white border rounded-sm overflow-hidden shadow">
                            <div class="p-4 bg-indigo-400"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" fill="currentColor" class="bi bi-cash" viewBox="0 0 16 16">
                                    <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                                    <path
                                        d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2z" />
                                </svg></div>
                            <div class="px-4 text-gray-700">
                                <h3 class="text-sm tracking-wider">Total Revenue</h3>
                                <p class="text-3xl">{{ formatter.format(props.revenue) }}</p>
                            </div>
                        </div>
                        <div class="cols-span-1">
                            <Label class="font-bold">Select a year:</Label>
                            <select @change="updateGraph()" v-model="selectedYear"
                                class="form-select" aria-label="Floating label disabled select example">
                                <option v-for="year in years" :value="year">{{ year }}</option>
                            </select>
                        </div>
                        <div class="flex col-span-3 items-center bg-white border rounded-sm overflow-hidden shadow">
                            <canvas id="myLineGraph"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
