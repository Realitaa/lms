import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

/**
 * Memberikan informasi tentang pengguna yang sedang diautentikasi, seperti username, id, dan role.
 * @returns An object containing the authenticated user's information, including `user`, `userId`, and `role`.
 * - `user`: The authenticated user's data, or `null` if not authenticated.
 * - `userId`: The authenticated user's ID, or `null` if not authenticated.
 * - `role`: The authenticated user's role, or `null` if not authenticated.
 */
export function useAuth() {
  const page = usePage()

  const user = computed(() => page.props.auth?.user)
  const userId = computed(() => user.value?.id ?? null)
  const role = computed(() => user.value?.role ?? null)

  return { user, userId, role }
}