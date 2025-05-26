<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    country: ''
});

const submit = () => {
    form.post(route('admin.services.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AdminLayout title="Create Service">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Create New Service
            </h2>
        </template>

        <div class="py-12 bg-white dark:bg-gray-900 min-h-screen">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Service Name -->
                        <div>
                            <InputLabel for="name" value="Service Name" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-2 block w-full"
                                autocomplete="off"
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="country" value="Service Country" />
                            <TextInput
                                id="country"
                                v-model="form.country"
                                type="text"
                                class="mt-2 block w-full"
                                autocomplete="off"
                            />
                            <InputError :message="form.errors.country" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-start pt-4">
                            <ActionMessage :on="form.recentlySuccessful" class="mr-4 text-green-600 dark:text-green-400 font-semibold">
                                Service created successfully.
                            </ActionMessage>

                            <PrimaryButton :class="{ 'opacity-25 cursor-not-allowed': form.processing }"
                                :disabled="form.processing">
                                Create
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
