<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    user: Object,
    clients: Object,
})

const search = ref('')

const filteredClients = computed(() => {
  if (!search.value) return props.clients.data
  const term = search.value.toLowerCase()
  return props.clients.data.filter(client =>
    (client.name && client.name.toLowerCase().includes(term)) ||
    (client.code_reference && client.code_reference.toLowerCase().includes(term))
  )
})
</script>

<template>
    <AppLayout :title="`Clients of ${user.name}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Clients of {{ user.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                    <div class="mb-4 flex justify-between items-center">
                        <PrimaryButton as="span">
                            <Link :href="`/clients/create`" class="block w-full h-full text-white">
                                Add New Client
                            </Link>
                        </PrimaryButton>

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search Client"
                            class="border rounded px-3 py-1 dark:bg-gray-700 dark:text-gray-200"
                        />
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Code Reference
                                </th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Client Name
                                </th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Service
                                </th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Created At
                                </th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="client in filteredClients" :key="client.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ client.code_reference }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ client.name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ client.service.name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ new Date(client.created_at).toLocaleString() }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <Link :href="`/clients/${client.id}`" class="text-blue-600 hover:underline">
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4 flex justify-between items-center">
                        <button v-if="clients.prev_page_url" @click="router.visit(clients.prev_page_url)" class="text-sm text-gray-600 dark:text-gray-300">
                            ← Previous
                        </button>
                        <button v-if="clients.next_page_url" @click="router.visit(clients.next_page_url)" class="text-sm text-gray-600 dark:text-gray-300">
                            Next →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
