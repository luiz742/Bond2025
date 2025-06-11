import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';

export default function useDocumentUpdate() {
    const updating = ref(false);
    const form = useForm({
        file: null,
    });

    const update = async (clientId, fileId, file) => {
        console.log('📡 Dentro do composable: update()', { clientId, fileId, file })

        updating.value = true
        form.clearErrors()

        form.file = file

        try {
            await form.post(route('client.update-document', {
                client: clientId,
                id: fileId,
            }), {
                preserveScroll: true,
                forceFormData: true, // isso força multipart/form-data
                onSuccess: () => {
                    form.reset()
                },
                // 👇 Isso é essencial!
                _method: 'put',
            })
        } catch (error) {
            console.error(error)
        } finally {
            updating.value = false
        }
    }

    return {
        form,
        updating,
        update,
    };
}
