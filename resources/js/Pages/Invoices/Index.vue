<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    invoices: Object, // estrutura paginada { data: [...], prev_page_url, next_page_url }
});

function companyLabel(type) {
  switch(type) {
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
    // o service.total pode vir como string, converte para float
    const val = parseFloat(service.total)
    return acc + (isNaN(val) ? 0 : val)
  }, 0)
}
</script>

<template>
    <AdminLayout title="Invoices">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Invoices
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-4">
                        <PrimaryButton as="span">
                            <Link href="/invoices/create" class="block w-full h-full">
                            Add New Invoice
                            </Link>
                        </PrimaryButton>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Invoice Number
                                    </th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        To
                                    </th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Date
                                    </th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Amount
                                    </th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Company
                                    </th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Actions
                                    </th>
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
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                                        {{ new Date(invoice.date).toLocaleDateString() }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                                        {{ formatCurrency(calculateTotal(invoice.services), invoice.currency) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">
                                        {{ companyLabel(invoice.type) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <Link :href="`/invoices/${invoice.id}`"
                                            class="text-blue-600 hover:underline mr-3">View
                                        </Link>
                                        <Link :href="`/invoices/${invoice.id}/printable`"
                                            class="text-green-600 hover:underline">Print</Link>
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
