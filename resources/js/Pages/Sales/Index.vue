<script setup>
import SalesLayouts from './Components/SalesLayouts.vue'
import CustomerList from './CustomerList.vue'
import SalesReportList from './SalesReportList.vue';

const props = defineProps({
    salesReports: Array,
    sellingPackets: Array,
    customers: Array,

})
</script>

<template>
    <SalesLayouts>
        <div class="bg-white">
            <CustomerList :data="props"></CustomerList>
            <SalesReportList :data="props"></SalesReportList>
        </div>
    </SalesLayouts>
</template>