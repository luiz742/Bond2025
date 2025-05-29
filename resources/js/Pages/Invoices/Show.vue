<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ invoice: Object });
</script>

<template>
  <AppLayout :title="`Invoice ${invoice.invoice_number}`">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Invoice {{ invoice.invoice_number }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-gray-800 dark:text-gray-100">

          <section class="mb-6">
            <h3 class="font-bold">To:</h3>
            <p>{{ invoice.to.name }}</p>
            <p>{{ invoice.to.address }}</p>
            <p v-if="invoice.to.tax_registration_number">TRN: {{ invoice.to.tax_registration_number }}</p>
          </section>

          <section class="mb-6">
            <h3 class="font-bold">From:</h3>
            <p>{{ invoice.from.name }}</p>
            <p>{{ invoice.from.address }}</p>
            <p>Email: {{ invoice.from.email }}</p>
            <p>Phone: {{ invoice.from.phone }}</p>
            <p>Bank Details: {{ invoice.from.bank_details }}</p>
          </section>

          <p><strong>Date:</strong> {{ invoice.date }}</p>
          <p><strong>Payment Due:</strong> {{ invoice.payment_due }}</p>
          <p><strong>Currency:</strong> {{ invoice.currency }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
