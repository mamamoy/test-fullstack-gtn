<script setup>
import { onMounted } from 'vue'
import { initFlowbite } from 'flowbite'
import Header from './Header.vue'

// initialize components based on data attribute selectors
onMounted(() => {
    initFlowbite();
})

</script>

<template>
    <!-- Navbar start -->
    <Header></Header>
    <!-- Navbar end -->

    <!-- Main content start -->

    <slot />
    <!-- Main content end -->

    <!-- Footer start -->
    <Footer></Footer>
    <!-- Footer end -->
</template>