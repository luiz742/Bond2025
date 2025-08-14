<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue' // <-- IMPORTANTE

const props = defineProps({
    user: Object,
    clients: Object,
})

const form = useForm({})
const search = ref('')

// Controle do modal
const showDeleteModal = ref(false)
const clientToDelete = ref({ id: null, name: '' })


const filteredClients = computed(() => {
    if (!search.value) return props.clients.data
    const term = search.value.toLowerCase()
    return props.clients.data.filter(client =>
        (client.name && client.name.toLowerCase().includes(term)) ||
        (client.code_reference && client.code_reference.toLowerCase().includes(term))
    )
})

const confirmDelete = (client) => {
    clientToDelete.value = {
        id: client.id,
        name: client.name
    }
    showDeleteModal.value = true
}

const deleteClient = () => {

    if (!clientToDelete.value.id) {
        alert('ID inválido para exclusão.')
        return
    }

    form.delete(route('admin.clients.mydestroy', clientToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            showDeleteModal.value = false
        }
    })
}
</script>

<template>
    <AdminLayout :title="`Clients`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                My Clients
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                    <div class="mb-4 flex justify-between items-center">
                        <PrimaryButton as="span">
                            <Link :href="route('admin.myclients.create')" class="block w-full h-full">
                            Add New Client
                            </Link>
                        </PrimaryButton>

                        <input v-model="search" type="text" placeholder="Search Client"
                            class="border rounded px-3 py-1 dark:bg-gray-700 dark:text-gray-200" />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Code Reference
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Client Name
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Created At
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="client in filteredClients" :key="client.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{
                                        client.code_reference
                                        }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ client.name }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ new
                                        Date(client.created_at).toLocaleString() }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <Link :href="`/admin/clients/${client.id}`"
                                            class="text-blue-600 hover:underline">
                                        View
                                        </Link> /
                                        <Link :href="`/admin/clients/${client.id}/edit`"
                                            class="text-yellow-600 hover:underline">
                                        Edit</Link> /
                                        <button @click="confirmDelete(client)" class="text-red-600 hover:underline"
                                            :disabled="form.processing">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <button v-if="clients.prev_page_url" @click="router.visit(clients.prev_page_url)"
                            class="text-sm text-gray-600 dark:text-gray-300">
                            ← Previous
                        </button>
                        <button v-if="clients.next_page_url" @click="router.visit(clients.next_page_url)"
                            class="text-sm text-gray-600 dark:text-gray-300">
                            Next →
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <ConfirmDeleteModal :show="showDeleteModal" :itemName="clientToDelete.name" @cancel="showDeleteModal = false"
            @confirm="deleteClient" />
    </AdminLayout>
</template>
