<template>
  <div class="relative w-full">
    <!-- Botão principal que abre o dropdown -->
    <button
      @click="isOpen = !isOpen"
      class="w-full text-left px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
    >
      {{ selectedLabel || 'Select an option' }}
    </button>

    <!-- Dropdown -->
    <div
      v-if="isOpen"
      class="absolute z-20 mt-1 w-full rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-lg"
    >
      <!-- Campo de busca -->
      <input
        v-model="search"
        type="text"
        placeholder="Type to search..."
        class="w-full px-4 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-600 focus:outline-none"
      />

      <!-- Lista de opções -->
      <ul class="max-h-60 overflow-y-auto text-sm">
        <li
          v-for="option in filteredOptions"
          :key="option[valueKey]"
          @click="selectOption(option)"
          class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100"
        >
          {{ option[labelKey] }}
        </li>
        <li
          v-if="filteredOptions.length === 0"
          class="px-4 py-2 text-gray-500 dark:text-gray-400 italic"
        >
          No results found
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: [String, Number],
  options: Array,
  labelKey: {
    type: String,
    default: 'name'
  },
  valueKey: {
    type: String,
    default: 'id'
  }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const search = ref('');
const selected = ref(props.modelValue);

watch(() => props.modelValue, (val) => {
  selected.value = val;
});

const selectedLabel = computed(() => {
  const selectedOption = props.options.find(o => o[props.valueKey] === selected.value);
  return selectedOption ? selectedOption[props.labelKey] : '';
});

const filteredOptions = computed(() => {
  if (!search.value) return props.options;
  return props.options.filter(option =>
    option[props.labelKey].toLowerCase().includes(search.value.toLowerCase())
  );
});

function selectOption(option) {
  selected.value = option[props.valueKey];
  emit('update:modelValue', selected.value);
  isOpen.value = false;
  search.value = '';
}
</script>
