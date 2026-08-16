import { defineStore } from 'pinia'

export const useUsersStore = defineStore('users', {
  state: () => ({
    users: [],
    loading: false,
    error: '',
    loaded: false,
  }),
  actions: {
    async fetchUsers() {
      if (this.loaded || this.loading) {
        return
      }

      this.loading = true
      this.error = ''

      try {
        const response = await fetch('/api/users', {
          headers: {
            'X-Api-Key': import.meta.env.VITE_API_KEY,
          },
        })

        if (!response.ok) {
          if (response.status === 401) {
            throw new Error('دسترسی به API مجاز نیست.')
          }

          throw new Error('خطا در دریافت لیست کاربران.')
        }

        const result = await response.json()
        this.users = Array.isArray(result.data) ? result.data : []
        this.loaded = true
      } catch (error) {
        this.error = error instanceof Error ? error.message : 'خطا در دریافت لیست کاربران.'
      } finally {
        this.loading = false
      }
    },
  },
})
