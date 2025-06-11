import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

export default function useDocumentUpload(props) {
    const activeTab = ref('client')
    const filterClientText = ref('')
    const filterCompanyText = ref('')

    watch(activeTab, () => {
        filterClientText.value = ''
    })

    const tabs = computed(() => [
        { key: 'client', label: 'Client' },
        ...props.familyMembers.map(m => ({
            key: m.label.toLowerCase(),
            label: m.label.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
        }))
    ])

    const documentsForTab = (tabKey) => {
        const mapTabToClientType = {
            client: 'main',
            spouse: 'spouse',
            child_1: 'child_1',
        }
        const clientTypeToFilter = mapTabToClientType[tabKey] || tabKey
        return (props.client.service?.documents || []).filter(doc => doc.client_type === clientTypeToFilter)
    }

    const companyDocuments = computed(() =>
        (props.client.service?.documents || []).filter(doc => doc?.type?.toLowerCase() === 'company')
    )

    const filteredClientDocuments = computed(() =>
        documentsForTab(activeTab.value).filter(doc =>
            doc.name.toLowerCase().includes(filterClientText.value.toLowerCase())
        )
    )

    const filteredCompanyDocuments = computed(() => {
        if (!filterCompanyText.value) return companyDocuments.value
        return companyDocuments.value.filter(doc =>
            doc.name.toLowerCase().includes(filterCompanyText.value.toLowerCase())
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

    return {
        activeTab,
        tabs,
        filterClientText,
        filterCompanyText,
        filteredClientDocuments,
        filteredCompanyDocuments,
        onFileChange,
        submit,
        getFileForDocument,
        getFileUrl,
        form,
    }
}
