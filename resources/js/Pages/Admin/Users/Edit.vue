<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    user: Object,
    services: Object,
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    role: props.user.role,
    service_id: props.user.service_id || null,  // pega o service_id se existir
})

const submit = () => {
    form.put(route('admin.users.update', props.user.id))
}
</script>

<template>
    <AdminLayout title="Edit User">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit User
            </h2>
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
                        <input v-model="form.password" type="password" placeholder="Leave blank to keep current"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white" />
                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select v-model="form.role"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white">
                            <option value="admin">Admin</option>
                            <option value="b2b">B2B</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                        <div v-if="form.errors.role" class="text-red-600 text-sm mt-1">{{ form.errors.role }}</div>
                    </div>

                    <div v-if="user.role === 'admin'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Program Allowed
                        </label>
                        <select v-model="form.service_id"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white">
                            <option value="">-- Select a service --</option>
                            <option v-for="service in services" :key="service.id" :value="service.id">
                                {{ service.name }}
                            </option>
                        </select>
                        <div v-if="form.errors.service_id" class="text-red-600 text-sm mt-1">{{ form.errors.service_id
                            }}</div>
                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton type="submit">
                            Update
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
