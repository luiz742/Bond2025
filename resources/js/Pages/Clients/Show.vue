<!-- <script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    user: Object,
    client: Object,
})

const filterText = ref('')

const filteredDocuments = computed(() => {
    if (!filterText.value) {
        return props.client.service?.documents || []
    }
    return (props.client.service?.documents || []).filter(doc =>
        doc.name.toLowerCase().includes(filterText.value.toLowerCase())
    )
})

const form = useForm({
    client_id: props.client.id,
    files: {},
})

const onFileChange = (event, documentId) => {
    const file = event.target.files[0]
    if (file) {
        form.files[documentId] = file
    } else {
        delete form.files[documentId]
    }
}

const submit = (documentId = null) => {
    let dataToSend = {
        client_id: form.client_id,
        files: {},
    }
    if (documentId) {
        if (form.files[documentId]) {
            dataToSend.files[documentId] = form.files[documentId]
        } else {
            return
        }
    } else {
        dataToSend.files = form.files
    }

    form.post(route('client.upload-documents'), {
        data: dataToSend,
        onSuccess: () => {
            if (documentId) {
                delete form.files[documentId]
            } else {
                form.reset('files')
            }
        }
    })
}

const getFileForDocument = (documentId) => {
    return props.client.files.find(f => f.document_id === documentId)
}

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

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Upload Required Documents
                    </h4>

                    <input v-model="filterText" type="text" placeholder="Filter documents by name..."
                        class="mb-4 w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white" />

                    <table class="w-full table-auto border-collapse border border-gray-300 dark:border-gray-700">
                        <tbody>
                            <tr v-for="(document, index) in filteredDocuments" :key="document.id">
                                <template v-if="index % 2 === 0">
                                    <td class="border border-gray-300 dark:border-gray-700 p-4 align-top w-1/2">
                                        <InputLabel :for="`doc-${document.id}`" :value="document.name" />

                                        <template v-if="getFileForDocument(document.id)">
                                            <span class="mr-2 font-semibold">Status:</span>
                                            <span>{{ getFileForDocument(document.id).status }}</span>
                                            <a :href="getFileUrl(getFileForDocument(document.id))" target="_blank"
                                                class="ml-4 text-blue-600 hover:underline">
                                                Open Document
                                            </a>
                                        </template>

                                        <template v-else>
                                            <input :id="`doc-${document.id}`" type="file"
                                                @change="e => onFileChange(e, document.id)"
                                                class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                accept=".pdf,.jpg,.png" />
                                        </template>
                                        <InputError :message="form.errors[`files.${document.id}`]" class="mt-2" />

                                        <PrimaryButton v-if="form.files[document.id]" class="mt-3"
                                            :disabled="form.processing" @click.prevent="submit(document.id)">
                                            Submit
                                        </PrimaryButton>
                                    </td>

                                    <td v-if="filteredDocuments[index + 1]"
                                        class="border border-gray-300 dark:border-gray-700 p-4 align-top w-1/2">
                                        <InputLabel :for="`doc-${filteredDocuments[index + 1].id}`"
                                            :value="filteredDocuments[index + 1].name" />

                                        <template v-if="getFileForDocument(filteredDocuments[index + 1].id)">
                                            <span class="mr-2 font-semibold">Status:</span>
                                            <span>{{ getFileForDocument(filteredDocuments[index + 1].id).status
                                                }}</span>
                                            <a :href="getFileUrl(getFileForDocument(filteredDocuments[index + 1].id))"
                                                target="_blank" class="ml-4 text-blue-600 hover:underline">
                                                Open Document
                                            </a>
                                        </template>

                                        <template v-else>
                                            <input :id="`doc-${filteredDocuments[index + 1].id}`" type="file"
                                                @change="e => onFileChange(e, filteredDocuments[index + 1].id)"
                                                class="mt-2 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                accept=".pdf,.jpg,.png" />
                                        </template>
                                        <InputError :message="form.errors[`files.${filteredDocuments[index + 1].id}`]"
                                            class="mt-2" />

                                        <PrimaryButton v-if="form.files[filteredDocuments[index + 1].id]" class="mt-3"
                                            :disabled="form.processing"
                                            @click.prevent="submit(filteredDocuments[index + 1].id)">
                                            Submit
                                        </PrimaryButton>
                                    </td>
                                    <td v-else class="w-1/2 border border-gray-300 dark:border-gray-700 p-4"></td>
                                </template>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template> -->

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    user: Object,
    client: Object,
})

const filterText = ref('')

const filteredDocuments = computed(() => {
    if (!filterText.value) {
        return props.client.service?.documents || []
    }
    return (props.client.service?.documents || []).filter(doc =>
        doc.name.toLowerCase().includes(filterText.value.toLowerCase())
    )
})

const form = useForm({
    client_id: props.client.id,
    files: {},
})

const onFileChange = (event, documentId) => {
    const file = event.target.files[0]
    if (file) {
        form.files[documentId] = file
    } else {
        delete form.files[documentId]
    }
}

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

const getFileForDocument = (documentId) => {
    return props.client.files.find(f => f.document_id === documentId)
}

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

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Upload Required Documents
                    </h4>

                    <input v-model="filterText" type="text" placeholder="Filter documents by name..."
                        class="mb-4 w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 dark:bg-gray-700 dark:text-white" />

                    <table class="w-full table-auto border-collapse">
                        <tbody>
                            <tr v-for="document in filteredDocuments" :key="document.id"
                                class="border-b border-gray-300 dark:border-gray-700">
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
    </AppLayout>
</template>
