<script setup>
import { usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ActionMessage from '@/Components/ActionMessage.vue';

const page = usePage();
const userRole = page.props.auth.user.role;

const form = useForm({
    type: userRole,
    company_trade_license: undefined,
    tax_certificate: undefined,
    passport_copy: undefined,
    id_proof: undefined,
});

const onFileChange = (event, field) => {
    const file = event.target.files[0];
    if (file) {
        form[field] = file;
    }
};

const submit = () => {
    form.post(route('kyc.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AppLayout title="Create KYC">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Upload KYC Documents
            </h2>
        </template>

        <div class="py-12 bg-white dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                            Provide the following documents
                        </h3>

                        <div class="space-y-6">
                            <template v-if="userRole === 'b2b'">
                                <!-- Company Trade License -->
                                <div>
                                    <InputLabel for="company_trade_license" value="Company Trade License" />
                                    <input
                                        id="company_trade_license"
                                        type="file"
                                        @change="e => onFileChange(e, 'company_trade_license')"
                                        class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        accept=".pdf,.jpg,.png"
                                    />
                                    <InputError :message="form.errors.company_trade_license" class="mt-2" />
                                </div>

                                <!-- Tax Certificate -->
                                <div>
                                    <InputLabel for="tax_certificate" value="Tax Certificate" />
                                    <input
                                        id="tax_certificate"
                                        type="file"
                                        @change="e => onFileChange(e, 'tax_certificate')"
                                        class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        accept=".pdf,.jpg,.png"
                                    />
                                    <InputError :message="form.errors.tax_certificate" class="mt-2" />
                                </div>
                            </template>

                            <template v-else-if="userRole === 'b2c'">
                                <!-- Passport Copy -->
                                <div>
                                    <InputLabel for="passport_copy" value="Passport Copy" />
                                    <input
                                        id="passport_copy"
                                        type="file"
                                        @change="e => onFileChange(e, 'passport_copy')"
                                        class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        accept=".pdf,.jpg,.png"
                                    />
                                    <InputError :message="form.errors.passport_copy" class="mt-2" />
                                </div>

                                <!-- ID Proof -->
                                <div>
                                    <InputLabel for="id_proof" value="ID Proof" />
                                    <input
                                        id="id_proof"
                                        type="file"
                                        @change="e => onFileChange(e, 'id_proof')"
                                        class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        accept=".pdf,.jpg,.png"
                                    />
                                    <InputError :message="form.errors.id_proof" class="mt-2" />
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center justify-start pt-4">
                            <ActionMessage :on="form.recentlySuccessful" class="mr-4 text-green-600 dark:text-green-400 font-semibold">
                                Saved successfully.
                            </ActionMessage>

                            <PrimaryButton :class="{ 'opacity-25 cursor-not-allowed': form.processing }"
                                :disabled="form.processing">
                                Submit
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
