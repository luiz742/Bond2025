<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const pdfFiles = [
    { name: 'NBV Standard VDSP Checklist', url: '/images/NBVSTANDARDVDSPCHECKLIST.pdf' },
    { name: 'Passport Application Form', url: '/images/PASSPORTAPPLICATIONFOR.pdf' },
    { name: 'Asset Report', url: '/images/ASSET-REPORT.pdf' },
    { name: 'Nomination Checklist Form', url: '/images/NominationChecklistform.pdf' },
    { name: 'Nomination Form for Principal Applicant', url: '/images/Nominationformforprincipalapplicant.pdf' },
    { name: 'Vanuatu Document Checklist', url: '/images/Vanuatu_Document_Checklist.pdf' },
    { name: 'Spouse Form', url: '/images/SPOUSE-FORM.pdf' },
    { name: 'Child Form', url: '/images/CHILD-FORM.pdf' },
    { name: 'Dependent Form', url: '/images/DEPENDENT-FORM.pdf' },
]

const search = ref('')

const filteredDocuments = computed(() => {
    if (!search.value) return pdfFiles
    return pdfFiles.filter(doc => doc.name.toLowerCase().includes(search.value.toLowerCase()))
})
</script>

<template>
    <AppLayout title="PDF Downloads">
        <template #header>
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">
                Vanuatu Documents
            </h2>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 sm:p-6">

                    <!-- Filtro -->
                    <div class="mb-3 flex justify-end">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search document..."
                            class="w-full sm:w-64 px-3 py-1.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring focus:border-blue-400"
                        />
                    </div>

                    <!-- Tabela -->
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">
                                    Document Name
                                </th>
                                <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                            <tr
                                v-for="(file, index) in filteredDocuments"
                                :key="index"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                            >
                                <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                    {{ file.name }}
                                </td>
                                <td class="px-4 py-2">
                                    <a :href="file.url" download>
                                        <PrimaryButton class="text-xs px-3 py-1">
                                            Download
                                        </PrimaryButton>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p v-if="!filteredDocuments.length" class="mt-4 text-sm text-gray-500 dark:text-gray-300">
                        No matching documents found.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
