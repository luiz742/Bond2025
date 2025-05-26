<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

defineProps({
    user: Object,
    clients: Object,
})
</script>

<template>
    <AdminLayout :title="`Clients`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Clients
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-4">
                        <div class="mb-4">
                            <PrimaryButton as="span">
                                <Link :href="route('admin.clients.create')" class="block w-full h-full text-white">
                                New Client
                                </Link>
                            </PrimaryButton>
                        </div>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Name</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Service
                                </th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Subagent
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
                            <tr v-for="client in clients.data" :key="client.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ client.name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ client.service.name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ client.user.name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ new
                                    Date(client.created_at).toLocaleDateString() }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <Link :href="`/clients/${client.id}`" class="text-blue-600 hover:underline">
                                    View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>

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
    </AdminLayout>
</template>
