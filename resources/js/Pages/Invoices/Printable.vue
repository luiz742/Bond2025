<script setup>
defineProps({ invoice: Object });

// Dados fixos "From" direto no componente
const fromDetails = {
    name: 'Bond and Partners Corporate Service Providers LLC',
    address: 'Office 101, Business Bay, Dubai, UAE',
    email: 'info@bondandpartners.com',
    phone: '+971 4 123 4567',
    bank_details: 'Bank: Emirates NBD, IBAN: AE070260000000123456789',
};
</script>

<template>
    <div class="text-gray-900 bg-white p-10 font-sans mx-auto shadow-lg border border-gray-200 rounded-md"
        style="width: 794px; min-height: 1123px;">
        <!-- Logo em uma linha -->
        <div class="mb-20">
            <img src="/images/logo.png" alt="Logo" class="h-12 w-auto mx-auto" />
        </div>

        <!-- Texto do cabeçalho abaixo da logo -->
        <div class="flex justify-between items-center mb-8 mt-4 border-b border-gray-300 pb-4">
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wide leading-tight mb-1">
                    Tax Invoice / Proforma Invoice
                </h4>
                <p class="text-sm text-gray-700">
                    Invoice #: <span class="font-medium">{{ invoice.invoice_number }}</span>
                </p>
                <p class="text-sm text-gray-700">
                    Date: <span class="font-medium">{{ invoice.date }}</span>
                </p>
                <p class="text-sm text-gray-700">
                    Payment Due: <span class="font-medium">{{ invoice.payment_due }}</span>
                </p>
            </div>

            <div class="text-right min-w-[120px]">
                <p class="font-semibold text-sm">Currency</p>
                <p class="text-lg font-bold">{{ invoice.currency }}</p>
            </div>
        </div>

        <section class="mb-6">
            <h2 class="text-base font-semibold mb-1 border-b border-gray-300 pb-1">To:</h2>
            <p class="text-sm font-medium"><strong>Name:</strong> {{ invoice.to.name }}</p>
            <p class="text-sm"><strong>Address:</strong> {{ invoice.to.address }}</p>
            <p v-if="invoice.to.tax_registration_number" class="text-sm">
                <strong>TRN:</strong> {{ invoice.to.tax_registration_number }}
            </p>
        </section>

        <section class="mb-6">
            <h2 class="text-base font-semibold mb-1 border-b border-gray-300 pb-1">From:</h2>
            <p class="text-sm font-medium"><strong>Name:</strong> {{ fromDetails.name }}</p>
            <p class="text-sm"><strong>Address:</strong> {{ fromDetails.address }}</p>
            <p class="text-sm"><strong>Email:</strong> {{ fromDetails.email }}</p>
            <p class="text-sm"><strong>Phone:</strong> {{ fromDetails.phone }}</p>
            <p class="text-sm"><strong>Bank Details:</strong> {{ fromDetails.bank_details }}</p>
        </section>

        <!-- Optional Table or Notes -->
        <div class="mt-8 text-xs text-gray-500 italic">
            <!-- <p>This is a system-generated invoice.</p> -->
        </div>
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
