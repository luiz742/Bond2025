<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    user: Object,
    services: Array,
});

const form = useForm({
    name: '',
    service_id: '', // começa como null
    user_id: props.user.id,
})

const submit = () => {
    form.post(route('admin.clients.user.store', { user: props.user.id }), {
        onSuccess: () => {
            form.reset()
        },
        onError: (errors) => {
            console.error('Error:', errors)
        }
    })

}
</script>

<template>
    <AdminLayout :title="`New Client for ${user.name}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                New Client
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Campo nome -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Client Name
                            </label>
                            <input id="name" v-model="form.name" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                required />
                        </div>

                        <!-- Campo select de serviços -->
                        <div>
                            <label for="service_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Service
                            </label>
                            <select id="service_id" v-model="form.service_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                <option value="">Select a Service</option>
                                <option v-for="service in services" :key="service.id" :value="service.id">
                                    {{ service.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Botão de envio -->
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
