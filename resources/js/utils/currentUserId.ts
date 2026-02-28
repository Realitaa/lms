import { usePage } from '@inertiajs/vue3'

const currentUserId = usePage().props.auth.user.id

export default currentUserId