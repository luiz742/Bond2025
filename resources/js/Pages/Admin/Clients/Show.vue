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

// Aba ativa
const activeTab = ref('client')

// Filtros separados para cada tabela
const filterClientText = ref('')
const filterCompanyText = ref('')

// Quando mudar de aba, limpa filtro client (não limpa company para manter independente)
watch(activeTab, () => {
    filterClientText.value = ''
})

// Monta as tabs: client + membros
const tabs = computed(() => {
    return [
        { key: 'client', label: 'Client' },
        ...props.familyMembers.map(m => ({
            key: m.label.toLowerCase(), // Garantir lowercase na key para comparação
            label: m.label.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
        }))
    ]
})

// Função para pegar documentos de cada aba (client ou membro)
const documentsForTab = (tabKey) => {
    // Se aba = client, pegar client_type = 'main'
    // Se aba = spouse, pegar client_type = 'spouse'
    // Caso haja outras abas, mapeie conforme necessário

    const mapTabToClientType = {
        client: 'main',
        spouse: 'spouse',
        child_1: 'child_1',
        // outras abas se tiver
    }

    const clientTypeToFilter = mapTabToClientType[tabKey] || tabKey

    const docs = (props.client.service?.documents || []).filter(doc => {
        return doc.client_type === clientTypeToFilter
    })

    return docs
}


// Documentos da empresa (sempre filtrados por 'company')
const companyDocuments = computed(() =>
    (props.client.service?.documents || []).filter(doc => doc.type.toLowerCase() === 'company')
)

// Documentos filtrados pelo filtro client/family
const filteredClientDocuments = computed(() => {
    return documentsForTab(activeTab.value).filter(doc =>
        doc.name.toLowerCase().includes(filterClientText.value.toLowerCase())
    )
})

// Documentos filtrados pelo filtro company
const filteredCompanyDocuments = computed(() => {
    if (!filterCompanyText.value) return companyDocuments.value
    return companyDocuments.value.filter(doc =>
        doc.name.toLowerCase().includes(filterCompanyText.value.toLowerCase())
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
    <AppLayout :title="`Client: ${client.name}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Client - {{ client.name }}
            </h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- TABS (Client + membros) -->
            <nav class="mb-6 flex space-x-4 border-b border-gray-200 dark:border-gray-700">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="activeTab = tab.key"
                    :class="[
                        'px-4 py-2 font-semibold',
                        activeTab === tab.key
                            ? 'border-b-2 border-blue-600 text-blue-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400'
                    ]"
                >
                    {{ tab.label }}
                </button>
            </nav>

            <!-- GRID 2 COLUNAS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- COLUNA 1: DOCUMENTOS CLIENTE/MEMBROS -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                    <input
                        v-model="filterClientText"
                        type="text"
                        :placeholder="`Filter documents for ${tabs.find(t => t.key === activeTab)?.label || ''}...`"
                        class="mb-4 w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white"
                    />

                    <table class="w-full table-auto border-collapse">
                        <tbody>
                            <tr v-for="document in filteredClientDocuments" :key="document.id">
                                <td class="py-4">
                                    <InputLabel :for="`doc-${document.id}`" :value="document.name" class="mb-1" />

                                    <template v-if="getFileForDocument(document.id)">
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            <span class="font-medium">Status:</span>
                                            {{ getFileForDocument(document.id).status }}
                                            <a
                                                :href="getFileUrl(getFileForDocument(document.id))"
                                                target="_blank"
                                                class="ml-4 text-blue-600 hover:underline"
                                            >
                                                View Document
                                            </a>
                                        </div>
                                    </template>

                                    <template v-else>
                                        <input
                                            :id="`doc-${document.id}`"
                                            type="file"
                                            @change="e => onFileChange(e, document.id)"
                                            class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                            accept=".pdf,.jpg,.png"
                                        />

                                        <InputError :message="form.errors[`files.${document.id}`]" class="mt-2" />

                                        <PrimaryButton
                                            v-if="form.files[document.id]"
                                            class="mt-3"
                                            :disabled="form.processing"
                                            @click.prevent="submit(document.id)"
                                        >
                                            Submit
                                        </PrimaryButton>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- COLUNA 2: DOCUMENTOS DA EMPRESA -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Documents Issued By {{ client.service.country }} Government
                    </h4>

                    <input
                        v-model="filterCompanyText"
                        type="text"
                        placeholder="Filter company documents..."
                        class="mb-4 w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white"
                    />

                    <table class="w-full table-auto border-collapse">
                        <tbody>
                            <tr v-for="document in filteredCompanyDocuments" :key="document.id">
                                <td class="py-4">
                                    <InputLabel :for="`doc-${document.id}`" :value="document.name" class="mb-1" />

                                    <template v-if="getFileForDocument(document.id)">
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            <span class="font-medium">Status:</span>
                                            {{ getFileForDocument(document.id).status }}
                                            <a
                                                :href="getFileUrl(getFileForDocument(document.id))"
                                                target="_blank"
                                                class="ml-4 text-blue-600 hover:underline"
                                            >
                                                View Document
                                            </a>
                                        </div>
                                    </template>

                                    <template v-else>
                                        <input
                                            :id="`doc-${document.id}`"
                                            type="file"
                                            @change="e => onFileChange(e, document.id)"
                                            class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                            accept=".pdf,.jpg,.png"
                                        />

                                        <InputError :message="form.errors[`files.${document.id}`]" class="mt-2" />

                                        <PrimaryButton
                                            v-if="form.files[document.id]"
                                            class="mt-3"
                                            :disabled="form.processing"
                                            @click.prevent="submit(document.id)"
                                        >
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
