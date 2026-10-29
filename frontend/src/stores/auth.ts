import { defineStore } from 'pinia'
import axiosClient from '@/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    isLoggedIn: !!localStorage.getItem('user'),
    cartCount: 0
  }),
  actions: {
    async login(username: string, password: string) {
      const { data } = await axiosClient.post('/auth/login', { username, password })
      if (data.success) {
        this.user = data.user
        this.isLoggedIn = true
        localStorage.setItem('user', JSON.stringify(data.user))
        localStorage.setItem('user_id', data.user.user_id)
      }
      return data
    },
    async register(payload: any) {
      const { data } = await axiosClient.post('/auth/register', payload)
      return data
    },
    async loadCartCount() {
      if (!this.user) return
      try {
        const { data } = await axiosClient.get('/cart', { params: { user_id: this.user.user_id } })
        if (data.success) this.cartCount = data.data.length
      } catch (e) { console.error(e) }
    },
    logout() {
      this.user = null
      this.isLoggedIn = false
      this.cartCount = 0
      localStorage.removeItem('user')
      localStorage.removeItem('user_id')
    }
  }
})