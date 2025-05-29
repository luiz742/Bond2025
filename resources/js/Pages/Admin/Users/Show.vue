<script setup>
import { ref } from 'vue'
import { router, useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    user: Object,
    clients: Object,
    services: Object,
})

const showModal = ref(false)

const form = useForm({
    name: '',
    service_id: '',
    user_id: props.user.id,
})

const openModal = () => {
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    form.reset()
}

const submit = () => {
    form.post(route('admin.clients.user.store', { user: props.user.id }), {
        onSuccess: () => {
            closeModal()
            router.reload()
        },
        onError: (errors) => {
            console.error(errors)
        },
    })
}

// Criando o form do Inertia.js (não há campos visíveis pois é para DELETE)
const deleteForm = useForm({})

// Função de exclusão usando useForm
const deleteClient = (id) => {
    if (confirm('Delete Client?')) {
        deleteForm.delete(route('admin.users.client.destroy', { client: id }), {
            preserveScroll: true,
            onSuccess: () => router.reload(),
        })
    }
}
</script>

<template>
    <AdminLayout :title="`Clients of ${user.name}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Clients of {{ user.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                    <!-- Botão que abre o modal -->
                    <PrimaryButton @click="openModal" class="mb-4">
                        Add New Client
                    </PrimaryButton>

                    <!-- Modal customizado com transição e estilo -->
                    <transition name="fade">
                        <div v-if="showModal"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm"
                            @click="closeModal" aria-modal="true" role="dialog" aria-labelledby="modal-title">
                            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full p-6 mx-4 relative"
                                @click.stop>
                                <h3 id="modal-title"
                                    class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                    New Client for {{ user.name }}
                                </h3>

                                <form @submit.prevent="submit" novalidate class="space-y-4">
                                    <div>
                                        <label for="name"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Name
                                        </label>
                                        <input id="name" v-model="form.name" type="text" required class="mt-1 w-full p-2 border border-gray-300 rounded-md dark:bg-gray-800 dark:text-white
                        focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" />
                                    </div>
                                    <div>
                                        <label for="service_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Service
                                        </label>
                                        <select id="service_id" v-model="form.service_id" class="mt-1 w-full p-2 border border-gray-300 rounded-md dark:bg-gray-800 dark:text-white
                        focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                            <option value="">Select a Service</option>
                                            <option v-for="service in services" :key="service.id" :value="service.id">
                                                {{ service.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="flex justify-end space-x-3">
                                        <button type="button" @click="closeModal" class="px-4 py-1.5 rounded-md bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600
                        text-gray-700 dark:text-gray-200 text-sm font-medium transition">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-1.5 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition"
                                            :disabled="form.processing">
                                            Submit
                                        </button>
                                    </div>
                                </form>

                                <!-- Close Button Top Right -->
                                <button @click="closeModal" aria-label="Close modal"
                                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </transition>

                    <!-- Tabela com clients -->
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">ID
                                </th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Name</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Created At
                                </th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="client in clients.data" :key="client.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ client.id }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ client.name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                                    {{ new Date(client.created_at).toLocaleString() }}
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <Link :href="`/admin/clients/${client.id}`" class="text-blue-600 hover:underline">
                                    View
                                    </Link>/
                                    <button @click="deleteClient(client.id)" class="text-red-600 hover:underline"
                                        :disabled="form.processing">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Paginação -->
                    <div class="mt-4 flex justify-between items-center">
                        <button v-if="clients.prev_page_url" @click="router.visit(clients.prev_page_url)"
                            class="text-sm text-gray-600 dark:text-gray-300">← Previous</button>
                        <button v-if="clients.next_page_url" @click="router.visit(clients.next_page_url)"
                            class="text-sm text-gray-600 dark:text-gray-300">Next →</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
