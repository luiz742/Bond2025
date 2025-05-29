<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, usePage, useForm, router } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const page = usePage()

defineProps({
    users: Object,
    name: String
})

// Criando o form do Inertia.js (não há campos visíveis pois é para DELETE)
const form = useForm({})

// Função de exclusão usando useForm
const deleteUser = (id) => {
    if (confirm('Tem certeza que deseja deletar este usuário?')) {
        form.delete(`/admin/users/${id}`, {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <AdminLayout title="Users">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">User List</h3>

                        <PrimaryButton as="span">
                            <Link v-if="page.url === '/admin/users'" href="/admin/users/create?type=admin"
                                class="block w-full h-full">
                            Add New User
                            </Link>
                            <Link v-else href="/admin/subagents/create?type=b2b" class="block w-full h-full">
                            Add New Subagent
                            </Link>
                        </PrimaryButton>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Created At
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="user in users.data" :key="user.id">
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ user.id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ user.name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ user.email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ new Date(user.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-4 py-2 text-sm space-x-2">
                                        <Link :href="`/admin/users/${user.id}`" class="text-blue-600 hover:underline">
                                        View
                                        </Link> /
                                        <Link :href="route('admin.users.edit', user.id)"
                                            class="text-blue-600 hover:underline">
                                        Edit
                                        </Link> /
                                        <button @click="deleteUser(user.id)" class="text-red-600 hover:underline"
                                            :disabled="form.processing">
                                            Delete
                                        </button>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="users.data.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
                        No users found.
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
