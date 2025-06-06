<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ActionMessage from '@/Components/ActionMessage.vue';

const props = defineProps({
    user: Object,
});

// Referências para inputs de arquivos
const fileInputRefs = {
    company_trade_license: ref(null),
    tax_certificate: ref(null),
    company_address: ref(null),          // novo campo (pode ser arquivo ou texto, ajusta abaixo)
    passport_copy: ref(null),
    id_proof: ref(null),
    proof_of_address: ref(null),
};

const form = useForm({
    type: props.user.role,
    company_trade_license: null,
    tax_certificate: null,
    company_address: '', // pode ser string se for endereço digitado, ou null se for arquivo
    passport_copy: null,
    id_proof: null,
    proof_of_address: null,
});

const isB2B = computed(() => props.user.kyc.type === 'b2b');
const isB2C = computed(() => props.user.kyc.type === 'b2c');

const handleFileChange = (event, field) => {
    const file = event.target.files[0];
    if (file) {
        form[field] = file;
    }
};

const submit = () => {
    form.post(route('kyc.store'), {
        preserveScroll: true,
        onSuccess: () => {
            Object.keys(fileInputRefs).forEach((key) => {
                if (fileInputRefs[key].value) {
                    fileInputRefs[key].value.value = null;
                }
            });
            form.reset();
        },
    });
};
</script>

<template>
    <FormSection @submitted="submit">
        <template #title>
            KYC / KYB Documents
        </template>

        <template #description>
            Upload the necessary documents for identity verification.
        </template>

        <template #form>
            <template v-if="isB2B">
                <!-- Company Trade License -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="company_trade_license" value="Company Trade License" />
                    <div v-if="user.kyc?.company_trade_license" class="mt-2">
                        <a :href="`/storage/${user.kyc.company_trade_license}`" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                            View Uploaded File
                        </a>
                    </div>
                    <div v-else>
                        <input id="company_trade_license" type="file" accept=".pdf,.jpg,.png"
                            ref="fileInputRefs.company_trade_license"
                            class="mt-2 block w-full text-sm text-gray-900 dark:text-white file:bg-blue-50 file:text-blue-700 file:px-4 file:py-2 file:rounded file:border-0 hover:file:bg-blue-100"
                            @change="e => handleFileChange(e, 'company_trade_license')" />
                        <InputError :message="form.errors.company_trade_license" class="mt-2" />
                    </div>
                </div>

                <!-- Tax Certificate -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="tax_certificate" value="Tax Certificate" />
                    <div v-if="user.kyc?.tax_certificate" class="mt-2">
                        <a :href="`/storage/${user.kyc.tax_certificate}`" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                            View Uploaded File
                        </a>
                    </div>
                    <div v-else>
                        <input id="tax_certificate" type="file" accept=".pdf,.jpg,.png"
                            ref="fileInputRefs.tax_certificate"
                            class="mt-2 block w-full text-sm text-gray-900 dark:text-white file:bg-blue-50 file:text-blue-700 file:px-4 file:py-2 file:rounded file:border-0 hover:file:bg-blue-100"
                            @change="e => handleFileChange(e, 'tax_certificate')" />
                        <InputError :message="form.errors.tax_certificate" class="mt-2" />
                    </div>
                </div>

                <!-- Proof of Address -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="proof_of_address" value="Company Utility Bill" />
                    <div v-if="user.kyc?.proof_of_address" class="mt-2">
                        <a :href="`/storage/${user.kyc.proof_of_address}`" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                            View Uploaded File
                        </a>
                    </div>
                    <div v-else>
                        <input id="proof_of_address" type="file" accept=".pdf,.jpg,.png"
                            ref="fileInputRefs.proof_of_address"
                            class="mt-2 block w-full text-sm text-gray-900 dark:text-white file:bg-blue-50 file:text-blue-700 file:px-4 file:py-2 file:rounded file:border-0 hover:file:bg-blue-100"
                            @change="e => handleFileChange(e, 'proof_of_address')" />
                        <InputError :message="form.errors.proof_of_address" class="mt-2" />
                    </div>
                </div>
            </template>

            <template v-if="isB2C">
                <!-- Passport Copy -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="passport_copy" value="Passport Copy" />
                    <div v-if="user.kyc?.passport_copy" class="mt-2">
                        <a :href="`/storage/${user.kyc.passport_copy}`" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                            View Uploaded File
                        </a>
                    </div>
                    <div v-else>
                        <input id="passport_copy" type="file" accept=".pdf,.jpg,.png"
                            ref="fileInputRefs.passport_copy"
                            class="mt-2 block w-full text-sm text-gray-900 dark:text-white file:bg-blue-50 file:text-blue-700 file:px-4 file:py-2 file:rounded file:border-0 hover:file:bg-blue-100"
                            @change="e => handleFileChange(e, 'passport_copy')" />
                        <InputError :message="form.errors.passport_copy" class="mt-2" />
                    </div>
                </div>

                <!-- ID Proof -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="id_proof" value="ID Proof" />
                    <div v-if="user.kyc?.id_proof" class="mt-2">
                        <a :href="`/storage/${user.kyc.id_proof}`" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                            View Uploaded File
                        </a>
                    </div>
                    <div v-else>
                        <input id="id_proof" type="file" accept=".pdf,.jpg,.png"
                            ref="fileInputRefs.id_proof"
                            class="mt-2 block w-full text-sm text-gray-900 dark:text-white file:bg-blue-50 file:text-blue-700 file:px-4 file:py-2 file:rounded file:border-0 hover:file:bg-blue-100"
                            @change="e => handleFileChange(e, 'id_proof')" />
                        <InputError :message="form.errors.id_proof" class="mt-2" />
                    </div>
                </div>

                <!-- Proof of Address -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="proof_of_address" value="Proof of Address" />
                    <div v-if="user.kyc?.proof_of_address" class="mt-2">
                        <a :href="`/storage/${user.kyc.proof_of_address}`" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                            View Uploaded File
                        </a>
                    </div>
                    <div v-else>
                        <input id="proof_of_address" type="file" accept=".pdf,.jpg,.png"
                            ref="fileInputRefs.proof_of_address"
                            class="mt-2 block w-full text-sm text-gray-900 dark:text-white file:bg-blue-50 file:text-blue-700 file:px-4 file:py-2 file:rounded file:border-0 hover:file:bg-blue-100"
                            @change="e => handleFileChange(e, 'proof_of_address')" />
                        <InputError :message="form.errors.proof_of_address" class="mt-2" />
                    </div>
                </div>
            </template>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Submit
            </PrimaryButton>
        </template>
    </FormSection>
</template>
