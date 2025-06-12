<template>
    <transition name="fade">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-sm w-full p-5 mx-4 relative" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Confirm Deletion</h3>

                <p class="text-gray-700 dark:text-gray-300 mb-6 text-sm">
                    Are you sure you want to delete <strong>{{ itemName }}</strong>? This action cannot be undone.
                </p>

                <div class="flex justify-end space-x-3">
                    <button @click="onCancel"
                        class="px-4 py-1.5 rounded-md bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition">
                        Cancel
                    </button>
                    <button @click="onConfirm"
                        class="px-4 py-1.5 rounded-md bg-red-600 hover:bg-red-700 text-white font-semibold text-sm transition">
                        Delete
                    </button>
                </div>

                <button @click="onCancel" aria-label="Close modal"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </transition>
</template>

<script setup>
defineProps({
    show: Boolean,
    itemName: String
})

const emit = defineEmits(['cancel', 'confirm'])

const onCancel = () => emit('cancel')
const onConfirm = () => emit('confirm')
</script>
