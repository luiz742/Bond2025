<script setup>
import { computed } from 'vue'
import useDocumentUpload from '@/Composables/useDocumentUpload'
import useDocumentTabs from '@/Composables/useDocumentTabs'
import useDocumentStatus from '@/Composables/useDocumentStatus'
import useDocumentUpdate from '@/Composables/useDocumentUpdate'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    user: Object,
    client: Object,
    familyMembers: Array,
})

const { tabs } = useDocumentTabs(props.familyMembers)


const {
    activeTab,
    filterClientText,
    filterCompanyText,
    filteredClientDocuments,
    filteredCompanyDocuments,
    onFileChange,
    submit,
    getFileForDocument,
    getFileUrl,
    form
} = useDocumentUpload(props)

const {
    form: updateForm,
    updating,
    update
} = useDocumentUpdate()

const {
    statusForm,
    statusSubmit,
    showRejectionModal,
    openRejectionModal,
    submitRejection
} = useDocumentStatus()
</script>



<template>
    <AdminLayout :title="`Client: ${client.name}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Client - {{ client.name }}
            </h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- TABS -->
            <nav class="mb-6 flex space-x-4 border-b border-gray-200 dark:border-gray-700">
                <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="[
                    'px-4 py-2 font-semibold',
                    activeTab === tab.key
                        ? 'border-b-2 border-blue-600 text-blue-600'
                        : 'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400'
                ]">
                    {{ tab.label }}
                </button>
            </nav>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- DOCUMENTOS CLIENTE/MEMBROS -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                    <input v-model="filterClientText" type="text"
                        :placeholder="`Filter documents for ${tabs.find(t => t.key === activeTab)?.label || ''}...`"
                        class="mb-4 w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white" />

                    <table class="w-full table-auto border-collapse">
                        <tbody>
                            <tr v-for="document in filteredClientDocuments" :key="document.id">
                                <td class="py-4">
                                    <InputLabel :for="`doc-${document.id}`" :value="document.name" class="mb-1" />

                                    <template v-if="getFileForDocument(document.id)">
                                        <div
                                            class="text-sm text-gray-700 dark:text-gray-300 flex items-center space-x-4">

                                            <!-- Status -->
                                            <div class="flex items-center space-x-2">
                                                <span
                                                    class="font-semibold text-gray-700 dark:text-gray-300">Status:</span>
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-semibold text-white uppercase"
                                                    :class="{
                                                        'bg-yellow-400': getFileForDocument(document.id)?.status === 'pending',
                                                        'bg-green-500': getFileForDocument(document.id)?.status === 'approved',
                                                        'bg-red-500': getFileForDocument(document.id)?.status === 'rejected'
                                                    }">
                                                    {{ getFileForDocument(document.id)?.status?.toUpperCase() || 'N/A'
                                                    }}
                                                </span>
                                            </div>

                                            <!-- Botões se status for "pending" -->
                                            <div v-if="getFileForDocument(document.id)?.status === 'pending'"
                                                class="flex space-x-3">
                                                <button
                                                    class="px-3 py-1 rounded-full text-xs font-semibold text-white uppercase bg-green-500 hover:bg-green-600 transition"
                                                    :disabled="form.processing"
                                                    @click.prevent="statusSubmit('approved', getFileForDocument(document.id).id)">
                                                    Approve
                                                </button>

                                                <button
                                                    class="px-3 py-1 rounded-full text-xs font-semibold text-white uppercase bg-red-500 hover:bg-red-600 transition"
                                                    :disabled="form.processing"
                                                    @click.prevent="openRejectionModal(getFileForDocument(document.id).id)">
                                                    Reject
                                                </button>
                                            </div>


                                            <!-- Link para ver arquivo se status for diferente de 'rejected' -->
                                            <a v-if="getFileForDocument(document.id)?.status !== 'rejected'"
                                                :href="getFileUrl(getFileForDocument(document.id))" target="_blank"
                                                class="flex items-center space-x-1 text-blue-500 hover:text-blue-700 underline whitespace-nowrap">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>View File</span>
                                            </a>

                                            <!-- Se status do arquivo for 'rejected', mostra botão e input para reenvio -->
                                            <div v-if="getFileForDocument(document.id)?.status === 'rejected'"
                                                class="mt-2">

                                                <input type="file" :id="`reupload-${document.id}`"
                                                    @change="(e) => onFileChange(e, document.id)"
                                                    class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer bg-white dark:bg-gray-700 focus:outline-none" />

                                                <PrimaryButton class="mt-2"
                                                    :disabled="form.processing || !form.files[document.id]"
                                                    @click.prevent="() => {
                                                        const fileEntry = getFileForDocument(document.id);
                                                        if (fileEntry?.id) {
                                                            update(client.id, fileEntry.id, form.files[document.id]);
                                                        } else {
                                                            alert('Arquivo não encontrado para reenvio.');
                                                        }
                                                    }">
                                                    Reupload
                                                </PrimaryButton>

                                                <InputError :message="form.errors[`files.${document.id}`]"
                                                    class="mt-2" />
                                            </div>

                                        </div>


                                        <div class="flex items-center space-x-2">
                                            <span v-if="getFileForDocument(document.id)?.status === 'rejected'"
                                                class=" text-sm">
                                                <span class="text-red-500">Rejection Reason: </span>
                                                {{ getFileForDocument(document.id)?.rejection_reason || 'N/A' }}
                                            </span>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <input :id="`doc-${document.id}`" type="file"
                                            @change="e => onFileChange(e, document.id)"
                                            class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                            accept=".pdf,.jpg,.png" />

                                        <InputError :message="form.errors[`files.${document.id}`]" class="mt-2" />

                                        <PrimaryButton v-if="form.files[document.id]" class="mt-3"
                                            :disabled="form.processing" @click.prevent="submit(document.id)">
                                            Submit
                                        </PrimaryButton>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- DOCUMENTOS DA EMPRESA -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Documents Issued By {{ client.service?.country || '' }} Government
                    </h4>

                    <input v-model="filterCompanyText" type="text" placeholder="Filter company documents..."
                        class="mb-4 w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white" />

                    <table class="w-full table-auto border-collapse">
                        <tbody>
                            <tr v-for="document in filteredCompanyDocuments" :key="document.id">
                                <td class="py-4">
                                    <InputLabel :for="`doc-${document.id}`" :value="document.name" class="mb-1" />

                                    <template v-if="getFileForDocument(document.id)">
                                        <div
                                            class="text-sm text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                                            <span class="font-medium">Status:</span>


                                            <a :href="getFileUrl(getFileForDocument(document.id))" target="_blank"
                                                class="ml-4 text-blue-600 hover:underline">
                                                View Document
                                            </a>
                                        </div>
                                    </template>

                                    <template v-else>
                                        <input :id="`doc-${document.id}`" type="file"
                                            @change="e => onFileChange(e, document.id)"
                                            class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                            accept=".pdf,.jpg,.png" />

                                        <InputError :message="form.errors[`files.${document.id}`]" class="mt-2" />

                                        <PrimaryButton v-if="form.files[document.id]" class="mt-3"
                                            :disabled="form.processing" @click.prevent="submit(document.id)">
                                            Submit
                                        </PrimaryButton>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal for rejection reason -->
        <template v-if="showRejectionModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Reason for Rejection</h2>

                    <textarea v-model="statusForm.rejection_reason"
                        class="w-full h-24 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white"
                        placeholder="Enter the reason for rejection..."></textarea>

                    <InputError :message="statusForm.errors.rejection_reason" class="mt-2" />

                    <div class="mt-4 flex justify-end space-x-2">
                        <PrimaryButton @click="showRejectionModal = false" class="bg-gray-500 hover:bg-gray-600">
                            Cancel
                        </PrimaryButton>
                        <PrimaryButton @click="submitRejection" :disabled="statusForm.processing"
                            class="bg-red-600 hover:bg-red-700">
                            Submit Rejection
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>
