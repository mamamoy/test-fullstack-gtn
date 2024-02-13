<script setup>
import { Plus } from '@element-plus/icons-vue'
import { router, usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { reactive } from 'vue';
import { computed } from 'vue';
import { ref } from 'vue';

const props = defineProps({
    data: Object,
})

const isAddSalesReport = ref(false)
const dialogSalesReportVisible = ref(false)
const editSalesReportMode = ref(false)

// sales report data
const id = ref('')
const reportCount = props.data.salesReports.length
const sales_report_id = ref(`Report-${String(reportCount + 1).padStart(5, '0')}`)
const customerId = ref('')
const packetId = ref('')
const id_images = ref([])
const home_images = ref([])

const idPicture = ref([])
const dialogIdPictureURL = ref('')
const homePicture = ref([])
const dialogHomePictureURL = ref('')
// end

// handle ID Picture
const handleidPicture = (file) => {
    idPicture.value.push(file)
}

const handleidPicturePreview = (file) => {
    dialogIdPictureURL.value = file.url
    dialogSalesReportVisible.value = true
}

const handleidPictureRemove = (file) => {
    console.log(file)
}

// end

// Handle homePicture

const handlehomePicture = (file) => {
    homePicture.value.push(file)
}

const handlehomePicturePreview = (file) => {
    dialogHomePictureURL.value = file.url
    dialogSalesReportVisible.value = true
}

const handlehomePictureRemove = (file) => {
    console.log(file)
}

const searchTable = ref('')

// Search data by input search
const filteredReports = computed(() => {
    if (!searchTable.value) {
        return props.data.salesReports;
    } else {
        const query = searchTable.value.toLowerCase();
        return props.data.salesReports.filter(report => report.customer.customer_name.toLowerCase().includes(query) || report.sales_report_id.includes(query) || report.customer.telephone_number.includes(query))
    }
})
// end

// Pagination for table
const pagination = reactive({
    currentPage: 1,
    perPage: 10,
    get totalPages() {
        return Math.ceil(filteredReports.value.length / this.perPage);
    }
})

// Data show by paginate
const paginatedReports = computed(() => {
    const startIndex = (pagination.currentPage - 1) * pagination.perPage
    const endIndex = startIndex + pagination.perPage
    return filteredReports.value.slice(startIndex, endIndex)
})
// end


// open add sales report modal
const openAddSalesReportModal = () => {
    isAddSalesReport.value = true
    dialogSalesReportVisible.value = true
    editSalesReportMode.value = false
}
// end


// reset form sales report data
const resetFormSalesReport = () => {
    customerId.value = ''
    packetId.value = ''
    idPicture.value = []
    id_images.value = []
    dialogIdPictureURL = ''
    homePicture.value = []
    home_images.value = []
    dialogHomePictureURL = ''
}
// end





// Add new sales report
const addSalesReport = async () => {
    const formData = new FormData()
    formData.append('customerId', customerId.value)
    formData.append('packetId', packetId.value)

    // append id images to formData
    for (const idImage of idPicture.value) {
        formData.append('id_images[]', idImage.raw)
    }
    // end
    // append home images to formData
    for (const homeImage of homePicture.value) {
        formData.append('home_images[]', homeImage.raw)
    }
    // end


    try {
        await router.post('/sales-report/store', formData, {
            onSuccess: page => {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: false,
                    title: page.props.flash.success
                })

                dialogSalesReportVisible.value = false
                resetFormSalesReport()
            }
        })


    } catch (errors) {
        console.log(errors)
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Sales has not been added, try again!'
        })
        dialogSalesReportVisible.value = false
    }
}
// End



// handle close dialog modal
const handleSalesReportClose = (done) => {
    resetFormSalesReport()
    done()
}
// end


// open edit modal
const openEditModal = (report) => {
    editSalesReportMode.value = true
    isAddSalesReport.value = false
    dialogSalesReportVisible.value = true

    // update data
    id.value = report.id
    sales_report_id.value = report.sales_report_id
    customerId.value = report.customer_id
    packetId.value = report.selling_packet_id
    id_images.value = report.id_images
    home_images.value = report.home_images

}
// end

