<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    users: Array,
    services: Array,
})

const form = useForm({
    name: '',
    user_id: null,
    service_id: '',
})

const submit = () => {
    form.post(route('admin.adminclients.store'), {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <AdminLayout title="New Client">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                New Client
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Nome do cliente -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Name
                            </label>
                            <input id="name" v-model="form.name" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                required />
                        </div>

                        <!-- Select usuário padrão -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Select User
                            </label>
                            <select id="user_id" v-model="form.user_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                <option value="" disabled>Select a user</option>
                                <option v-for="user in props.users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>

                        <PrimaryButton :class="{ 'opacity-25 cursor-not-allowed': form.processing }"
                            :disabled="form.processing">
                            Submit
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
