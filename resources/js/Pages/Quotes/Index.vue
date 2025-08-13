<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, router, useForm } from '@inertiajs/vue3';

defineProps({
    quotes: Object, // estrutura paginada { data: [...], prev_page_url, next_page_url }
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

// Função para calcular total do quote somando os serviços
function calculateTotal(services) {
    if (!services || services.length === 0) return 0
    return services.reduce((acc, service) => {
        const val = parseFloat(service.total)
        return acc + (isNaN(val) ? 0 : val)
    }, 0)
}

// Form para enviar post para criar invoice a partir do quote
const form = useForm()

function createInvoice(quoteId) {
    if (!quoteId) return;
    form.post(route('quotes.create-invoice', quoteId), {
        onSuccess: () => {
            alert('Invoice created successfully!')
            // Se quiser, pode atualizar a lista, redirecionar etc.
        },
        onError: (errors) => {
            console.error(errors)
            alert('Failed to create invoice.')
        }
    })
}
</script>

<template>
    <AdminLayout title="Quotes">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Quotes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-4">
                        <PrimaryButton as="span">
                            <Link href="/quotes/create" class="block w-full h-full">
                            Add New Quote
                            </Link>
                        </PrimaryButton>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Quote
                                        Number</th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        To</th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Description</th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Date
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Amount
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Company
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="quote in quotes.data" :key="quote.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ quote.quote_number
                                        }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ quote.to_name }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100 max-w-xs truncate"
                                        :title="quote.description || 'No description'">
                                        {{ quote.description || '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ new
                                        Date(quote.date).toLocaleDateString() }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{
                                        formatCurrency(calculateTotal(quote.services), quote.currency) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{
                                        companyLabel(quote.type)
                                        }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <Link :href="`/quotes/${quote.id}`" class="text-blue-600 hover:underline mr-3">
                                        View
                                        </Link>
                                        <Link :href="`/quotes/${quote.id}/printable`"
                                            class="text-green-600 hover:underline mr-3">Print</Link>
                                        <!-- Só mostra o botão se NÃO existir chain_id -->
                                        <button v-if="!quote.chain_id" @click="createInvoice(quote.id)"
                                            class="text-purple-600 hover:underline mr-3 bg-transparent border-none cursor-pointer p-0">
                                            Create Invoice
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <button v-if="quotes.prev_page_url" @click="router.visit(quotes.prev_page_url)"
                            class="text-sm text-gray-600 dark:text-gray-300">← Previous</button>
                        <button v-if="quotes.next_page_url" @click="router.visit(quotes.next_page_url)"
                            class="text-sm text-gray-600 dark:text-gray-300">Next →</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
