<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    services: Array
})

// Modal de edição já existente (não muda)
const showEditModal = ref(false)
const selectedService = ref({ id: null, name: '', country: '' })

const openEditModal = (service) => {
    selectedService.value = { ...service }
    showEditModal.value = true
}
const closeEditModal = () => {
    showEditModal.value = false
    selectedService.value = { id: null, name: '', country: '' }
}
const submitEdit = () => {
    router.put(`/admin/services/${selectedService.value.id}`, {
        name: selectedService.value.name,
        country: selectedService.value.country
    }, {
        onSuccess: () => closeEditModal()
    })
}

// Modal de criação novo
const showCreateModal = ref(false)
const form = useForm({
    name: '',
    country: ''
})

const openCreateModal = () => {
    form.reset()
    showCreateModal.value = true
}
const closeCreateModal = () => {
    showCreateModal.value = false
}

const submitCreate = () => {
    form.post(route('admin.services.store'), {
        onSuccess: () => {
            closeCreateModal()
            // Opcional: recarregar a lista (se for via Inertia pode usar router.reload())
            router.reload()
        }
    })
}
</script>

<template>
    <AdminLayout title="Services">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Services</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Service List</h3>

                        <!-- Aqui abre o modal de criação -->
                        <PrimaryButton @click="openCreateModal" type="button">New Service</PrimaryButton>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="service in services" :key="service.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ service.id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ service.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ new Date(service.created_at).toLocaleDateString() }}</td>
                                    <td class="px-4 py-2 space-x-2">
                                        <Link :href="`/admin/services/${service.id}`" class="text-blue-600 hover:underline">View</Link>
                                        <button @click="openEditModal(service)" class="text-yellow-600 hover:underline">Edit</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="services.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
                        No services found.
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de edição (igual ao seu atual) -->
        <transition name="fade">
            <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full p-5 mx-4 relative" @click.stop>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Edit Service</h3>

                    <form @submit.prevent="submitEdit" novalidate>
                        <input v-model="selectedService.name" type="text" placeholder="Service name"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                            required />

                        <input v-model="selectedService.country" type="text" placeholder="Country"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                            required />

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeEditModal"
                                class="px-4 py-1.5 rounded-md bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-1.5 rounded-md bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition">
                                Save
                            </button>
                        </div>
                    </form>

                    <button @click="closeEditModal" aria-label="Close modal"
                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </transition>

        <!-- Modal de criação (novo) -->
        <transition name="fade">
            <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full p-5 mx-4 relative" @click.stop>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">New Service</h3>

                    <form @submit.prevent="submitCreate" novalidate>
                        <input v-model="form.name" type="text" placeholder="Service name"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                            required />

                        <input v-model="form.country" type="text" placeholder="Country"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                            required />

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeCreateModal"
                                class="px-4 py-1.5 rounded-md bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-1.5 rounded-md bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition"
                                :disabled="form.processing">
                                Create
                            </button>
                        </div>
                    </form>

                    <button @click="closeCreateModal" aria-label="Close modal"
                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </transition>
    </AdminLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
