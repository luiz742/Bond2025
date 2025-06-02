<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    user: Object,
    client: Object,
    familyMembers: Array,
})

// Status possíveis
const statusOptions = ['pending', 'approved', 'rejected']

// Aba ativa
const activeTab = ref('client')

// Filtros separados para cada tabela
const filterClientText = ref('')
const filterCompanyText = ref('')

// Limpa filtro client ao mudar aba
watch(activeTab, () => {
    filterClientText.value = ''
})

// Monta abas
const tabs = computed(() => {
    return [
        { key: 'client', label: 'Client' },
        ...props.familyMembers.map(m => ({
            key: m.label.toLowerCase(),
            label: m.label.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
        }))
    ]
})

// Função que pega documentos da aba
const documentsForTab = (tabKey) => {
    const mapTabToClientType = {
        client: 'main',
        spouse: 'spouse',
        child_1: 'child_1',
    }
    const clientTypeToFilter = mapTabToClientType[tabKey] || tabKey

    return (props.client.service?.documents || []).filter(doc => doc.client_type === clientTypeToFilter)
}

// Documentos da empresa
const companyDocuments = computed(() =>
    (props.client.service?.documents || []).filter(doc => doc.type.toLowerCase() === 'company')
)

// Documentos filtrados client/family
const filteredClientDocuments = computed(() => {
    return documentsForTab(activeTab.value).filter(doc =>
        doc.name.toLowerCase().includes(filterClientText.value.toLowerCase())
    )
})

// Documentos filtrados company
const filteredCompanyDocuments = computed(() => {
    if (!filterCompanyText.value) return companyDocuments.value
    return companyDocuments.value.filter(doc =>
        doc.name.toLowerCase().includes(filterCompanyText.value.toLowerCase())
    )
})

// Formulário upload arquivos
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

// Retorna arquivo enviado (se existir) para um documento
const getFileForDocument = (documentId) => {
    return props.client.files.find(f => f.document_id === documentId)
}

// Retorna URL do arquivo
const getFileUrl = (file) => {
    if (!file) return null
    return `/storage/${file.path}`
}

const statusForm = useForm({
    status: '',
    id: null,
})

const statusSubmit = (status, id) => {
    statusForm.status = status
    statusForm.id = id

    statusForm.post(route('admin.files.status.update', id), {
        preserveScroll: true,
        onSuccess: () => console.log('Status atualizado com sucesso'),
        onError: (errors) => console.error(errors),
    })
}

</script>

<template>
    <AppLayout :title="`Client: ${client.name}`">
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

                                            <!-- Botões (aparecem só se status for pending) -->
                                            <div v-if="getFileForDocument(document.id)?.status === 'pending'"
                                                class="flex space-x-3">
                                                <PrimaryButton
                                                    class="px-4 py-1 bg-green-600 hover:bg-green-700 transition rounded text-sm"
                                                    :disabled="form.processing"
                                                    @click.prevent="statusSubmit('approved', getFileForDocument(document.id).id)">
                                                    Approve
                                                </PrimaryButton>

                                                <PrimaryButton
                                                    class="px-4 py-1 bg-red-600 hover:bg-red-700 transition rounded text-sm"
                                                    :disabled="form.processing"
                                                    @click.prevent="statusSubmit('rejected', getFileForDocument(document.id).id)">
                                                    Reject
                                                </PrimaryButton>
                                            </div>

                                            <!-- Link para ver arquivo -->
                                            <a :href="getFileUrl(getFileForDocument(document.id))" target="_blank"
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
                        Documents Issued By {{ client.service.country }} Government
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
    </AppLayout>
</template>
