<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    service: Object,
    documents: Array,
});

const documents = ref(props.documents ?? []);
const service = props.service;

const showModal = ref(false);

const form = useForm({
    name: '',
    type: 'client',
    client_type: 'main',
});

const openModal = () => {
    form.reset();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const submit = () => {
    form.post(route('admin.documents.store', service.id), {
        onSuccess: () => {
            router.get(route('admin.services.show', service.id), {
                preserveState: true,
                only: ['documents'],
                onSuccess: (page) => {
                    documents.value = page.props.documents || [];
                },
            });
            closeModal();
        },
    });
};

const searchQuery = ref('');

const filteredDocuments = computed(() => {
    const q = searchQuery.value.toLowerCase();
    return documents.value.filter(doc =>
        doc.name.toLowerCase().includes(q) ||
        doc.type.toLowerCase().includes(q) ||
        doc.client_type.toLowerCase().includes(q)
    );
});
</script>

<template>
    <AdminLayout :title="`Service: ${service.name}`">
        <template #header>
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">
                Service Details
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4">

                    <!-- Service Info -->
                    <section class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">Name</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ service.name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            Created at: {{ new Date(service.created_at).toLocaleString() }}
                        </p>
                    </section>

                    <!-- Filtro e Tabela -->
                    <section>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Documents</h3>
                            <PrimaryButton @click="openModal" class="mb-4">
                                Add New Document
                            </PrimaryButton>
                        </div>

                        <!-- Campo de busca -->
                        <div class="mb-3">
                            <input v-model="searchQuery" type="text" placeholder="Search by name, type or client type"
                                class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-800 dark:text-white text-sm" />
                        </div>

                        <!-- Tabela -->
                        <div v-if="filteredDocuments.length === 0"
                            class="text-gray-500 dark:text-gray-400 text-sm italic">
                            No matching documents.
                        </div>

                        <div v-else class="overflow-auto">
                            <table
                                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-300 dark:border-gray-600 text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300 border-r">
                                            Name</th>
                                        <th
                                            class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300 border-r">
                                            Type</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">
                                            Client Type
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="doc in filteredDocuments" :key="doc.id"
                                        class="bg-white dark:bg-gray-800">
                                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100 border-r">{{ doc.name }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100 border-r capitalize">{{
                                            doc.type
                                        }}</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100 capitalize">{{
                                            doc.client_type.replace('_', ' ') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Back -->
                    <div class="mt-8">
                        <Link href="/admin/services"
                            class="inline-block text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-600 text-sm font-semibold">
                        &larr; Back to list
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <transition name="fade">
            <div v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full p-5 mx-4 relative"
                    @click.stop>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">New Document</h3>
                    <form @submit.prevent="submit" novalidate>
                        <input v-model="form.name" type="text" placeholder="Document name"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white text-sm"
                            required />
                        <select v-model="form.type"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white text-sm">
                            <option value="client">Client</option>
                            <option value="company">Company</option>
                        </select>
                        <select v-model="form.client_type"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white text-sm">
                            <option value="main">Main</option>
                            <option value="spouse">Spouse</option>
                            <option value="child_1">Child 1</option>
                            <option value="child_2">Child 2</option>
                            <option value="child_3">Child 3</option>
                            <option value="child_4">Child 4</option>
                        </select>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeModal"
                                class="px-4 py-1.5 rounded-md bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-1.5 rounded-md bg-green-600 hover:bg-green-700 text-white font-semibold text-sm"
                                :disabled="form.processing">
                                Save
                            </button>
                        </div>
                    </form>

                    <!-- Close button -->
                    <button @click="closeModal"
                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
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
