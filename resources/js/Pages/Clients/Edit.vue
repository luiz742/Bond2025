<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    client: Object,
    services: Object,
    familyMembers: Object,
})

// Form principal de edição do cliente
const form = useForm({
    name: props.client.name,
    code_reference: props.client.code_reference || '',
    user_id: props.client.user_id,
    service_id: props.client.service_id || null,
})

const submit = () => {
    form.put(route('admin.clients.update', props.client.id))
}

// Modal de família
const showModal = ref(false)

// Lista reativa de membros da família
const familyMembers = ref(props.familyMembers || [])

// Lista completa de possíveis labels
const allLabels = [
    { value: 'spouse', text: 'Spouse' },
    { value: 'child_1', text: 'Child 1' },
    { value: 'child_2', text: 'Child 2' },
    { value: 'child_3', text: 'Child 3' },
    { value: 'child_4', text: 'Child 4' },
]

// Computed para filtrar os labels já usados
const availableLabels = computed(() => {
    const usedLabels = familyMembers.value.map(m => m.label)
    return allLabels.filter(label => !usedLabels.includes(label.value))
})

const familyForm = useForm({
    client_id: props.client.id,
    name: '',
    label: '',
})

const submitFamily = () => {
    familyForm.post(route('family-members.store'), {
        onSuccess: () => {
            // Fecha modal
            showModal.value = false

            // Adiciona na lista local imediatamente
            familyMembers.value.push({
                id: Date.now(), // gera um id temporário único (exemplo)
                name: familyForm.name,
                label: familyForm.label,
            })

            // Reseta o form
            familyForm.reset()
        },
        onError: (errors) => {
        }
    });
}

const closeModal = () => {
    showModal.value = false
}
</script>

<template>
    <AppLayout title="Edit Client">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Client
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Formulário de edição do cliente -->
                <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-md shadow">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client Name</label>
                        <input v-model="form.name" type="text"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white" />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div class="flex justify-end">
                        <PrimaryButton type="submit">
                            Update Client
                        </PrimaryButton>
                    </div>
                </form>

                <!-- Seção de membros da família -->
                <div class="mt-10 bg-white dark:bg-gray-800 p-6 rounded-md shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Family Members</h3>
                        <PrimaryButton @click="showModal = true" :disabled="availableLabels.length === 0">
                            Add Member
                        </PrimaryButton>
                    </div>

                    <table v-if="familyMembers.length" class="min-w-full table-auto text-sm text-gray-800 dark:text-gray-200">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="text-left px-4 py-2">Name</th>
                                <th class="text-left px-4 py-2">Relationship</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="member in familyMembers" :key="member.id" class="border-t border-gray-300 dark:border-gray-600">
                                <td class="px-4 py-2">{{ member.name }}</td>
                                <td class="px-4 py-2 capitalize">{{ member.label.replace('_', ' ') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <p v-else class="text-gray-500 dark:text-gray-400 text-sm">No family members yet.</p>
                </div>
            </div>
        </div>

        <!-- Modal de Adição de Membro da Família -->
        <transition name="fade">
            <div v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full p-5 mx-4 relative"
                    @click.stop>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Add Family Member</h3>

                    <form @submit.prevent="submitFamily" novalidate>
                        <input v-model="familyForm.name" type="text" placeholder="Full name"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                            required />

                        <select v-model="familyForm.label"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                            required>
                            <option value="">Select Relationship</option>
                            <option v-for="option in availableLabels" :key="option.value" :value="option.value">
                                {{ option.text }}
                            </option>
                        </select>

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeModal"
                                class="px-4 py-1.5 rounded-md bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-1.5 rounded-md bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition"
                                :disabled="familyForm.processing || availableLabels.length === 0">
                                Save
                            </button>
                        </div>
                    </form>

                    <!-- Botão de fechar no canto superior -->
                    <button @click="closeModal" aria-label="Close modal"
                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </transition>
    </AppLayout>
</template>
