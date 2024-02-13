<template>
    <AdminLayout>
        <SalesList :users="props"></SalesList>
        <SalesReports :data="props.salesReports"></SalesReports>

    </AdminLayout>
</template>

<script setup>
import AdminLayout from './Components/AdminLayout.vue';
import SalesList from './SalesList.vue'
import SalesReports from './SalesReports.vue';

const props = defineProps({
    users: Array,
    sellingPackets: Array,
    salesReports: Array
})
</script>