<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    service: Object,
    documents: Array,
});

const documents = ref(props.documents ?? []);
const service = props.service;

const showModal = ref(false);
const isEditing = ref(false);
const editingDocument = ref(null);

const form = useForm({
    name: '',
    type: 'client',
    client_type: [],
});

const openModal = () => {
    form.reset();
    isEditing.value = false;
    editingDocument.value = null;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    isEditing.value = false;
    editingDocument.value = null;
};

const editDocument = (doc) => {
    isEditing.value = true;
    editingDocument.value = doc;
    form.name = doc.name;
    form.type = doc.type;
    form.client_type = [...doc.client_types];
    showModal.value = true;
};

const refreshDocuments = () => {
    router.get(route('admin.services.show', service.id), {
        preserveState: true,
        only: ['documents'],
        onSuccess: (page) => {
            documents.value = page.props.documents || [];
        },
    });
};

const submit = () => {
    if (isEditing.value && editingDocument.value) {
        form.put(route('admin.documents.updateGrouped', {
            service: service.id,
            name: editingDocument.value.name,
            type: editingDocument.value.type,
        }), {
            onSuccess: () => {
                refreshDocuments();
                closeModal();
            },
        });
    } else {
        form.post(route('admin.documents.store', service.id), {
            onSuccess: () => {
                refreshDocuments();
                closeModal();
            },
        });
    }
};

const searchQuery = ref('');

const filteredDocuments = computed(() => {
    const q = searchQuery.value.toLowerCase();
    const grouped = {};

    for (const doc of documents.value) {
        const key = `${doc.name}__${doc.type}`;
        if (!grouped[key]) {
            grouped[key] = {
                id: doc.id,
                name: doc.name,
                type: doc.type,
                client_types: [],
            };
        }
        grouped[key].client_types.push(doc.client_type);
    }

    return Object.values(grouped)
    .filter(doc =>
        doc.name.toLowerCase().includes(q) ||
        doc.type.toLowerCase().includes(q) ||
        doc.client_types.some(ct => ct.toLowerCase().includes(q))
    )
    .sort((a, b) => a.name.localeCompare(b.name));
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

                    <!-- Tabela -->
                    <section>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Documents</h3>
                            <PrimaryButton @click="openModal">Add New Document</PrimaryButton>
                        </div>

                        <input v-model="searchQuery" type="text" placeholder="Search"
                            class="w-full mb-4 p-2 border border-gray-300 rounded-md dark:bg-gray-800 dark:text-white text-sm" />

                        <div v-if="filteredDocuments.length === 0"
                            class="text-gray-500 dark:text-gray-400 text-sm italic">No matching documents.</div>

                        <div v-else class="overflow-auto">
                            <table
                                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-300 dark:border-gray-600 text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Name</th>
                                        <th class="px-4 py-2 text-left">Type</th>
                                        <th class="px-4 py-2 text-left">Client Types</th>
                                        <th class="px-4 py-2 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="doc in filteredDocuments" :key="doc.name + doc.type"
                                        class="bg-white dark:bg-gray-800">
                                        <td class="px-4 py-2">{{ doc.name }}</td>
                                        <td class="px-4 py-2 capitalize">{{ doc.type }}</td>
                                        <td class="px-4 py-2 capitalize">
                                            {{ doc.client_types.map(ct => ct.replace('_', ' ')).join(', ') }}
                                        </td>
                                        <td class="px-4 py-2">
                                            <button @click="editDocument(doc)"
                                                class="text-blue-600 hover:underline text-sm">Edit</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <div class="mt-8">
                        <Link href="/admin/services"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-600 text-sm font-semibold">
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
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full p-5 mx-4 relative">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                        {{ isEditing ? 'Edit Document' : 'New Document' }}
                    </h3>
                    <form @submit.prevent="submit" novalidate>
                        <input v-model="form.name" type="text" placeholder="Document name"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white text-sm"
                            required />

                        <select v-model="form.type"
                            class="w-full p-2 border border-gray-300 rounded-md mb-4 dark:bg-gray-800 dark:text-white text-sm">
                            <option value="client">Client</option>
                            <option value="company">Company</option>
                        </select>

                        <!-- Checkboxes -->
                        <div class="mb-4">
                            <label class="block mb-1 text-sm text-gray-700 dark:text-gray-200">Client Types</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label v-for="type in ['main', 'spouse', 'child_1', 'child_2', 'child_3', 'child_4']"
                                    :key="type"
                                    class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" :value="type" v-model="form.client_type"
                                        class="rounded border-gray-300 dark:bg-gray-800 dark:border-gray-600" />
                                    <span class="capitalize">{{ type.replace('_', ' ') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeModal"
                                class="px-4 py-1.5 rounded-md bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-1.5 rounded-md bg-green-600 hover:bg-green-700 text-white font-semibold text-sm"
                                :disabled="form.processing">
                                {{ isEditing ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>

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
