<script setup>
const props = defineProps({ invoice: Object });

// Dados fixos "From"
const fromDetails = {
    name: 'Bond and Partners Corporate Service Providers LLC',
    address: 'Office 101, Business Bay, Dubai, UAE',
    email: 'info@bondandpartners.com',
    phone: '+971 4 123 4567',
    bank_details: 'Bank: Emirates NBD, IBAN: AE070260000000123456789',
};

function formatCurrency(value, currency) {
    if (typeof value !== 'number') value = parseFloat(value) || 0;
    return value.toLocaleString(undefined, {
        style: 'currency',
        currency: currency || 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

const totalServices = props.invoice.services
    ? props.invoice.services.reduce((sum, service) => sum + parseFloat(service.unit_price || 0), 0)
    : 0;
</script>

<template>
    <div class="text-gray-900 bg-white p-10 font-sans mx-auto shadow-lg border border-gray-200 rounded-md flex flex-col"
        style="width: 794px; min-height: 1123px;">
        <!-- Conteúdo principal -->
        <div>
            <!-- Logo -->
            <div class="mb-20">
                <img src="/images/logo.png" alt="Logo" class="h-12 w-auto mx-auto" />
            </div>

            <!-- Cabeçalho -->
            <div class="flex justify-between items-center mb-8 mt-4 border-b border-gray-300 pb-4">
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wide leading-tight mb-1">
                        Tax Invoice / Proforma Invoice
                    </h4>
                    <p class="text-sm text-gray-700">
                        Invoice #: <span class="font-medium">{{ props.invoice.invoice_number }}</span>
                    </p>
                    <p class="text-sm text-gray-700">
                        Date: <span class="font-medium">{{ props.invoice.date }}</span>
                    </p>
                    <p class="text-sm text-gray-700">
                        Payment Due: <span class="font-medium">{{ props.invoice.payment_due }}</span>
                    </p>
                </div>

                <div class="text-right min-w-[120px]">
                    <p class="font-semibold text-sm">Currency</p>
                    <p class="text-lg font-bold">{{ props.invoice.currency }}</p>
                </div>
            </div>

            <!-- To Section -->
            <section class="mb-6">
                <h2 class="text-base font-semibold mb-1 border-b border-gray-300 pb-1">To:</h2>
                <p class="text-sm font-medium"><strong>Name:</strong> {{ props.invoice.to.name }}</p>
                <p class="text-sm"><strong>Address:</strong> {{ props.invoice.to.address }}</p>
                <p v-if="props.invoice.to.tax_registration_number" class="text-sm">
                    <strong>TRN:</strong> {{ props.invoice.to.tax_registration_number }}
                </p>
            </section>

            <!-- Services Table -->
            <section v-if="props.invoice.services && props.invoice.services.length" class="mb-6">
                <h2 class="text-base font-semibold mb-2 border-b border-gray-300 pb-1">
                    Services
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="pb-2 text-left text-sm">Service</th>
                                <th class="pb-2 text-left text-sm">Description</th>
                                <th class="pb-2 text-right text-sm">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="service in props.invoice.services" :key="service.id"
                                class="border-b border-gray-300">
                                <td class="py-1 text-sm">{{ service.name }}</td>
                                <td class="py-1 text-sm">{{ service.description }}</td>
                                <td class="py-1 text-right text-sm">
                                    {{ formatCurrency(service.unit_price, props.invoice.currency) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-2 flex justify-end font-semibold text-sm">
                    Total: {{ formatCurrency(totalServices, props.invoice.currency) }}
                </div>
            </section>
        </div>

        <!-- From Section fixado no bottom -->
        <section class="mt-auto mb-0">
            <h2 class="text-base font-semibold mb-1 border-b border-gray-300 pb-1">From:</h2>
            <p class="text-sm font-medium"><strong>Name:</strong> {{ fromDetails.name }}</p>
            <p class="text-sm"><strong>Address:</strong> {{ fromDetails.address }}</p>
            <p class="text-sm"><strong>Email:</strong> {{ fromDetails.email }}</p>
            <p class="text-sm"><strong>Phone:</strong> {{ fromDetails.phone }}</p>
            <p class="text-sm"><strong>Bank Details:</strong> {{ fromDetails.bank_details }}</p>
        </section>
    </div>
</template>

<style scoped>
@media print {
    @page {
        margin: 0;
        size: A4 portrait;
    }

    body {
        margin: 0;
        -webkit-print-color-adjust: exact;
    }
}
</style>
