<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    users: Array,
    nextInvoiceNumber: Number
});

const toType = ref('user'); // 'user' = B2B, 'client' = B2C
const selectedUserId = ref(null);
const selectedClientId = ref(null);
const searchUser = ref('');
const searchClient = ref('');

const form = useForm({
    invoice_number: props.nextInvoiceNumber.toString(),
    date: '',
    payment_due: '',
    currency: 'AED',
    to_type: toType.value,
    user_id: null,
    client_id: null,
    to_name: '',
    to_address: '',
});

// Atualiza o form.to_type quando toType mudar
watch(toType, (val) => {
    form.to_type = val;

    // Resetar IDs e campos
    selectedUserId.value = null;
    selectedClientId.value = null;
    form.user_id = null;
    form.client_id = null;
    form.to_name = '';
    form.to_address = '';
});

// Quando seleciona user (B2B)
watch(selectedUserId, (val) => {
    if (toType.value === 'user' && val) {
        const user = props.users.find(u => u.id === val);
        form.user_id = val;
        form.client_id = null;
        form.to_name = user?.name || '';
        form.to_address = ''; // Preencha se quiser usar endereço do user
    } else if (toType.value === 'client') {
        // Se estiver em modo client e trocar o usuário (dono do cliente), limpar cliente
        selectedClientId.value = null;
        form.client_id = null;
        form.to_name = '';
        form.to_address = '';
    }
});

// Quando seleciona client (B2C)
watch(selectedClientId, (val) => {
    if (toType.value === 'client' && val) {
        const user = props.users.find(u => u.id === selectedUserId.value);
        const client = user?.clients.find(c => c.id === val);
        form.client_id = val;
        form.user_id = selectedUserId.value; // <- Aqui está o fix
        form.to_name = client?.name || '';
        form.to_address = client?.address || '';
    }
});

// Filtra usuários pelo search (B2B)
const filteredUsers = computed(() => {
    if (!searchUser.value) return props.users;
    return props.users.filter(u =>
        u.name.toLowerCase().includes(searchUser.value.toLowerCase())
    );
});

// Filtra clientes do user selecionado pelo search (B2C)
const clientsForSelectedUser = computed(() => {
    const user = props.users.find(u => u.id === selectedUserId.value);
    if (!user) return [];
    if (!searchClient.value) return user.clients;
    return user.clients.filter(c =>
        c.name.toLowerCase().includes(searchClient.value.toLowerCase())
    );
});

function submit() {
    if (toType.value === 'user') {
        form.user_id = selectedUserId.value;
        form.client_id = null;
    } else {
        form.client_id = selectedClientId.value;
        // ✅ NÃO ZERE O user_id aqui!
    }

    form.post(route('invoices.store'), {
        onError: (errors) => { },
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

                    <!-- Tipo de Fatura (B2B ou B2C) -->
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


                    <!-- B2B (toType = 'user') -->
                    <div v-if="toType === 'user'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            User
                        </label>
                        <SearchableSelect v-model="selectedUserId" :options="filteredUsers" label-key="name"
                            value-key="id" placeholder="Search users..." />
                    </div>

                    <!-- B2C (toType = 'client') -->
                    <div v-if="toType === 'client'">
                        <!-- Seleciona o usuário (dono do cliente) -->
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Select User (Owner)
                        </label>
                        <SearchableSelect v-model="selectedUserId" :options="props.users" label-key="name"
                            value-key="id" placeholder="Search users..." />

                        <!-- Seleciona o cliente -->
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4 mb-1">
                            Client
                        </label>
                        <SearchableSelect v-model="selectedClientId" :options="clientsForSelectedUser" label-key="name"
                            value-key="id" placeholder="Search clients..." />
                    </div>


                    <!-- Invoice fields -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Address</label>
                            <input v-model="form.to_address" type="text"
                                class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" />
                            <p v-if="form.errors.to_address" class="text-red-600 text-sm mt-1">{{ form.errors.to_address
                            }}</p>
                        </div>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice
                                Date</label>
                            <input v-model="form.date" type="date"
                                class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment
                                Due</label>
                            <input v-model="form.payment_due" type="date"
                                class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" />
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
