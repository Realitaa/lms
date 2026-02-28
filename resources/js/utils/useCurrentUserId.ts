import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const currentUserId = computed(() => {
  return usePage().props.auth?.user?.id ?? null
})

export function useCurrentUserId() {
  return currentUserId.value
}