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
    nextQuoteNumber: Number,
})

const showClientModal = ref(false)

const toType = ref('user') // user = B2B, client = B2C

const selectedUserId = ref(null)
const selectedClientId = ref(null)

const form = useForm({
    quote_number: props.nextQuoteNumber?.toString() || '',
    valid_until: '',            // alterado aqui para bater com backend
    currency: 'AED',
    to_type: toType.value,
    user_id: null,
    client_id: null,
    to_name: '',
    description: '',
    type: '',
    bond_tax: '',
    date: ''
})

watch(toType, (val) => {
    form.to_type = val
    selectedUserId.value = null
    selectedClientId.value = null
    form.user_id = null
    form.client_id = null
    form.to_name = ''
    form.description = ''
})

watch(selectedUserId, (val) => {
    if (toType.value === 'user' && val) {
        const user = props.users.find(u => u.id === val)
        form.user_id = val
        form.client_id = null
        form.to_name = user?.name || ''
    } else if (toType.value === 'client') {
        selectedClientId.value = null
        form.client_id = null
        form.to_name = ''
    }
})

watch(selectedClientId, (val) => {
    if (toType.value === 'client' && val) {
        const user = props.users.find(u => u.id === selectedUserId.value)
        const client = user?.clients.find(c => c.id === val)
        form.client_id = val
        form.user_id = selectedUserId.value
        form.to_name = client?.name || ''
    }
})

const filteredUsers = computed(() => props.users || [])

const clientsForSelectedUser = computed(() => {
    const user = props.users.find(u => u.id === selectedUserId.value)
    return user?.clients || []
})

function openCreateClientModal() {
    showClientModal.value = true
}

function onClientCreated() {
    alert('Client created successfully!')
    showClientModal.value = false
}

async function submit() {
  try {
    if (toType.value === 'user') {
      form.user_id = selectedUserId.value
      form.client_id = null
    } else {
      form.user_id = selectedUserId.value
      form.client_id = selectedClientId.value
    }

    await form.post(route('quotes.store'), {
      onSuccess: () => {
        form.reset()
        selectedUserId.value = null
        selectedClientId.value = null
        toType.value = 'user'
        form.to_name = ''
      }
    })
  } catch (error) {
    console.error('Erro no submit:', error)
  }
}
</script>

<template>
    <AdminLayout title="Create Quote">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create Quote
            </h2>

            <button @click="openCreateClientModal"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium focus:outline-none">
                + New Client
            </button>

            <CreateClientModal
                :show="showClientModal"
                :users="props.users"
                :services="props.services"
                @update:show="showClientModal = $event"
                @created="onClientCreated"
            />
        </template>

        <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 space-y-6">

                <!-- Tipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Quote Type
                    </label>
                    <select v-model="toType"
                        class="block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                        <option value="user">B2B (User)</option>
                        <option value="client">B2C (Client)</option>
                    </select>
                </div>

                <!-- Usuário -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Select User
                    </label>
                    <SearchableSelect
                        v-model="selectedUserId"
                        :options="filteredUsers"
                        label-key="name"
                        value-key="id"
                        placeholder="Search users..."
                    />
                    <p v-if="form.errors.user_id" class="text-red-600 text-sm mt-1">{{ form.errors.user_id }}</p>
                </div>

                <!-- Cliente -->
                <div v-if="toType === 'client'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 mt-4">
                        Select Client
                    </label>
                    <SearchableSelect
                        v-model="selectedClientId"
                        :options="clientsForSelectedUser"
                        label-key="name"
                        value-key="id"
                        placeholder="Search clients..."
                    />
                    <p v-if="form.errors.client_id" class="text-red-600 text-sm mt-1">{{ form.errors.client_id }}</p>
                </div>

                <!-- Campos principais -->
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
                        <p v-if="form.errors.currency" class="text-red-600 text-sm mt-1">{{ form.errors.currency }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quote Date</label>
                        <input v-model="form.date" type="date"
                            class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" />
                        <p v-if="form.errors.date" class="text-red-600 text-sm mt-1">{{ form.errors.date }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valid Until</label>
                        <input v-model="form.valid_until" type="date"
                            class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" />
                        <p v-if="form.errors.valid_until" class="text-red-600 text-sm mt-1">{{ form.errors.valid_until }}</p>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <input v-model="form.description" type="text"
                            class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" />
                        <p v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company</label>
                        <select v-model="form.type"
                            class="block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">Select company</option>
                            <option value="bondandpartners">Bond and Partners Corporate Service Providers LLC</option>
                            <option value="sheikhdom">Sheikhdom</option>
                        </select>
                        <p v-if="form.errors.type" class="text-red-600 text-sm mt-1">{{ form.errors.type }}</p>
                    </div>

                    <div v-if="form.type === 'bondandpartners'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Bond Tax
                        </label>
                        <select v-model="form.bond_tax"
                            class="block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Bond Tax</option>
                            <option value="tax_invoice">TAX INVOICE</option>
                            <option value="invoice">INVOICE</option>
                            <option value="proforma_invoice">PROFORMA INVOICE</option>
                        </select>
                        <p v-if="form.errors.bond_tax" class="text-red-600 text-sm mt-1">{{ form.errors.bond_tax }}</p>
                    </div>
                </div>

                <PrimaryButton @click="submit" :disabled="form.processing">
                    Create Quote
                </PrimaryButton>
            </div>
        </div>
    </AdminLayout>
</template>
