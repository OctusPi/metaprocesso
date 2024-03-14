import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: () => import('../views/LoginView.vue')
    },
    {
      path: '/recover',
      name: 'recover',
      component: () => import('../views/RecoverView.vue')
    },
    {
      path: '/renew/:token?',
      name: 'renew',
      component: () => import('../views/RenewView.vue')
    },
    {
      path: '/home',
      name: 'home',
      meta:{auth:true},
      component: () => import('../views/HomeView.vue')
    }
  ]
})

export default router
