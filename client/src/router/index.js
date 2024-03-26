import axios from 'axios'
import { nextTick } from 'vue'
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
      component: () => import('../views/DashboardView.vue')
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      meta:{auth:true},
      component: () => import('../views/DashboardView.vue')
    },
    {
      path: '/management',
      name:'management',
      meta: {auth:true},
      component: () => import('../views/ManagementView.vue')
    },
    {
			path: '/:pathMatch(.*)*',
			name: 'notfound',
			component: () => import('../views/NotFoundView.vue')
		}
  ]
})

router.beforeEach((to, from) => {
  if(to.meta?.auth){
    let approved = false
    
    function respcheck(response){
      approved = response.status === 200
    }

    if(approved){
      
    }else{
      
    }
  }
})

export default router
