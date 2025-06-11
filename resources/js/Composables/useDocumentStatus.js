import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

export default function useDocumentStatus() {
    const showRejectionModal = ref(false)
    const pendingRejectId = ref(null)

    const statusForm = useForm({
        status: '',
        id: null,
        rejection_reason: '',
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

    const openRejectionModal = (id) => {
        pendingRejectId.value = id
        statusForm.rejection_reason = ''
        showRejectionModal.value = true
    }

    const submitRejection = () => {
        statusForm.status = 'rejected'
        statusForm.id = pendingRejectId.value

        statusForm.post(route('admin.files.status.update', pendingRejectId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showRejectionModal.value = false
                pendingRejectId.value = null
            },
            onError: (errors) => console.error(errors),
        })
    }

    return {
        statusForm,
        statusSubmit,
        showRejectionModal,
        openRejectionModal,
        submitRejection,
    }
}
