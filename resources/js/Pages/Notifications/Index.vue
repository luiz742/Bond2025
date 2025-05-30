<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    notifications: Array,
});

// Usamos uma ref local para poder atualizar o estado sem reload
const notifications = ref(props.notifications);

// Função para marcar a notificação como lida
async function markAsRead(id) {
    try {
        await axios.post(`/notifications/${id}/read`);
        const notif = notifications.value.find(n => n.id === id);
        if (notif) notif.read = true;
    } catch (error) {
        alert('Error marking notification as read');
    }
}
</script>

<template>
    <AdminLayout title="Notifications">
        <template #header>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                Notifications
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg divide-y divide-gray-200 dark:divide-gray-700">
                    <div v-if="notifications.length === 0"
                        class="p-8 text-center text-gray-500 dark:text-gray-400 text-lg italic select-none">
                        No notifications yet.
                    </div>

                    <ul v-else>
                        <li v-for="notification in notifications" :key="notification.id"
                            class="p-6 transition rounded-lg cursor-pointer flex justify-between items-start" :class="notification.read
                                ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
                                : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold shadow-md'">
                            <a :href="route('notifications.redirect', notification.id)"
                                class="flex-1 min-w-0 flex items-center gap-3">
                                <!-- Ícone círculo vermelho para notificações não lidas -->
                                <span v-if="!notification.read"
                                    class="inline-block w-3 h-3 rounded-full bg-red-500 animate-pulse"
                                    aria-label="Unread notification"></span>

                                <div>
                                    <p>
                                        <template v-if="notification.type === 'client_created'">
                                            New client created: <span class="text-indigo-600 dark:text-indigo-400">{{
                                                notification.client?.name }}</span>
                                        </template>

                                        <template v-else-if="notification.type === 'document_uploaded'">
                                            New document uploaded for: <span
                                                class="text-indigo-600 dark:text-indigo-400">{{
                                                notification.client?.name }}</span>
                                            <span v-if="notification.document"
                                                class="ml-2 text-sm italic text-gray-600 dark:text-gray-400">
                                                — {{ notification.document.name }}
                                            </span>
                                        </template>
                                    </p>

                                    <p v-if="notification.user" class="mt-1 text-sm">
                                        By: <span class="font-medium">{{ notification.user.name }}</span>
                                    </p>
                                    <p v-if="notification.service" class="mt-0.5 text-sm">
                                        Service: <span class="font-medium">{{ notification.service.name }}</span>
                                    </p>
                                </div>
                            </a>

                            <time class="ml-4 flex-shrink-0 whitespace-nowrap text-sm"
                                :class="notification.read ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400 dark:text-gray-500'"
                                :datetime="notification.created_at"
                                :title="new Date(notification.created_at).toLocaleString()">
                                {{ new Date(notification.created_at).toLocaleDateString() }}
                            </time>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
