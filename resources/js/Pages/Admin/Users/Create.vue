<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const page = usePage()
const query = new URLSearchParams(window.location.search)
const type = query.get('type') || 'b2b'


const form = useForm({
    name: '',
    email: '',
    password: '',
    role: type === 'b2b' ? 'b2b' : '',
})

const submit = () => {
    form.post(route('admin.users.store'), {
        onSuccess: () => form.reset(),
    })
}
</script>


<template>
    <AdminLayout title="Create User">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create New User</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-md shadow">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Name</label>
                        <input v-model="form.name" type="text"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white" />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input v-model="form.email" type="email"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white" />
                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input v-model="form.password" type="password"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white" />
                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Mostrar o select apenas se type === 'admin' -->
                    <div v-if="type === 'admin'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select v-model="form.role"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white">
                            <option value="admin">Admin</option>
                            <option value="b2b">B2B</option>
                        </select>
                        <div v-if="form.errors.role" class="text-red-600 text-sm mt-1">{{ form.errors.role }}</div>
                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton type="submit">
                            Create
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
