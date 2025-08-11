<script setup>
const props = defineProps({ invoice: Object });

const fromDetails = {
  name: 'SHEIKHDOM DANIŞMANLIK LİMİTED ŞİRKETİ',
  address: 'Gürsel, İmrahor Cd. No: 29 A Blok Daire 125, 34400 Kağıthane/İstanbul, Türkiye',
  phone: '+90 (212) 302 00 81',
  email: 'office@sheikhdom.org',
  website: 'www.sheikhdom.org',
  tax_id: '7692589440',
  trade_reg: '495409-5',
  mersis: '0769258944000001',
  bank: {
    account_name: 'SHEIKHDOM DANIŞMANLIK LİMİTED ŞİRKETİ',
    account_no: '5227528-101',
    iban: 'TR720020600210052275280101',
    swift: 'AFKBTRISXXX',
    branch: 'TAKSİM ŞUBE'
  }
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

const totalAmount = props.invoice.services
  ? props.invoice.services.reduce((sum, service) => sum + parseFloat(service.unit_price || 0), 0)
  : 0;
</script>

<template>
  <div class="w-[794px] mx-auto font-serif text-[13px] leading-relaxed text-black">
    <!-- ======================= PÁGINA 1 ======================= -->
    <div class="bg-white p-10 border border-gray-300 min-h-[1123px] page-break">
      <header class="flex justify-between items-start mb-8">
        <div>
          <img src="https://sheikhdom.org/wp-content/uploads/2023/01/sheikhdom-removebg-preview-e1739362511959.png" alt="Logo" style="width: 70%;"/>
        </div>
        <div class="text-right text-sm">
          <p class="font-semibold">{{ fromDetails.name }}</p>
          <p>{{ fromDetails.address }}</p>
          <p>Landline.: {{ fromDetails.phone }}</p>
          <p>E-mail: {{ fromDetails.email }}</p>
          <p>{{ fromDetails.website }}</p>
          <p class="mt-2">Kağıthane V.D (Tax ID).: {{ fromDetails.tax_id }}</p>
          <p>Trade Registration No.: {{ fromDetails.trade_reg }}</p>
          <p>Mersis: {{ fromDetails.mersis }}</p>
        </div>
      </header>

      <h1 class="text-center text-[15px] font-bold mb-4 uppercase">INVOICE</h1>

      <div class="flex justify-between text-sm mb-4">
        <div>
          <p class="font-bold">BILL TO:</p>
          <p class="font-medium">{{ props.invoice.to.name }}</p>
          <p>{{ props.invoice.to.address }}</p>
          <p v-if="props.invoice.to.license">License No.: {{ props.invoice.to.license }}</p>
        </div>
        <div class="text-right">
          <p><strong>INVOICE NO.:</strong> {{ props.invoice.invoice_number }}</p>
          <p><strong>INVOICE DATE:</strong> {{ props.invoice.date }}</p>
        </div>
      </div>

      <div class="mb-4 text-sm">
        <p class="font-bold">BILL FROM:</p>
        <p>{{ fromDetails.name }}</p>
        <p>{{ fromDetails.address }}</p>
        <p>Vergi No. / Tax ID: {{ fromDetails.tax_id }}</p>
      </div>

      <div class="mb-4 text-sm">
        <p class="font-bold">PURPOSE:</p>
        <p>{{ props.invoice.purpose }}</p>
      </div>

      <table class="w-full border border-black text-xs mb-4">
        <thead>
          <tr class="bg-gray-100">
            <th class="border border-black p-1 text-left">SERVICE DESCRIPTION</th>
            <th class="border border-black p-1 text-left">CURRENCY</th>
            <th class="border border-black p-1 text-right">TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(service, idx) in props.invoice.services" :key="idx">
            <td class="border border-black px-2 py-1">{{ service.name }}</td>
            <td class="border border-black px-2 py-1">USD</td>
            <td class="border border-black px-2 py-1 text-right">{{ formatCurrency(service.unit_price, 'USD') }}</td>
          </tr>
        </tbody>
      </table>

      <div class="w-full flex justify-end">
        <table class="border border-black w-[260px] text-xs">
          <tr>
            <td class="border border-black px-2 py-1 font-bold text-right">SUBTOTAL</td>
            <td class="border border-black px-2 py-1 text-right">{{ formatCurrency(totalAmount, 'USD') }}</td>
          </tr>
          <tr>
            <td class="border border-black px-2 py-1 font-bold text-right">BALANCE DUE</td>
            <td class="border border-black px-2 py-1 text-right">{{ formatCurrency(totalAmount, 'USD') }}</td>
          </tr>
        </table>
      </div>
    </div>

    <!-- ======================= PÁGINA 2 ======================= -->
    <div class="bg-white p-10 border border-gray-300 min-h-[1123px] page-break">
      <header class="flex justify-between items-start mb-6">
        <img src="https://sheikhdom.org/wp-content/uploads/2023/01/sheikhdom-removebg-preview-e1739362511959.png" alt="Logo" style="width: 30%;"/>
        <div class="text-right text-sm">
          <p class="font-semibold">{{ fromDetails.name }}</p>
          <p>{{ fromDetails.address }}</p>
          <p>Landline.: {{ fromDetails.phone }}</p>
          <p>E-mail: {{ fromDetails.email }}</p>
          <p>{{ fromDetails.website }}</p>
          <p class="mt-2">Kağıthane V.D (Tax ID).: {{ fromDetails.tax_id }}</p>
          <p>Trade Registration No.: {{ fromDetails.trade_reg }}</p>
          <p>Mersis: {{ fromDetails.mersis }}</p>
        </div>
      </header>

      <h2 class="text-center text-lg font-bold mb-4">TERMS & INSTRUCTIONS</h2>

      <ol class="list-decimal pl-6 space-y-2 text-sm">
        <li>
          The balance due on the invoice as stated above shall be deposited into the account of the issuer as per the bank details provided below:
          <div class="mt-2">
            <p><strong>ACCOUNT NAME:</strong> {{ fromDetails.bank.account_name }}</p>
            <p><strong>ACCOUNT NO.:</strong> {{ fromDetails.bank.account_no }}</p>
            <p><strong>IBAN:</strong> {{ fromDetails.bank.iban }}</p>
            <p><strong>SWIFT:</strong> {{ fromDetails.bank.swift }}</p>
            <p><strong>BRANCH:</strong> {{ fromDetails.bank.branch }}</p>
          </div>
        </li>
        <li>This invoice and any information contained herein are confidential and intended only for the use of the named recipient.</li>
        <li>The issuer of the invoice reserves the right to withhold future services or product deliveries until all outstanding balances have been paid in full.</li>
        <li>The issuer reserves the right to correct any errors or omissions on this invoice. Please notify the issuer of any errors and/or incorrect information within 7 days of receipt.</li>
        <li>The recipient of this invoice is responsible for any applicable local, state or federal taxes not expressly set forth in this invoice.</li>
        <li>The issuer is not responsible for any fees or charges associated with and imposed by third-party financial institutions and/or payment processors.</li>
        <li>The issuer shall not be liable for delays or failure to perform due to causes beyond its reasonable control, including but not limited to acts of God, labour disputes, or government actions.</li>
        <li>Any modifications to this invoice must be made in writing and agreed upon by both parties. Unauthorized alterations will render the contract null and void.</li>
      </ol>
    </div>

    <!-- ======================= PÁGINA 3 ======================= -->
    <div class="bg-white p-10 border border-gray-300 min-h-[1123px] relative">
      <header class="flex justify-between items-start mb-6">
        <img src="https://sheikhdom.org/wp-content/uploads/2023/01/sheikhdom-removebg-preview-e1739362511959.png" alt="Logo" style="width: 30%;"/>
        <div class="text-right text-sm">
          <p class="font-semibold">{{ fromDetails.name }}</p>
          <p>{{ fromDetails.address }}</p>
          <p>Landline.: {{ fromDetails.phone }}</p>
          <p>E-mail: {{ fromDetails.email }}</p>
          <p>{{ fromDetails.website }}</p>
          <p class="mt-2">Kağıthane V.D (Tax ID).: {{ fromDetails.tax_id }}</p>
          <p>Trade Registration No.: {{ fromDetails.trade_reg }}</p>
          <p>Mersis: {{ fromDetails.mersis }}</p>
        </div>
      </header>

      <ol start="9" class="list-decimal pl-6 space-y-2 text-sm">
        <li>This electronically generated document is legally binding, valid, and enforceable as an original copy.</li>
        <li>Acceptance of a partial payment does not constitute a waiver of any rights or remedies to collect the full balance due.</li>
        <li>In the event of non-payment, the client agrees to pay all costs of collection, including attorney’s fees, court costs, and other related expenses.</li>
        <li>This invoice and any related transactions shall be governed by the laws of the Republic of Türkiye. Any dispute arising therefrom shall be resolved in accordance with the laws and the courts of the Republic of Türkiye.</li>
      </ol>

      <div class="mt-10 text-right text-sm font-bold">
        Signed & stamped by:
      </div>

      <div class="absolute bottom-10 right-10">
        <img src="/images/stamp-signature.png" alt="Stamp" class="h-28" />
      </div>
    </div>
  </div>
</template>

<style scoped>
@media print {
  @page {
    size: A4;
    margin: 0;
  }
  body {
    margin: 0;
    -webkit-print-color-adjust: exact;
  }
  .page-break {
    page-break-after: always;
  }
}
</style>
