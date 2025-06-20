<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue'; // importe o layout admin
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import KycUploadForm from '@/Pages/Profile/Partials/KycUploadForm.vue';
import { computed } from 'vue';

import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const user = props.auth.user;

// Verifica o role para decidir o layout
const isB2B = computed(() => user.role === 'b2b');
const isAdmin = computed(() => user.role === 'admin' || user.role === 'super_admin');

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

const hasAgreement = computed(() => {
    return isB2B.value && user.service_agreement && user.service_agreement.length > 0;
});
</script>

<template>
    <component :is="isAdmin ? AdminLayout : AppLayout" :title="'Profile'">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Profile
                </h2>

                <a v-if="$page.props.auth.user.role === 'b2b' && $page.props.auth.user.service_agreement"
                    :href="`/storage/${$page.props.auth.user.service_agreement}`" target="_blank" rel="noopener"
                    class="inline-flex items-center space-x-2 px-3 py-1 bg-gray-800 text-white rounded "
                    title="Open Service Agreement">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Open Agreement</span>
                </a>

            </div>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.auth.user.role === 'b2b' && $page.props.jetstream.canUpdateProfileInformation">
                    <KycUploadForm :user="$page.props.auth.user" />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />

                    <!-- <SectionBorder /> -->
                </div>

                <!-- <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" /> -->
            </div>
        </div>
    </component>
</template>
