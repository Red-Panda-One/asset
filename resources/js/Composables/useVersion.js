import { ref, onMounted } from 'vue'

export function useVersion() {
    const version = ref('')

    onMounted(async () => {
        try {
            const response = await fetch('/api/version')
            const data = await response.json()
            version.value = data.version
        } catch (error) {
            console.error('Failed to fetch version:', error)
            version.value = 'Unknown'
        }
    })

    return {
        version
    }
}
