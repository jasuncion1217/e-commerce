<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    cartItems: Number,
    products: Number,
    revenue: String,
    users: Number,
    lineGraphData: Object,
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

onMounted(() => {
    renderLineGraph();
    console.log(props.lineGraphData.data);
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
                        <div class="flex col-span-3 sm:col-span-1 items-center bg-white border rounded-sm overflow-hidden shadow">
                            <div class="p-4 bg-green-400"><svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg></div>
                            <div class="px-4 text-gray-700">
                                <h3 class="text-sm tracking-wider">Total Users</h3>
                                <p class="text-3xl">{{ props.users }}</p>
                            </div>
                        </div>
                        <div class="flex col-span-3 sm:col-span-1 items-center bg-white border rounded-sm overflow-hidden shadow">
                            <div class="p-4 bg-blue-400"><svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2">
                                    </path>
                                </svg></div>
                            <div class="px-4 text-gray-700">
                                <h3 class="text-sm tracking-wider">Total Orders</h3>
                                <p class="text-3xl">{{ props.cartItems }}</p>
                            </div>
                        </div>
                        <div class="flex col-span-3 sm:col-span-1 items-center bg-white border rounded-sm overflow-hidden shadow">
                            <div class="p-4 bg-indigo-400"><svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                                    </path>
                                </svg></div>
                            <div class="px-4 text-gray-700">
                                <h3 class="text-sm tracking-wider">Revenue</h3>
                                <p class="text-3xl">{{ formatter.format(props.revenue) }}</p>
                            </div>
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
