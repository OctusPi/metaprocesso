import { createRouter, createWebHistory } from 'vue-router'
import auth from '@/stores/auth'

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
			meta: { auth: true },
			component: () => import('../views/DashboardView.vue')
		},
		{
			path: '/dashboard',
			name: 'dashboard',
			meta: { auth: true },
			component: () => import('../views/DashboardView.vue')
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
			path: '/ordinators',
			name: 'ordinators',
			meta: { auth: true },
			component: () => import('../views/OrdinatorsView.vue')
		},
		{
			path: '/demandants',
			name: 'demandants',
			meta: { auth: true },
			component: () => import('../views/DemandantsView.vue')
		},
		{
			path: '/comissions',
			name: 'comissions',
			meta: { auth: true },
			component: () => import('../views/ComissionsView.vue')
		},
		{
			path: '/comissionsmembers/:id(\\d+)',
			name: 'comissionsmembers',
			meta: { auth: true },
			component: () => import('../views/ComissionsMembersView.vue')
		},
		{
			path: '/programs',
			name: 'programs',
			meta: { auth: true },
			component: () => import('../views/ProgramsView.vue')
		},
		{
			path: '/dotations',
			name: 'dotations',
			meta: { auth: true },
			component: () => import('../views/DotationsView.vue')
		},
		{
			path: '/management',
			name: 'management',
			meta: { auth: true },
			component: () => import('../views/ManagementView.vue')
		},
		{
			path: '/catalogs',
			name: 'catalogs',
			meta: { auth: true },
			component: () => import('../views/CatalogsView.vue')
		},
		{
			path: '/catalogitems/:catalog(\\d+)?',
			name: 'catalogitems',
			meta: {auth: true},
			component: () => import('../views/CatalogItemsView.vue')
		},
		{
			path: '/suppliers',
			name: 'suppliers',
			meta: { auth: true },
			component: () => import('../views/SuppliersView.vue')
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

router.beforeEach(async (to) => {
	if (to.meta?.auth) {
		try{
			const isAuthenticated = await auth.isLoggedIn(to.path)
			if (!isAuthenticated) {
				return '/'
			}
		}catch(e){
			return e.response?.status === 403 ? '/forbidden' :
			e.response?.status === 404 ? '/notfound' :'/'
		}
		
	}
})

export default router
