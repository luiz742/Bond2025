<script setup>
import { computed } from 'vue'
import { bankDetails } from './plugins/bankDetails'  // ✅ import do arquivo externo

// ✅ Props
const props = defineProps({
    invoice: Object,
})

// ✅ Reatividade para show_conversion
const showConversion = computed(() => props.invoice?.show_conversion ?? false)

// ✅ Pega os detalhes bancários conforme a moeda da invoice
const account = computed(() => {
    const currency = props.invoice?.currency || 'AED'
    return bankDetails[currency] || bankDetails['AED']
})

// ✅ Função de formatação de moeda
function formatCurrency(value, currency = 'USD') {
    if (typeof value !== 'number') value = parseFloat(value) || 0
    return value.toLocaleString(undefined, {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })
}

// ✅ Soma dos serviços (unit_price)
const totalServices = computed(() => {
    return props.invoice?.services
        ? props.invoice.services.reduce((sum, service) => sum + parseFloat(service.unit_price || 0), 0)
        : 0
})

// ✅ Soma dos serviços convertidos em USD
const totalConvertedUSD = computed(() => {
    return props.invoice?.services
        ? props.invoice.services.reduce((sum, service) => {
            return sum + (parseFloat(service.total_usd) || 0)
        }, 0)
        : 0
})

// ✅ Formatação do bond_tax
const formatBondTax = (value) => {
    if (!value) return ''
    let formatted = value.replace(/_/g, ' ')
    formatted = formatted.replace(/\b\w/g, c => c.toUpperCase())
    return formatted
}
</script>


<template>
    <div class="bg-white p-10 font-sans mx-auto shadow-lg border border-gray-200
                flex flex-col" style="width: 794px; height: 1123px;">

        <!-- Conteúdo principal flex-grow -->
        <div class="flex-grow flex flex-col">

            <!-- Top header -->
            <div class="flex justify-between items-start mb-6">
                <img src="/images/logo.png" alt="Logo" class="h-12 w-auto" />
                <div class="text-right text-sm">
                    <p class="font-semibold text-lg mb-4">{{ formatBondTax(props.invoice.bond_tax) }}</p>
                    <p class="font-semibold">Invoice Number</p>
                    <p>{{ props.invoice.invoice_number }}</p>
                </div>
            </div>

            <!-- Company details -->
            <div class="mb-6">
                <h1 class="font-bold text-base">Bond And Partners Corporate Service Providers L.L.C</h1>
                <p class="text-sm">The PRISM Tower, Office number 2607, Business Bay, Dubai, U.A.E.</p>
                <p class="text-sm">www.bondandpartners.com</p>
                <p class="text-sm">info@bondandpartners.com</p>
            </div>

            <!-- Bill To & dates -->
            <div class="flex justify-between mb-6">
                <div>
                    <p class="font-semibold text-sm">Bill To</p>
                    <p class="text-sm font-bold">{{ props.invoice.to.name }}</p>
                    <p class="text-sm">{{ props.invoice.to.address }}</p>
                </div>
                <div class="text-sm">
                    <p><strong>Issue Date:</strong> {{ props.invoice.date }}</p>
                    <p><strong>Due Date:</strong> {{ props.invoice.payment_due }}</p>
                </div>
            </div>

            <!-- Services table -->
            <table class="min-w-full border border-gray-300 text-sm mb-4">
                <thead>
                    <tr class="bg-[#0b2d6e] text-white">
                        <th class="text-left py-2 px-3">Service Description</th>
                        <th class="text-right py-2 px-3">Price in {{ props.invoice.currency }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="service in props.invoice.services" :key="service.id" class="border-t border-gray-300">
                        <td class="py-2 px-3">{{ service.name }}</td>
                        <td class="text-right py-2 px-3">
                            {{ formatCurrency(service.unit_price, props.invoice.currency) }}
                        </td>
                    </tr>
                    <!-- Grand Total row -->
                    <tr class="bg-gray-100 font-semibold">
                        <td class="py-2 px-3">Grand Total</td>
                        <td class="text-right py-2 px-3">
                            {{ formatCurrency(props.invoice.grand_total || totalServices, props.invoice.currency) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- USD to AED -->
            <div v-if="showConversion" class="text-sm mb-6">
                <strong>Total in USD:</strong> {{ formatCurrency(totalConvertedUSD, 'USD') }}
            </div>

            <!-- Seção final (detalhes da conta + estampa) -->
            <div class="mt-auto mb-8 flex items-center justify-between">

                <!-- Exibir dados bancários -->
                <div class="text-sm">
                    <p class="font-semibold">Account Details:</p>
                    <div v-for="(value, key) in account" :key="key">
                        <p><strong>{{ key }}:</strong> {{ value }}</p>
                    </div>
                </div>

                <!-- Estampa -->
                <div>
                    <img src="/images/stamp.png" alt="Estampa" class="h-[200px]" />
                </div>
            </div>
        </div> <!-- fim flex-grow -->

        <!-- Footer fixo no bottom -->
        <footer class="border-t-4 border-[#0b2d6e] pt-3 text-center">
            <img src="/images/logo.png" alt="Footer Logo" class="h-8 w-auto mx-auto" />
        </footer>
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

thead tr {
    background-color: #0b2d6e !important;
    color: white !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

tfoot tr {
    background-color: #0b2d6e !important;
    color: white !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
</style>
