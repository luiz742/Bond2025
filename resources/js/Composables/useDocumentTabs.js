// useDocumentTabs.js
import { computed } from 'vue'

export default function useDocumentTabs(familyMembers) {
  const tabs = computed(() => [
    { key: 'client', label: 'Main' },
    ...familyMembers.map(m => ({
      key: m.label.toLowerCase(),
      label: m.name || m.label.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()),
    })),
  ])
  console.log('familyMembers:', familyMembers)
  return { tabs }
}
