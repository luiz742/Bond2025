<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

// Props
const props = defineProps({
  quote: Object,
  users: Array,
})

// Formulário principal do quote
const form = useForm({
  quote_number: props.quote.quote_number,
  date: props.quote.date,
  valid_until: props.quote.valid_until,
  currency: props.quote.currency,
  client_id: props.quote.client_id,
  description: props.quote.description ?? '',
  user_id: props.quote.user_id,
})

// Submissão do formulário para atualizar quote
const submit = () => {
  form.put(`/quotes/${props.quote.id}`, {
    onSuccess: () => {
      // sucesso
    },
    onError: () => {
      // erro
    },
  })
}

// Modal de criação de serviço
const showCreateServiceModal = ref(false)

const serviceForm = useForm({
  name: '',
  description: '',
  unit_price: '',
  total: 0,
  quantity: 1,
  quote_id: props.quote.id,
})

watch(() => serviceForm.unit_price, () => {
  const price = parseFloat(serviceForm.unit_price)
  const qty = parseInt(serviceForm.quantity)
  serviceForm.total = !isNaN(price) && !isNaN(qty) ? (price * qty).toFixed(2) : '0.00'
})

// Abrir e fechar modal
const openCreateServiceModal = () => {
  serviceForm.reset()
  showCreateServiceModal.value = true
}
const closeCreateServiceModal = () => {
  showCreateServiceModal.value = false
}

// Enviar novo serviço
const submitServiceCreate = () => {
  serviceForm.post('/quote-services', {
    onSuccess: () => {
      closeCreateServiceModal()
      window.location.reload()
    },
  })
}

// Formatação de moeda
const formatCurrency = (value) => {
  if (isNaN(value)) return ''
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: form.currency || 'USD',
    minimumFractionDigits: 2,
  }).format(value)
}

// Total do quote (soma dos serviços)
const totalQuoteAmount = () => {
  if (!props.quote.services) return 0
  return props.quote.services.reduce((sum, s) => sum + parseFloat(s.total || 0), 0)
}
</script>

<template>
  <AdminLayout :title="`Edit Quote #${form.quote_number}`">
    <template #header>
      <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
        Edit Quote #{{ form.quote_number }}
      </h2>
    </template>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Formulário Quote -->
      <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-8 mb-10">
        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Quote Number -->
          <div>
            <label for="quote_number" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
              Quote Number
            </label>
            <input id="quote_number" v-model="form.quote_number" disabled
              class="w-full rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>

          <!-- Client -->
          <div>
            <label for="client_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Client</label>
            <select id="client_id" v-model="form.client_id"
              class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option v-for="client in props.quote.client ? [props.quote.client] : []" :key="client.id" :value="client.id">
                {{ client.name }}
              </option>
              <!-- Se quiser listar mais clients, passe na props -->
            </select>
          </div>

          <!-- Date -->
          <div>
            <label for="date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Date</label>
            <input id="date" type="date" v-model="form.date"
              class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>

          <!-- Valid Until -->
          <div>
            <label for="valid_until" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Valid Until</label>
            <input id="valid_until" type="date" v-model="form.valid_until"
              class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>

          <!-- Currency -->
          <div>
            <label for="currency" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Currency</label>
            <select id="currency" v-model="form.currency"
              class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Select currency</option>
              <option value="AED">AED</option>
              <option value="USD">USD</option>
              <option value="AUD">AUD</option>
              <option value="EGP">EGP</option>
              <option value="TRY">TRY</option>
            </select>
          </div>

          <!-- Description full width -->
          <div class="md:col-span-2">
            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
              Description
            </label>
            <input id="description" type="text" v-model="form.description" placeholder="Add a brief description"
              class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>

          <!-- Submit -->
          <div class="md:col-span-2 text-right mt-4">
            <PrimaryButton :disabled="form.processing" class="inline-flex items-center space-x-2">
              <svg class="w-5 h-5 animate-spin" v-if="form.processing" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
              </svg>
              <span>{{ form.processing ? 'Saving...' : 'Save Changes' }}</span>
            </PrimaryButton>
          </div>
        </form>
      </div>

      <!-- Services Section -->
      <section class="bg-white dark:bg-gray-900 rounded-lg shadow p-8">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Services</h3>
          <PrimaryButton @click="openCreateServiceModal" class="flex items-center space-x-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Service</span>
          </PrimaryButton>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Service</th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Qty</th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Unit Price</th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Total</th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Created At</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="service in quote.services || []" :key="service.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ service.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ service.quantity }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ formatCurrency(parseFloat(service.unit_price)) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ formatCurrency(parseFloat(service.total)) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ new Date(service.created_at).toLocaleString() }}</td>
              </tr>
            </tbody>
          </table>

          <p v-if="!(quote.services?.length > 0)" class="text-center py-6 text-gray-500 dark:text-gray-400">
            No services added to this quote.</p>
        </div>

        <!-- Total do quote -->
        <div class="mt-6 text-right text-xl font-semibold text-gray-900 dark:text-gray-100">
          Total Quote Amount: <span class="text-indigo-600">{{ formatCurrency(totalQuoteAmount()) }}</span>
        </div>
      </section>
    </div>

    <!-- Create Service Modal -->
    <transition name="fade">
      <div v-if="showCreateServiceModal" @click.self="closeCreateServiceModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg max-w-md w-full p-6 relative">
          <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Add Service</h3>

          <form @submit.prevent="submitServiceCreate" class="space-y-4">
            <input v-model="serviceForm.name" type="text" placeholder="Service name" required
              class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <textarea v-model="serviceForm.description" placeholder="Description (optional)" rows="3"
              class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            <input v-model="serviceForm.quantity" type="number" min="1" placeholder="Quantity" required
              class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <input v-model="serviceForm.unit_price" type="number" step="0.01" placeholder="Unit Price" required
              class="w-full rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <div class="text-right text-sm text-gray-700 dark:text-gray-300 font-semibold">
              Total: {{ formatCurrency(parseFloat(serviceForm.total)) }}
            </div>

            <div class="flex justify-end space-x-3">
              <button type="button" @click="closeCreateServiceModal"
                class="px-4 py-2 rounded-md bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-medium transition">
                Cancel
              </button>
              <PrimaryButton :disabled="serviceForm.processing" type="submit" class="px-5 py-2">
                Save
              </PrimaryButton>
            </div>
          </form>

          <button @click="closeCreateServiceModal"
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-lg font-bold"
            aria-label="Close modal">
            ×
          </button>
        </div>
      </div>
    </transition>
  </AdminLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