// delete selected ID image
const deleteIDImage = async (image, index) => {
    try {
        await router.delete('/sales-report/id-image/' + image.id, {
            onSuccess: (page) => {
                idPicture.value.splice(index, 1)
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: false,
                    title: page.props.flash.success
                })
            }
        })
    } catch (err) {
        console.log(err)
    }
}
// end
// delete selected ID image
const deleteHomeImage = async (image, index) => {
    try {
        await router.delete('/sales-report/home-image/' + image.id, {
            onSuccess: (page) => {
                homePicture.value.splice(index, 1)
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: false,
                    title: page.props.flash.success
                })
            }
        })
    } catch (err) {
        console.log(err)
    }
}
// end

// Update user information
const updateSalesReport = async () => {
    const formData = new FormData
    formData.append('customerId', customerId.value)
    formData.append('packetId', packetId.value)
    formData.append('_method', 'PUT')

    // append id images to formData
    for (const idImage of idPicture.value) {
        formData.append('id_images[]', idImage.raw)
    }
    // end
    // append home images to formData
    for (const homeImage of homePicture.value) {
        formData.append('home_images[]', homeImage.raw)
    }
    // end

    try {
        await router.post('sales-report/update/' + id.value, formData, {
            onSuccess: (page) => {
                dialogSalesReportVisible.value = false
                resetFormSalesReport()
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: false,
                    title: page.props.flash.success
                })
            }
        })
    } catch (errors) {
        console.log(errors)
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Sales has not been updated, try again!'
        })
        dialogSalesReportVisible.value = false
        resetFormSalesReport()
    }
}
// end

// delete selected sales with customers relation with
const deleteReport = (report) => {
    Swal.fire({
        title: 'Are you sure ?',
        text: 'Sales will be deleted permanently',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            try {
                router.delete('sales-report/destroy/' + report.id, {
                    onSuccess: (page) => {
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            position: 'top-end',
                            showConfirmButton: false,
                            title: page.props.flash.success
                        })
                    }
                })
            } catch (error) {
                console.log(error)
            }
        }
    })
}
// end

</script>

