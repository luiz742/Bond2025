<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

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

                    <!-- Service Name -->
                    <section class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">Name</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ service.name }}</p>
                    </section>

                    <!-- Created At -->
                    <section class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">Created At</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            {{ new Date(service.created_at).toLocaleString() }}
                        </p>
                    </section>

                    <!-- Documents Section -->
                    <section>
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Documents</h3>
                            <button @click="openModal"
                                class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1 rounded-md transition"
                                aria-label="Add new document">
                                Add New Document
                            </button>
                        </div>

                        <div v-if="documents.length === 0" class="text-gray-500 dark:text-gray-400 italic text-sm mt-3">
                            No documents linked yet.
                        </div>

                        <ul v-else class="mt-3 space-y-2">
                            <li v-for="doc in documents" :key="doc.id"
                                class="p-2 bg-gray-100 dark:bg-gray-800 rounded-md flex justify-between items-center shadow-sm hover:shadow-md transition text-sm">
                                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ doc.name }}</span>
                                <!-- <span class="text-gray-900 dark:text-gray-100 font-medium">
                                    {{ doc.name }}
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400 italic">
                                        ({{ doc.type === 'company' ? 'Company' : 'Cliente' }})
                                    </span>
                                </span> -->
                            </li>
                        </ul>
                    </section>

                    <!-- Back Link -->
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
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                            required />
                        <select v-model="form.type"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                            <option value="client">Client</option>
                            <option value="company">Company</option>
                        </select>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeModal"
                                class="px-4 py-1.5 rounded-md bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-1.5 rounded-md bg-green-600 hover:bg-green-700 text-white font-semibold text-sm transition"
                                :disabled="form.processing">
                                Save
                            </button>
                        </div>
                    </form>

                    <!-- Close Button Top Right -->
                    <button @click="closeModal" aria-label="Close modal"
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
