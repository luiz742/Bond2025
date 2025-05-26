<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    user: Object,
    client: Object,
})

// Filtro para a segunda tabela (company)
const filterText = ref('')

// Computed: documentos que o cliente deve enviar
const clientDocuments = computed(() =>
    (props.client.service?.documents || []).filter(doc => doc.type === 'client')
)

// Computed: documentos que a empresa envia
const companyDocuments = computed(() =>
    (props.client.service?.documents || []).filter(doc => doc.type === 'company')
)

// Computed: aplica filtro de texto na lista da empresa
const filteredCompanyDocuments = computed(() => {
    if (!filterText.value) return companyDocuments.value
    return companyDocuments.value.filter(doc =>
        doc.name.toLowerCase().includes(filterText.value.toLowerCase())
    )
})

// Formulário para envio de arquivos
const form = useForm({
    client_id: props.client.id,
    files: {},
})

// Atualiza arquivo ao selecionar
const onFileChange = (event, documentId) => {
    const file = event.target.files[0]
    if (file) {
        form.files[documentId] = file
    } else {
        delete form.files[documentId]
    }
}

// Envia arquivo individual
const submit = (documentId) => {
    if (!form.files[documentId]) return

    const dataToSend = {
        client_id: form.client_id,
        files: {
            [documentId]: form.files[documentId],
        },
    }

    form.post(route('client.upload-documents'), {
        data: dataToSend,
        onSuccess: () => {
            delete form.files[documentId]
        },
    })
}

// Retorna o arquivo enviado (se houver) para um documento
const getFileForDocument = (documentId) => {
    return props.client.files.find(f => f.document_id === documentId)
}

// Retorna a URL para visualização do arquivo
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
                <!-- GRID DE DUAS COLUNAS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- TABELA EXISTENTE (Documentos obrigatórios) -->
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

                                        <template v-if="getFileForDocument(document.id)">
                                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                                <span class="font-medium">Status:</span>
                                                {{ getFileForDocument(document.id).status }}
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

                    <!-- TABELA 2: Documentos que a empresa envia -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Documents Issued By Vanuatu Government
                        </h4>

                        <input v-model="filterText" type="text" placeholder="Filter company documents..."
                            class="mb-4 w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white" />

                        <table class="w-full table-auto border-collapse">
                            <tbody>
                                <tr v-for="document in filteredCompanyDocuments" :key="document.id">
                                    <td class="py-4">
                                        <InputLabel :for="`doc-${document.id}`" :value="document.name" class="mb-1" />

                                        <template v-if="getFileForDocument(document.id)">
                                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                                <span class="font-medium">Status:</span>
                                                {{ getFileForDocument(document.id).status }}
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
        </div>
    </AdminLayout>
</template>