<template>
    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <!-- Start coding here -->
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">
                        <h3 class=" text-gray-900 font-medium">List of Sales Reported</h3>
                        <form class="flex items-center">
                            <label for="simple-search" class="sr-only">Search</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                        fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="simple-search" v-model="searchTable"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Search" required="">
                            </div>
                        </form>
                    </div>
                    <!-- Dialog for Form Add/Edit Sales Report -->
                    <el-dialog v-model="dialogSalesReportVisible"
                        :title="editSalesReportMode ? 'Edit sales' : 'Add new sales'" width="500"
                        :before-close:="handleSalesReportClose">


                        <form @submit.prevent="editSalesReportMode ? updateSalesReport() : addSalesReport()"
                            class="max-w-md mx-auto">
                            <div class="relative z-0 w-full mb-5 group">
                                <input type="text" v-model="sales_report_id" name="sales_report_id" id="sales_report_id"
                                    class=" block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " readonly />
                                <label for="sales_report_id"
                                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Report
                                    ID</label>
                            </div>
                            <div class="relative mb-5 z-0 w-full group">
                                <div class="mb-2">
                                    <h3 class="text-sm text-gray-900">Customer Name</h3>
                                </div>
                                <div>
                                    <el-select v-model="customerId" placeholder="Select customer">
                                        <el-option v-for="item in props.data.customers" :key="item.id"
                                            :label="item.customer_name" :value="item.id">
                                            <span style="float: left">{{ item.customer_name }}</span>
                                            <span
                                                style="float: right;color: var(--el-text-color-secondary);font-size: 13px;">{{
                                                    item.telephone_number }}</span>
                                        </el-option>
                                    </el-select>
                                </div>
                            </div>
                            <div class="relative mb-5 z-0 w-full group">
                                <div class="mb-2">
                                    <h3 class="text-sm text-gray-900">Selling Packet</h3>
                                </div>
                                <div>
                                    <el-select v-model="packetId" placeholder="Select selling packet">
                                        <el-option v-for="item in props.data.sellingPackets" :key="item.id"
                                            :label="item.packet_name" :value="item.id">
                                            <span style="float: left">{{ item.packet_name }}</span>
                                            <span
                                                style="float: right;color: var(--el-text-color-secondary);font-size: 13px;">Rp.
                                                {{ item.packet_price }}</span>
                                        </el-option>
                                    </el-select>
                                </div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <div class="mb-2">
                                    <h3 class="text-sm text-gray-900">Identification Number</h3>
                                </div>
                                <div>
                                    <el-upload v-model:file-list="idPicture" list-type="picture-card" multiple
                                        :on-preview="handleidPicturePreview" :on-remove="handleidPictureRemove"
                                        :on-change="handleidPicture" id="identification_number">
                                        <el-icon>
                                            <Plus />
                                        </el-icon>
                                    </el-upload>
                                </div>
                            </div>
                            <div class="flex flex-nowrap mb-8">
                                <div v-for="(image, index) in id_images" :key="image.id" class="relative w-32 h-32">
                                    <img class="w-24 h-24 rounded" :src="`/${image.identification_id_image}`" alt="">
                                    <span
                                        class="absolute top-0 right-8 transform -translate-y-1/2 w-3.5 h-3.5 bg-red-400 border-2 border-white dark:border-gray-800 rounded-full">
                                        <span @click="deleteIDImage(image, index)"
                                            class="text-white text-xs font-bold absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">x</span>
                                    </span>
                                </div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <div class="mb-2">
                                    <h3 class="text-sm text-gray-900">Home Picture</h3>
                                </div>
                                <div>
                                    <el-upload v-model:file-list="homePicture" list-type="picture-card" multiple
                                        :on-preview="handlehomePicturePreview" :on-remove="handlehomePictureRemove"
                                        :on-change="handlehomePicture" id="homePicture">
                                        <el-icon>
                                            <Plus />
                                        </el-icon>
                                    </el-upload>
                                </div>
                            </div>
                            <div class="flex flex-nowrap mb-8">
                                <div v-for="(image, index) in home_images" :key="image.id" class="relative w-32 h-32">
                                    <img class="w-24 h-24 rounded" :src="`/${image.home_image}`" alt="">
                                    <span
                                        class="absolute top-0 right-8 transform -translate-y-1/2 w-3.5 h-3.5 bg-red-400 border-2 border-white dark:border-gray-800 rounded-full">
                                        <span @click="deleteHomeImage(image, index)"
                                            class="text-white text-xs font-bold absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">x</span>
                                    </span>
                                </div>
                            </div>
                            <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
                        </form>

                    </el-dialog>
                    <!-- End dialog -->

                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <button @click="openAddSalesReportModal" type="button"
                            class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>

                            Add New Sales Report
                        </button>
                    </div>

                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">#</th>
                                <th scope="col" class="px-4 py-3">Customer Name</th>
                                <th scope="col" class="px-4 py-3">Report ID</th>
                                <th scope="col" class="px-4 py-3">Created At</th>
                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(report, index) in paginatedReports" :key="report.id"
                                class="border-b dark:border-gray-700">
                                <td class="px-4 py-3">{{ (pagination.currentPage - 1) * pagination.perPage + index + 1 }}
                                </td>
                                <th scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ report.customer.customer_name }}</th>
                                <td class="px-4 py-3">{{ report.sales_report_id }}</td>
                                <td class="px-4 py-3">{{ report.created_at }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="openEditModal(report)"
                                            class="text-yellow-400 border border-yellow-400 hover:bg-yellow-400 hover:text-white focus:ring-4 focus:outline-none focus:ring-yellow-100 font-medium rounded-full text-sm p-1 text-center inline-flex items-center dark:border-yellow-200 dark:text-yellow-200 dark:hover:text-white dark:focus:ring-yellow-500 dark:hover:bg-yellow-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>

                                        </button>
                                        <button type="button" @click="deleteReport(report)"
                                            class="text-red-700 border border-red-700 hover:bg-red-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm p-1 text-center inline-flex items-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800 dark:hover:bg-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>


                                        </button>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
                    aria-label="Table navigation">
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">

                    </span>
                    <ul class="inline-flex items-stretch -space-x-px">
                        <li>
                            <button :disabled="pagination.currentPage <= 1" @click="pagination.currentPage--"
                                class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                <span class="sr-only">Previous</span>
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </li>
                        <li>
                            <button disabled
                                class=" flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Page
                                {{ pagination.currentPage }} of {{ pagination.totalPages }}</button>
                        </li>
                        <li>
                            <button :disabled="pagination.currentPage >= pagination.totalPages"
                                @click="pagination.currentPage++"
                                class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                <span class="sr-only">Next</span>
                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>
</template>