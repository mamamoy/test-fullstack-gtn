<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { reactive } from 'vue';
import { computed } from 'vue';
import { ref } from 'vue';
import { jsPDF } from 'jspdf'
import 'jspdf-autotable'

const props = defineProps({
    users: Array
})


// const users = usePage().props.users

const isAddSales = ref(false)
const dialogVisible = ref(false)
const editMode = ref(false)

// customers data
const id = ref('')
const name = ref('')
const email = ref('')
const nip = ref('')
const isAdmin = ref('')
const password = ref('')
const confirm_password = ref('')
// end

const searchTable = ref('')

// Search data by input search
const filteredUsers = computed(() => {
    if (!searchTable.value) {
        return props.users.users;
    } else {
        const query = searchTable.value.toLowerCase();
        return props.users.users.filter(user => user.name.toLowerCase().includes(query) || user.NIP.includes(query));
    }
})
// end

// Pagination for table
const pagination = reactive({
    currentPage: 1,
    perPage: 10,
    get totalPages() {
        return Math.ceil(filteredUsers.value.length / this.perPage);
    }
})

// Data show by paginate
const paginatedUsers = computed(() => {
    const startIndex = (pagination.currentPage - 1) * pagination.perPage
    const endIndex = startIndex + pagination.perPage
    return filteredUsers.value.slice(startIndex, endIndex)
})
// end



// open add modal
const openAddModal = () => {
    isAddSales.value = true
    dialogVisible.value = true
    editMode.value = false
}
// end

// reset form data
const resetFormData = () => {
    id.value = ''
    name.value = ''
    password.value = ''
    email.value = ''
    isAdmin.value = ''
    nip.value = ''
    confirm_password.value = ''
}


// Add new sales
const addSales = async () => {
    const formData = new FormData()
    formData.append('name', name.value)
    formData.append('email', email.value)
    formData.append('password', password.value)
    formData.append('isAdmin', 0)

    try {
        await router.post('sales/store', formData, {
            onSuccess: page => {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: false,
                    title: page.props.flash.success
                })

                dialogVisible.value = false
                resetFormData()
            }
        })


    } catch (errors) {
        console.log(errors)
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Sales has not been added, try again!'
        })
        dialogVisible.value = false
        resetFormData()
    }
}
// End

// Matching password
const validatePassword = () => {
    if (password.value == confirm_password.value) {
        addSales()
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Your password is not match, try again!'
        })
        dialogVisible.value = false
        resetFormData()

    }
}
// end
// end

// handle close dialog modal
const handleClose = (done) => {
    resetFormData()
    done()
}
// end


// open edit modal
const openEditModal = (user) => {
    editMode.value = true
    isAddSales.value = false
    dialogVisible.value = true

    // update data
    id.value = user.id
    name.value = user.name
    email.value = user.email
    password.value = user.password
    isAdmin.value = user.isAdmin

}
// end

// Update user information
const updateSales = async () => {
    const formData = new FormData
    formData.append('name', name.value)
    formData.append('email', email.value)
    formData.append('_method', 'PUT')

    try {
        await router.post('sales/update/' + id.value, formData, {
            onSuccess: (page) => {
                dialogVisible.value = false
                resetFormData()
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
        dialogVisible.value = false
        resetFormData()
    }
}
// end

// delete selected sales with customers relation with
const deleteSales = (user) => {
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
                router.delete('sales/destroy/' + user.id, {
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

// download function
const download = async () => {
    try {
        const response = await fetch('export', {
            method: 'GET',
        });
        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(new Blob([blob]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'report.pdf');

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            Swal.fire({
                toast: true,
                icon: 'success',
                position: 'top-end',
                showConfirmButton: false,
                title: 'PDF downloaded successfully'
            });
        } else {
            Swal.fire({
                toast: true,
                icon: 'error',
                position: 'top-end',
                showConfirmButton: false,
                title: 'Failed to download PDF'
            });
        }
    } catch (error) {
        console.log(error);
        Swal.fire({
            toast: true,
            icon: 'error',
            position: 'top-end',
            showConfirmButton: false,
            title: 'An error occurred while downloading PDF'
        });
    }
};

// 



</script>

<template>
    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <!-- Start coding here -->
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">
                        <h3 class="font-medium text-gray-900">List of Sales</h3>
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
                    <!-- Dialog for Form Add/Edit Sales -->
                    <el-dialog v-model="dialogVisible" :title="editMode ? 'Edit sales' : 'Add new sales'" width="500"
                        :before-close:="handleClose">


                        <form @submit.prevent="editMode ? updateSales() : validatePassword()" class="max-w-md mx-auto">
                            <div class="relative z-0 w-full mb-5 grou">
                                <input type="text" v-model="name" name="name" id="name"
                                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " required />
                                <label for="name"
                                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Sales
                                    Name</label>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">

                                <input type="email" v-model="email" name="email" id="email"
                                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " required />
                                <label for="email"
                                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Email
                                    address</label>
                            </div>
                            <div v-if="!editMode">
                                <div class="relative z-0 w-full mb-5 group">
                                    <input type="password" v-model="password" name="password" id="password"
                                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                        placeholder=" " required />
                                    <label for="password"
                                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Password</label>
                                </div>
                                <div class="relative z-0 w-full mb-5 group">
                                    <input type="password" v-model="confirm_password" name="confirm_password"
                                        id="confirm_password"
                                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                        placeholder=" " required />
                                    <label for="confirm_password"
                                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Confirm
                                        password</label>
                                </div>
                            </div>
                            <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
                        </form>

                    </el-dialog>
                    <!-- End dialog -->
                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <button @click="openAddModal" type="button"
                            class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>

                            Add New Sales
                        </button>
                        <button @click="download" type="button"
                            class="text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800"><svg
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg></button>

                    </div>
                </div>
                <div id="exportable" class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">#</th>
                                <th scope="col" class="px-4 py-3">Sales Name</th>
                                <th scope="col" class="px-4 py-3">Employee ID</th>
                                <th scope="col" class="px-4 py-3">Customer Handle</th>
                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in paginatedUsers" :key="user.id"
                                class="border-b dark:border-gray-700">
                                <td class="px-4 py-3">{{ (pagination.currentPage - 1) * pagination.perPage + index + 1 }}
                                </td>
                                <th scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ user.name }}</th>
                                <td class="px-4 py-3">{{ user.NIP }}</td>
                                <td class="px-4 py-3">{{ user.sales_reports.length }}</td>
                                <td class="px-4 py-3 flex items-center justify-end">
                                    <div class="flex items-center gap-3">

                                        <button type="button" @click="openEditModal(user)"
                                            class="text-yellow-400 border border-yellow-400 hover:bg-yellow-400 hover:text-white focus:ring-4 focus:outline-none focus:ring-yellow-100 font-medium rounded-full text-sm p-1 text-center inline-flex items-center dark:border-yellow-200 dark:text-yellow-200 dark:hover:text-white dark:focus:ring-yellow-500 dark:hover:bg-yellow-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>

                                        </button>
                                        <button type="button" @click="deleteSales(user)"
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