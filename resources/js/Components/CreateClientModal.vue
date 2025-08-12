<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
  show: Boolean,
  user: Object,
  users: Array,
  services: Array,
})

const emit = defineEmits(['update:show', 'created'])

const form = useForm({
  name: '',
  service_id: '',
  user_id: props.user?.id || null,
})

const showSlot = ref(props.show)

watch(() => props.show, (val) => {
  showSlot.value = val
  if (val) {
    form.reset({ user_id: props.user?.id || null })
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = null
  }
})

const submit = () => {
  form.post(route('clients.store'), {
    onSuccess: () => {
      emit('created')
      emit('update:show', false)
      form.reset({ user_id: props.user?.id || null })
      document.body.style.overflow = null
    },
  })
}

const close = () => {
  emit('update:show', false)
  document.body.style.overflow = null
}
</script>

<template>
  <transition name="fade">
    <div v-if="showSlot" class="fixed inset-0 z-50 flex items-center justify-center">
      <!-- Overlay -->
      <div
        class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"
        @click="close"
        aria-hidden="true"
      ></div>

      <!-- Modal Content -->
      <div
        class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-2xl mx-4 sm:mx-auto z-10"
        @click.stop
      >
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            New Client for {{ user?.name || 'Select user' }}
          </h2>
          <button
            @click="close"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-2xl font-bold leading-none"
            aria-label="Close modal"
            type="button"
          >
            &times;
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-6">
          <div v-if="users && users.length > 0">
            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              User
            </label>
            <select
              id="user_id"
              v-model="form.user_id"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                     focus:border-indigo-500 focus:ring-indigo-500
                     dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
            >
              <option disabled value="">Select a user</option>
              <option v-for="u in users" :key="u.id" :value="u.id">
                {{ u.name }}
              </option>
            </select>
          </div>

          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                     focus:border-indigo-500 focus:ring-indigo-500
                     dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
            />
          </div>

          <div>
            <label for="service_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service</label>
            <select
              id="service_id"
              v-model="form.service_id"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                     focus:border-indigo-500 focus:ring-indigo-500
                     dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
            >
              <option disabled value="">Select a service</option>
              <option v-for="service in services" :key="service.id" :value="service.id">
                {{ service.name }}
              </option>
            </select>
          </div>

          <PrimaryButton :disabled="form.processing" :class="{ 'opacity-25 cursor-not-allowed': form.processing }">
            Submit
          </PrimaryButton>
        </form>
      </div>
    </div>
  </transition>
</template>
