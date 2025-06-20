<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
  user: Object,
  services: Array,
})

// Form principal para editar usuário
const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  role: props.user.role,
  service_id: props.user.service_id || null,
})

// Form só para upload do agreement
const agreementForm = useForm({
  service_agreement: null,
})

const showAgreementModal = ref(false)
const fileInput = ref(null)

// Enviar dados do usuário (exceto agreement)
const submitUser = () => {
  form.put(route('admin.users.update', props.user.id))
}

// Atualizar arquivo no form separado usando .set()
const handleAgreementFileChange = (e) => {
  try {
    const file = e.target.files[0] || null
    console.log('Arquivo selecionado:', file)
    agreementForm.service_agreement = file
  } catch (error) {
    console.error('Erro no handleAgreementFileChange:', error)
  }
}

const submitAgreement = async () => {
  try {
    const file = agreementForm.service_agreement
    console.log('Enviando arquivo:', file)

    if (!file) {
      alert('Please select a file')
      return
    }

    await agreementForm.post(route('admin.users.uploadAgreement', props.user.id), {
      forceFormData: true,
      preserveState: true,
      onSuccess: () => {
        showAgreementModal.value = false
        agreementForm.reset()
        if (fileInput.value) fileInput.value.value = ''
      },
    })
  } catch (error) {
    console.error('Erro ao enviar agreement:', error)
    alert('Erro ao enviar arquivo. Veja o console para detalhes.')
  }
}

</script>

<template>
  <AdminLayout title="Edit User">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit User</h2>
        <PrimaryButton @click="showAgreementModal = true">Upload Agreement</PrimaryButton>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Form principal do usuário -->
        <form
          @submit.prevent="submitUser"
          class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-md shadow"
        >
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >User Name</label
            >
            <input
              v-model="form.name"
              type="text"
              class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white"
            />
            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
              {{ form.errors.name }}
            </div>
          </div>

          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Email</label
            >
            <input
              v-model="form.email"
              type="email"
              class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white"
            />
            <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
              {{ form.errors.email }}
            </div>
          </div>

          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Password</label
            >
            <input
              v-model="form.password"
              type="password"
              placeholder="Leave blank to keep current"
              class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white"
            />
            <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">
              {{ form.errors.password }}
            </div>
          </div>

          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Role</label
            >
            <select
              v-model="form.role"
              class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white"
            >
              <option value="admin">Admin</option>
              <option value="b2b">B2B</option>
              <option value="super_admin">Super Admin</option>
            </select>
            <div v-if="form.errors.role" class="text-red-600 text-sm mt-1">
              {{ form.errors.role }}
            </div>
          </div>

          <div v-if="props.user.role === 'admin'">
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >Program Allowed</label
            >
            <select
              v-model="form.service_id"
              class="mt-1 w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-white"
            >
              <option value="">-- Select a service --</option>
              <option v-for="service in services" :key="service.id" :value="service.id">
                {{ service.name }}
              </option>
            </select>
            <div v-if="form.errors.service_id" class="text-red-600 text-sm mt-1">
              {{ form.errors.service_id }}
            </div>
          </div>

          <div class="flex justify-end">
            <PrimaryButton type="submit" :disabled="form.processing">Update</PrimaryButton>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal para upload do agreement -->
    <div
      v-if="showAgreementModal"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
      <div
        class="bg-white dark:bg-gray-800 p-6 rounded shadow max-w-md w-full"
      >
        <h3
          class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100"
        >
          Upload Service Agreement (PDF)
        </h3>
        <input
          ref="fileInput"
          type="file"
          accept="application/pdf"
          @change="handleAgreementFileChange"
        />
        <div v-if="agreementForm.errors.service_agreement" class="text-red-600 text-sm mt-1">
          {{ agreementForm.errors.service_agreement }}
        </div>
        <div class="mt-4 flex justify-end space-x-2">
          <PrimaryButton
            @click="showAgreementModal = false"
            class="bg-gray-500 hover:bg-gray-600"
            :disabled="agreementForm.processing"
          >
            Cancel
          </PrimaryButton>
          <PrimaryButton
            @click="submitAgreement"
            :disabled="agreementForm.processing"
          >
            Upload
          </PrimaryButton>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
