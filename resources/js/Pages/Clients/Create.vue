<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    user: Object,
    services: Array,
});

const form = useForm({
    name: '',
    service_id: '',
    user_id: props.user.id, // incluir explicitamente no payload
})

const submit = () => {
    // alert(form.service_id)
    form.post(route('clients.store'), {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <AppLayout :title="`New Client for ${user.name}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                New Client
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Name
                            </label>
                            <input id="name" v-model="form.name" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                required />
                        </div>

                        <div>
                            <label for="service_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Service
                            </label>
                            <select id="service_id" v-model="form.service_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                                required>
                                <option value="" disabled>Select a service</option>
                                <option v-for="service in services" :key="service.id" :value="service.id">
                                    {{ service.name }}
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
    </AppLayout>
</template>
