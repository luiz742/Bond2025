<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SearchableSelect from '@/Components/SearchableSelect.vue'
import CreateClientModal from '@/Components/CreateClientModal.vue'

const props = defineProps({
    users: Array,
    services: Array,
    nextInvoiceNumber: Number,
})

const showClientModal = ref(false)

const toType = ref('user')  // user = B2B, client = B2C

const selectedUserId = ref(null)
const selectedClientId = ref(null)

const form = useForm({
    invoice_number: props.nextInvoiceNumber?.toString() || '',
    payment_due: '',
    currency: 'AED',
    to_type: toType.value,
    user_id: null,
    client_id: null,
    to_name: '',
    description: '',
    type: '',
})

// Atualiza o tipo no form
watch(toType, (val) => {
    form.to_type = val
    // Reset seleção
    selectedUserId.value = null
    selectedClientId.value = null
    form.user_id = null
    form.client_id = null
    form.to_name = ''
    form.description = ''
})

// Atualiza form.user_id e form.to_name ao escolher user
watch(selectedUserId, (val) => {
    if (toType.value === 'user' && val) {
        const user = props.users.find(u => u.id === val)
        form.user_id = val
        form.client_id = null
        form.to_name = user?.name || ''
    } else if (toType.value === 'client') {
        // Se trocar usuário em modo client, reseta cliente selecionado
        selectedClientId.value = null
        form.client_id = null
        form.to_name = ''
    }
})

// Atualiza form.client_id e form.to_name ao escolher client
watch(selectedClientId, (val) => {
    if (toType.value === 'client' && val) {
        const user = props.users.find(u => u.id === selectedUserId.value)
        const client = user?.clients.find(c => c.id === val)
        form.client_id = val
        form.user_id = selectedUserId.value
        form.to_name = client?.name || ''
    }
})

// Filtra usuários para dropdown (você pode melhorar para filtro com input)
const filteredUsers = computed(() => {
    return props.users || []
})

// Clientes do usuário selecionado (para dropdown cliente)
const clientsForSelectedUser = computed(() => {
    const user = props.users.find(u => u.id === selectedUserId.value)
    return user?.clients || []
})

function openCreateClientModal() {
    showClientModal.value = true
}

// Após criar cliente, você pode querer atualizar a lista. Aqui só fecha e alerta
function onClientCreated() {
    alert('Cliente criado com sucesso!')
    showClientModal.value = false
    // TODO: atualizar a lista de usuários/clientes via API ou Inertia reload
}

function submit() {
    if (toType.value === 'user') {
        form.user_id = selectedUserId.value
        form.client_id = null
    } else {
        form.user_id = selectedUserId.value
        form.client_id = selectedClientId.value
    }

    form.post(route('invoices.store'), {
        onSuccess: () => {
            // Você pode mostrar mensagem, resetar formulário, etc
            form.reset()
            selectedUserId.value = null
            selectedClientId.value = null
            toType.value = 'user'
            form.to_name = ''
        }
    })
}
</script>

<template>
    <AdminLayout title="Create Invoice">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create Invoice
            </h2>

            <button @click="openCreateClientModal"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium focus:outline-none">
                + New Client
            </button>

            <CreateClientModal :show="showClientModal" :users="props.users" :services="props.services"
                @update:show="showClientModal = $event" @created="onClientCreated" />
        </template>

        <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 space-y-6">

                <!-- Tipo da Invoice -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Invoice Type
                    </label>
                    <select v-model="toType"
                        class="block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                        <option value="user">B2B (User)</option>
                        <option value="client">B2C (Client)</option>
                    </select>
                </div>

                <!-- Seleção de Usuário -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        User
                    </label>
                    <SearchableSelect v-model="selectedUserId" :options="filteredUsers" label-key="name" value-key="id"
                        placeholder="Search users..." />
                </div>

                <!-- Seleção de Cliente (apenas se B2C) -->
                <div v-if="toType === 'client'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 mt-4">
                        Client
                    </label>
                    <SearchableSelect v-model="selectedClientId" :options="clientsForSelectedUser" label-key="name"
                        value-key="id" placeholder="Search clients..." />
                </div>

                <!-- Outros campos da invoice -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
                        <select v-model="form.currency"
                            class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">Select currency</option>
                            <option value="AED">AED</option>
                            <option value="USD">USD</option>
                            <option value="AUD">AUD</option>
                            <option value="EGP">EGP</option>
                            <option value="TRY">TRY</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Due</label>
                        <input v-model="form.payment_due" type="date"
                            class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" />
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <input v-model="form.description" type="text"
                            class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company</label>
                        <select v-model="form.type"
                            class="block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">Select company</option>
                            <option value="bondandpartners">Bond and Partners Corporate Service Providers LLC</option>
                            <option value="sheikhdom">Sheikhdom</option>
                        </select>
                    </div>
                </div>

                <PrimaryButton @click="submit" :disabled="form.processing">
                    Create Invoice
                </PrimaryButton>
            </div>
        </div>
    </AdminLayout>
</template>
