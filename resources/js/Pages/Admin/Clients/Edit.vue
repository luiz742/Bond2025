<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    client: Object,
    services: Object,
    familyMembers: Object,
})

const form = useForm({
    name: props.client.name,
    code_reference: props.client.code_reference || '',
    user_id: props.client.user_id,
    service_id: props.client.service_id || null,
})

const submit = () => {
    form.put(route('admin.clients.update', props.client.id))
}

// Modal de membros da família
const showModal = ref(false)
const editingMember = ref(null)
const familyMembers = ref([...props.familyMembers])

const allLabels = [
    { value: 'spouse', text: 'Spouse' },
    { value: 'child_1', text: 'Child 1' },
    { value: 'child_2', text: 'Child 2' },
    { value: 'child_3', text: 'Child 3' },
    { value: 'child_4', text: 'Child 4' },
]

const availableLabels = computed(() => {
    const used = familyMembers.value.map(f => f.label)
    return allLabels.filter(l => !used.includes(l.value) || (editingMember.value && editingMember.value.label === l.value))
})

const familyForm = useForm({
    client_id: props.client.id,
    name: '',
    label: '',
})

const openEditModal = (member) => {
    editingMember.value = { ...member }
    showModal.value = true
}

const openCreateModal = () => {
    editingMember.value = null
    familyForm.reset()
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editingMember.value = null
    familyForm.reset()
}

const submitFamily = () => {
    familyForm.post(route('family-members.store'), {
        onSuccess: () => {
            familyMembers.value.push({
                id: Date.now(),
                name: familyForm.name,
                label: familyForm.label,
            })
            closeModal()
        }
    })
}

const submitEditFamily = () => {
    useForm(editingMember.value).put(route('admin.family-members.update', editingMember.value.id), {
        onSuccess: () => {
            const index = familyMembers.value.findIndex(f => f.id === editingMember.value.id)
            if (index !== -1) familyMembers.value[index] = { ...editingMember.value }
            closeModal()
        }
    })
}

const currentName = computed({
    get: () => editingMember.value ? editingMember.value.name : familyForm.name,
    set: (val) => {
        if (editingMember.value) editingMember.value.name = val
        else familyForm.name = val
    }
})

const currentLabel = computed({
    get: () => editingMember.value ? editingMember.value.label : familyForm.label,
    set: (val) => {
        if (editingMember.value) editingMember.value.label = val
        else familyForm.label = val
    }
})
</script>

<template>
    <AdminLayout title="Edit Client">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Client
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Formulário de edição do cliente -->
                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 p-6 rounded-md shadow space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client Name</label>
                        <input v-model="form.name" type="text"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white" />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton type="submit">Update Client</PrimaryButton>
                    </div>
                </form>

                <!-- Seção membros da família -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Family Members</h3>
                        <PrimaryButton @click="openCreateModal" :disabled="availableLabels.length === 0">
                            Add Member
                        </PrimaryButton>
                    </div>
                    <div class="overflow-x-auto">
                        <table v-if="familyMembers.length" class="min-w-full text-sm text-gray-800 dark:text-gray-200">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="text-left px-4 py-2">Name</th>
                                    <th class="text-left px-4 py-2">Relationship</th>
                                    <th class="text-left px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="member in familyMembers" :key="member.id"
                                    class="border-t border-gray-300 dark:border-gray-600">
                                    <td class="px-4 py-2">{{ member.name }}</td>
                                    <td class="px-4 py-2 capitalize">{{ member.label.replace('_', ' ') }}</td>
                                    <td class="px-4 py-2">
                                        <button @click="openEditModal(member)"
                                            class="text-blue-600 hover:underline text-sm">Edit</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-else class="text-gray-500 dark:text-gray-400 text-sm">No family members yet.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal reutilizável para criar ou editar membros -->
        <transition name="fade">
            <div v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full p-5 mx-4 relative"
                    @click.stop>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                        {{ editingMember ? 'Edit Member' : 'Add Family Member' }}
                    </h3>

                    <form @submit.prevent="editingMember ? submitEditFamily() : submitFamily()" novalidate>
                        <input v-model="currentName" type="text" placeholder="Full name"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white text-sm"
                            required />

                        <select v-model="currentLabel"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white text-sm"
                            required>
                            <option value="">Select Relationship</option>
                            <option v-for="option in allLabels" :key="option.value" :value="option.value">
                                {{ option.text }}
                            </option>
                        </select>

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeModal"
                                class="px-4 py-1.5 rounded-md bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-1.5 rounded-md bg-green-600 hover:bg-green-700 text-white font-semibold text-sm"
                                :disabled="editingMember ? false : familyForm.processing">
                                {{ editingMember ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>

                    <button @click="closeModal"
                        class="absolute top-3 right-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                        aria-label="Close modal">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </transition>
    </AdminLayout>
</template>
