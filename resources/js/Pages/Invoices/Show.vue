<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ref, watch } from 'vue'

const props = defineProps({
    invoice: Object,
    users: Array,
})

const form = useForm({
    invoice_number: props.invoice.invoice_number,
    date: props.invoice.date,
    payment_due: props.invoice.payment_due,
    currency: props.invoice.currency,
    to_name: props.invoice.to_name,
    to_address: props.invoice.to_address,
    to_type: props.invoice.to_type,
    description: props.invoice.description ?? '',
    user_id: props.invoice.user_id,
    client_id: props.invoice.client_id,
    to_tax_registration_number: props.invoice.to_tax_registration_number ?? '',
    type: props.invoice.type
})

const submit = () => {
    console.log('Enviando update...')
    form.put(`/invoices/${props.invoice.id}`, {
        onSuccess: () => {
            console.log('Atualizado com sucesso!')
        },
        onError: (errors) => {
            console.log('Erros:', errors)
        },
        onFinish: () => {
            console.log('Requisição finalizada')
        }
    })
}

// Add Services
const showCreateServiceModal = ref(false)

const serviceForm = useForm({
    name: '',
    description: '',
    unit_price: '',
    total: 0,
    invoice_id: props.invoice.id,
})

watch(
    () => serviceForm.unit_price,
    () => {
        const p = parseFloat(serviceForm.unit_price)
        if (!isNaN(p)) {
            // Quantidade fixa = 1
            serviceForm.total = p.toFixed(2)
        } else {
            serviceForm.total = '0.00'
        }
    }
)

const openCreateServiceModal = () => {
    serviceForm.reset()
    showCreateServiceModal.value = true
}

const closeCreateServiceModal = () => {
    showCreateServiceModal.value = false
}

const submitServiceCreate = () => {
    serviceForm.post('/invoice-services', {
        onSuccess: () => {
            closeCreateServiceModal()
            window.location.reload() // recarrega a página para atualizar a lista de serviços
        },
    })
}

// Formatter moeda internacional (ex: AED, USD, EUR)
const formatCurrency = (value) => {
    if (isNaN(value)) return ''
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: form.currency || 'USD',
        minimumFractionDigits: 2,
    }).format(value)
}
</script>

<template>
    <AdminLayout :title="`Edit Invoice ${form.invoice_number}`">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Invoice #{{ form.invoice_number }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg p-8 text-gray-800 dark:text-gray-100">
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">To Name</label>
                            <input v-model="form.to_name"
                                class="w-full p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-black dark:text-white"
                                disabled />
                            <div v-if="form.errors.to_name" class="text-red-500 text-sm mt-1">{{ form.errors.to_name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">To Address</label>
                            <input v-model="form.to_address"
                                class="w-full p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-black dark:text-white" />
                            <div v-if="form.errors.to_address" class="text-red-500 text-sm mt-1">{{
                                form.errors.to_address }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">TRN</label>
                            <input v-model="form.to_tax_registration_number"
                                class="w-full p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-black dark:text-white" />
                            <div v-if="form.errors.to_tax_registration_number" class="text-red-500 text-sm mt-1">
                                {{ form.errors.to_tax_registration_number }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Date</label>
                            <input type="date" v-model="form.date"
                                class="w-full p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-black dark:text-white" />
                            <div v-if="form.errors.date" class="text-red-500 text-sm mt-1">{{ form.errors.date }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Payment Due</label>
                            <input type="date" v-model="form.payment_due"
                                class="w-full p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-black dark:text-white" />
                            <div v-if="form.errors.payment_due" class="text-red-500 text-sm mt-1">{{
                                form.errors.payment_due }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Currency</label>
                            <input v-model="form.currency"
                                class="w-full p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-black dark:text-white"
                                disabled />
                            <div v-if="form.errors.currency" class="text-red-500 text-sm mt-1">{{ form.errors.currency
                            }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Description</label>
                            <input type="text" v-model="form.description"
                                class="w-full p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-black dark:text-white" />
                            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{
                                form.errors.description }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Invoice Type</label>
                            <select v-model="form.type"
                                class="w-full p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-black dark:text-white">
                                <option value="bondandpartners">Bond And Partners</option>
                                <option value="sheikhdom">Sheikhdom</option>
                            </select>
                            <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">
                                {{ form.errors.type }}
                            </div>
                        </div>

                        <div class="md:col-span-2 text-right">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-md shadow transition-all">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Services Card -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2 pb-8">
            <div
                class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg p-8 text-gray-800 dark:text-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Services</h3>
                    <PrimaryButton @click="openCreateServiceModal">Add Service</PrimaryButton>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium">Service</th>
                                <th class="px-4 py-2 text-left text-sm font-medium">Qty</th>
                                <th class="px-4 py-2 text-left text-sm font-medium">Unit Price</th>
                                <th class="px-4 py-2 text-left text-sm font-medium">Total</th>
                                <th class="px-4 py-2 text-left text-sm font-medium">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="service in invoice.services || []" :key="service.id">
                                <td class="px-4 py-2">{{ service.name }}</td>
                                <td class="px-4 py-2">{{ service.quantity }}</td>
                                <td class="px-4 py-2">{{ formatCurrency(parseFloat(service.unit_price)) }}</td>
                                <td class="px-4 py-2">{{ formatCurrency(parseFloat(service.total)) }}</td>
                                <td class="px-4 py-2">{{ new Date(service.created_at).toLocaleString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="!(invoice.services?.length > 0)" class="text-gray-500 dark:text-gray-400 mt-4">
                    No services added to this invoice.
                </div>
            </div>
        </div>

        <!-- Create Service Modal -->
        <transition name="fade">
            <div v-if="showCreateServiceModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full p-5 mx-4 relative">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Add Service</h3>

                    <form @submit.prevent="submitServiceCreate">
                        <input v-model="serviceForm.name" type="text" placeholder="Service name"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white text-sm"
                            required />

                        <textarea v-model="serviceForm.description" placeholder="Description (optional)"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white text-sm"
                            rows="2"></textarea>

                        <input v-model="serviceForm.unit_price" type="number" step="0.01" placeholder="Unit Price"
                            class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-800 dark:text-white text-sm"
                            required />

                        <div class="mb-4 text-right text-sm text-gray-700 dark:text-gray-300">
                            <strong>Total:</strong> {{ formatCurrency(parseFloat(serviceForm.total)) }}
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeCreateServiceModal"
                                class="px-4 py-1.5 rounded-md bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-1.5 rounded-md bg-green-600 hover:bg-green-700 text-white font-semibold text-sm"
                                :disabled="serviceForm.processing">
                                Save
                            </button>
                        </div>
                    </form>

                    <button @click="closeCreateServiceModal"
                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                        ✕
                    </button>
                </div>
            </div>
        </transition>
    </AdminLayout>
</template>
