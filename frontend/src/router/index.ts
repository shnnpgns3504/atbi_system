import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path: '/', redirect: '/home' },
  { path: '/login', component: () => import('@/views/Login.vue'), meta: { public: true } },
  { path: '/register', component: () => import('@/views/Register.vue'), meta: { public: true } },
  {
    path: '/',
    component: () => import('@/components/Layout.vue'),
    children: [
      { path: 'home', component: () => import('@/views/Home.vue') },
      { path: 'atbi/:slug', component: () => import('@/views/AtbiDetail.vue') },
      { path: 'cart', component: () => import('@/views/Cart.vue') },
      { path: 'calendar', component: () => import('@/views/Calendar.vue') },
      { path: 'admin/products', component: () => import('@/views/AdminProducts.vue'), meta: { admin: true } },
      { path: 'admin/calendar', component: () => import('@/views/AdminCalendar.vue'), meta: { admin: true } }
    ]
  }
]

const router = createRouter({ history: createWebHistory(), routes })

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (!to.meta.public && !auth.isLoggedIn) return '/login'
  if (to.meta.public && auth.isLoggedIn) return '/home'
  if (to.meta.admin && auth.user?.role !== 'admin') return '/home'
})

export default router