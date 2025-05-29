<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    client: Object,
    services: Object,
})

const form = useForm({
    name: props.client.name,
    code_reference: props.client.code_reference || '',
    user_id: props.client.user_id,
    service_id: props.client.service_id || null,
})

const submit = () => {
    form.put(route('admin.clients.update', props.client.id))
}
</script>

<template>
    <AdminLayout title="Edit Client">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Client
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-md shadow">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client Name</label>
                        <input v-model="form.name" type="text"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white" />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code Reference</label>
                        <input v-model="form.code_reference" type="text"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white" />
                        <div v-if="form.errors.code_reference" class="text-red-600 text-sm mt-1">{{ form.errors.code_reference }}</div>
                    </div> -->

                    <div class="flex justify-end">
                        <PrimaryButton type="submit">
                            Update Client
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
