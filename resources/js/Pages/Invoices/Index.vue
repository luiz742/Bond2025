<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    invoices: Object, // estrutura paginada { data: [...], prev_page_url, next_page_url }
});
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
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Invoice
                                        Number</th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        To</th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Date
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Currency</th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="invoice in invoices.data" :key="invoice.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{
                                        invoice.invoice_number }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ invoice.to_name }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ new
                                        Date(invoice.date).toLocaleString() }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-100">{{ invoice.currency
                                        }}</td>
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
