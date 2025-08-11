<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    users: Array,
    nextInvoiceNumber: Number,
});

const toType = ref('user');

const selectedUserId = ref(null);
const selectedClientId = ref(null);

const searchUser = ref('');
const searchClient = ref('');

const form = useForm({
    invoice_number: props.nextInvoiceNumber?.toString() || '',
    // removido date
    payment_due: '',
    currency: 'AED',
    to_type: toType.value,
    user_id: null,
    client_id: null,
    to_name: '',
    // removido to_address
    type: '',
    description: '', // novo campo description
});

watch(toType, (val) => {
    form.to_type = val;

    selectedUserId.value = null;
    selectedClientId.value = null;

    form.user_id = null;
    form.client_id = null;
    form.to_name = '';
    // removido to_address
    form.description = '';
});

watch(selectedUserId, (val) => {
    if (toType.value === 'user' && val) {
        const user = props.users.find(u => u.id === val);
        form.user_id = val;
        form.client_id = null;
        form.to_name = user?.name || '';
    } else if (toType.value === 'client') {
        selectedClientId.value = null;
        form.client_id = null;
        form.to_name = '';
    }
});

watch(selectedClientId, (val) => {
    if (toType.value === 'client' && val) {
        const user = props.users.find(u => u.id === selectedUserId.value);
        const client = user?.clients.find(c => c.id === val);
        form.client_id = val;
        form.user_id = selectedUserId.value;
        form.to_name = client?.name || '';
    }
});

const filteredUsers = computed(() => {
    if (!searchUser.value) return props.users;
    return props.users.filter(u =>
        u.name.toLowerCase().includes(searchUser.value.toLowerCase())
    );
});

const clientsForSelectedUser = computed(() => {
    const user = props.users.find(u => u.id === selectedUserId.value);
    if (!user) return [];
    if (!searchClient.value) return user.clients || [];
    return (user.clients || []).filter(c =>
        c.name.toLowerCase().includes(searchClient.value.toLowerCase())
    );
});

function submit() {
    if (toType.value === 'user') {
        form.user_id = selectedUserId.value;
        form.client_id = null;
    } else {
        form.client_id = selectedClientId.value;
    }

    form.post(route('invoices.store'), {
        onError: () => { },
        onFinish: () => { },
        onSuccess: () => { }
    });
}
</script>

<template>
    <AdminLayout title="Create Invoice">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create Invoice
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 space-y-6">

                    <!-- Invoice Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Invoice Type
                        </label>
                        <select v-model="toType"
                            class="block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="user">B2B (User)</option>
                            <option value="client">B2C (Client)</option>
                        </select>
                    </div>

                    <!-- B2B User Select -->
                    <div v-if="toType === 'user'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            User
                        </label>
                        <SearchableSelect v-model="selectedUserId" :options="filteredUsers" label-key="name"
                            value-key="id" placeholder="Search users..." />
                    </div>

                    <!-- B2C Client Select -->
                    <div v-if="toType === 'client'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Select User (Owner)
                        </label>
                        <SearchableSelect v-model="selectedUserId" :options="props.users" label-key="name"
                            value-key="id" placeholder="Search users..." />

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4 mb-1">
                            Client
                        </label>
                        <SearchableSelect v-model="selectedClientId" :options="clientsForSelectedUser" label-key="name"
                            value-key="id" placeholder="Search clients..." />
                    </div>

                    <!-- Invoice Details -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Removido To Address -->

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
                            <select v-model="form.currency"
                                class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                                <option value="">Select currency</option>
                                <option value="AED">AED</option>
                                <option value="USD">USD</option>
                                <option value="AUD">AUD</option>
                                <option value="EGP">EGP</option>
                                <option value="TRY">TRY</option>
                            </select>
                        </div>

                        <!-- Removido Invoice Date -->

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment
                                Due</label>
                            <input v-model="form.payment_due" type="date"
                                class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" />
                        </div>

                        <!-- Novo campo description, full width -->
                        <div class="col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <input v-model="form.description" type="text"
                                class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" />
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Company
                            </label>
                            <select v-model="form.type"
                                class="block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                                <option value="">Select company</option>
                                <option value="bondandpartners">Bond and Partners</option>
                                <option value="sheikhdom">Sheikhdom</option>
                            </select>
                            <p v-if="form.errors.type" class="text-red-600 text-sm mt-1">{{ form.errors.type }}</p>
                        </div>
                    </div>

                    <PrimaryButton @click="submit" :disabled="form.processing">
                        Create Invoice
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
