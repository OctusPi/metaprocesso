import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: () => import('@/views/LoginView.vue')
    },
    {
      path: '/recover',
      name: 'recover',
      component: () => import('@/views/RecoverView.vue')
    },
    {
      path: '/renew/:token',
      name: 'renew',
      component: () => import('@/views/RenewView.vue')
    },
    {
      path: '/selectorgan',
      name: 'selectorgan',
      meta: {
        auth:true
      },
      component: () => import('@/views/SelectOrganView.vue')
    },
    {
      path: '/home',
      name: 'home',
      component: () => import('@/views/HomeView.vue')
    },
    {
			path: '/organs',
			name: 'organs',
			meta: { auth: true },
			component: () => import('../views/OrgansView.vue')
		},
    {
			path: '/units',
			name: 'units',
			meta: { auth: true },
			component: () => import('../views/UnitsView.vue')
		},
    {
			path: '/sectors',
			name: 'sectors',
			meta: { auth: true },
			component: () => import('../views/SectorsView.vue')
		},
		{
			path: '/forbidden',
			name: 'forbidden',
			component: () => import('../views/ForbiddenView.vue')
		},
		{
			path: '/:pathMatch(.*)*',
			name: 'notfound',
			component: () => import('../views/NotFoundView.vue')
		}
  ]
})

export default router
