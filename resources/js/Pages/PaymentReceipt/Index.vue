<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    invoices: Object, // estrutura paginada { data: [...], prev_page_url, next_page_url }
});

function companyLabel(type) {
    switch (type) {
        case 'bondandpartners': return 'Bond and Partners';
        case 'sheikhdom': return 'Sheikhdom';
        default: return type || '-';
    }
}

function formatCurrency(value, currency) {
    if (isNaN(value)) return '-'
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD',
        minimumFractionDigits: 2,
    }).format(value)
}

// Função para calcular total da invoice (somando serviços)
function calculateTotal(services) {
    if (!services || services.length === 0) return 0
    return services.reduce((acc, service) => {
        const val = parseFloat(service.total)
        return acc + (isNaN(val) ? 0 : val)
    }, 0)
}
</script>

<template>
    <AdminLayout title="Payment Receipts">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Payment Receipts
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Invoice #</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Client</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Description</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Paid At</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Amount</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Company</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="invoice in invoices.data" :key="invoice.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                                        {{ invoice.invoice_number }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                                        {{ invoice.to_name }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100 max-w-xs truncate"
                                        :title="invoice.description || 'No description'">
                                        {{ invoice.description || '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                                        {{ invoice.paid_at ? new Date(invoice.paid_at).toLocaleDateString() : '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                                        {{ formatCurrency(calculateTotal(invoice.services), invoice.currency) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                                        {{ companyLabel(invoice.type) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <Link :href="`/invoices/${invoice.id}/receipt`"
                                            class="text-blue-600 hover:underline mr-3">
                                            View Receipt
                                        </Link>
                                        <Link :href="`/invoices/${invoice.id}/receipt/download`"
                                            class="text-green-600 hover:underline">
                                            Download
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <button v-if="invoices.prev_page_url" @click="router.visit(invoices.prev_page_url)"
                            class="text-sm text-gray-600 dark:text-gray-300">
                            ← Previous
                        </button>
                        <button v-if="invoices.next_page_url" @click="router.visit(invoices.next_page_url)"
                            class="text-sm text-gray-600 dark:text-gray-300">
                            Next →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
