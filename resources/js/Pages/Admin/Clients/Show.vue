<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    user: Object,
    client: Object,
})

const files = ref([])
files.value = props.client.files.map(f => ({ ...f }))

watch(() => props.client.files, (newFiles) => {
    files.value = newFiles.map(f => ({ ...f }))
})

const filterText = ref('')

// Form para upload de arquivos
const uploadForm = useForm({
    client_id: props.client.id,
    files: {},
})

// Form para atualização de status
const statusForm = useForm({
    status: '',
})

const onFileChange = (event, documentId) => {
    const file = event.target.files[0]
    if (file) {
        uploadForm.files[documentId] = file
    } else {
        delete uploadForm.files[documentId]
    }
}

const submit = (documentId) => {
    if (!uploadForm.files[documentId]) return

    const dataToSend = {
        client_id: uploadForm.client_id,
        files: {
            [documentId]: uploadForm.files[documentId],
        },
    }

    uploadForm.post(route('client.upload-documents'), {
        preserveScroll: true,
        preserveState: false,
        data: dataToSend,
        onSuccess: () => {
            delete uploadForm.files[documentId]
        },
    })
}

const onStatusChange = (fileId, newStatus, documentId) => {
    if (!fileId) return

    // Atualiza localmente
    const file = files.value.find(f => f.document_id === documentId)
    if (file) {
        file.status = newStatus
    }

    // Atualiza via backend
    statusForm.status = newStatus

    statusForm.put(route('admin.update-document-status', fileId), {
        preserveScroll: true,
        onSuccess: () => {
            console.log(`Status do arquivo ${fileId} atualizado para ${newStatus}`)
        },
    })
}

const clientDocuments = computed(() =>
    (props.client.service?.documents || []).filter(doc => doc.type === 'client')
)
const companyDocuments = computed(() =>
    (props.client.service?.documents || []).filter(doc => doc.type === 'company')
)

const filteredCompanyDocuments = computed(() => {
    if (!filterText.value) return companyDocuments.value
    return companyDocuments.value.filter(doc =>
        doc.name.toLowerCase().includes(filterText.value.toLowerCase())
    )
})

const getFileForDocumentLocal = (documentId) => {
    return files.value.find(f => f.document_id === documentId)
}

const getFileUrl = (file) => {
    if (!file) return null
    return `/storage/${file.path}`
}
</script>

<template>
    <AdminLayout :title="`Client: ${client.name}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Client - {{ client.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Documentos do cliente -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Upload Required Documents
                        </h4>

                        <input v-model="filterText" type="text" placeholder="Filter documents by name..."
                            class="mb-4 w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white" />

                        <table class="w-full table-auto border-collapse">
                            <tbody>
                                <tr v-for="document in clientDocuments" :key="document.id">
                                    <td class="py-4">
                                        <InputLabel :for="`doc-${document.id}`" :value="document.name" class="mb-1" />

                                        <template v-if="getFileForDocumentLocal(document.id)">
                                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                                <span class="font-medium">Status:</span>
                                                <span class="capitalize">{{ getFileForDocumentLocal(document.id).status
                                                    }}</span>
                                                <a :href="getFileUrl(getFileForDocumentLocal(document.id))"
                                                    target="_blank" class="ml-4 text-blue-600 hover:underline">
                                                    View Document
                                                </a>
                                            </div>

                                            <div class="mt-2">
                                                <form
                                                    @submit.prevent="onStatusChange(getFileForDocumentLocal(document.id).id, getFileForDocumentLocal(document.id).status, document.id)">
                                                    <select :value="getFileForDocumentLocal(document.id).status"
                                                        @change="e => onStatusChange(getFileForDocumentLocal(document.id).id, e.target.value, document.id)"
                                                        class="border rounded px-2 py-1 mr-2">
                                                        <option value="pending">Pending</option>
                                                        <option value="approved">Approved</option>
                                                        <option value="rejected">Rejected</option>
                                                    </select>
                                                </form>
                                            </div>
                                        </template>

                                        <template v-else>
                                            <input :id="`doc-${document.id}`" type="file"
                                                @change="e => onFileChange(e, document.id)"
                                                class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                accept=".pdf,.jpg,.png" />

                                            <InputError :message="uploadForm.errors[`files.${document.id}`]"
                                                class="mt-2" />

                                            <PrimaryButton v-if="uploadForm.files[document.id]" class="mt-3"
                                                :disabled="form.processing" @click.prevent="submit(document.id)">
                                                Submit
                                            </PrimaryButton>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Documentos da empresa -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Company Documents
                        </h4>

                        <input v-model="filterText" type="text" placeholder="Filter company documents..."
                            class="mb-4 w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white" />

                        <table class="w-full table-auto border-collapse">
                            <tbody>
                                <tr v-for="document in filteredCompanyDocuments" :key="document.id">
                                    <td class="py-4">
                                        <InputLabel :for="`doc-${document.id}`" :value="document.name" class="mb-1" />

                                        <template v-if="getFileForDocumentLocal(document.id)">
                                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                                <span class="font-medium">Status:</span>
                                                {{ getFileForDocumentLocal(document.id).status }}
                                                <a :href="getFileUrl(getFileForDocumentLocal(document.id))"
                                                    target="_blank" class="ml-4 text-blue-600 hover:underline">
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
        </div>
    </AdminLayout>
</template>
