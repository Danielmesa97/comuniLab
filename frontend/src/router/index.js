import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import Incidencias from '../views/Incidencias.vue'
import Comunidades from '../views/Comunidades.vue'
import DashboardView from '../views/DashboardView.vue'
import VotacionesView from '../views/VotacionesView.vue'
import SolicitudView from '@/views/SolicitudView.vue'
import SolicitudesAdminView from '@/views/SolicitudesAdminView.vue'
import AnunciosView from '@/views/AnunciosView.vue'
import ReservaInstalaciones from '../views/ReservaInstalaciones.vue'


import SuperAdminDashboard from '../views/superadmin/SuperAdminDashboard.vue'
import ComunidadesAdmin from '../views/superadmin/ComunidadesAdmin.vue'
import SuperAdminLayout from '../views/superadmin/SuperAdminLayout.vue'

const routes = [
  { path: '/', name: 'login', component: LoginView },

  { path: '/dashboard', component: DashboardView },
  { path: '/incidencias', component: Incidencias },
  { path: '/comunidades', component: Comunidades },
  { path: '/votaciones', component: VotacionesView },
  { path: '/solicitud', component: SolicitudView },
  { path: '/anuncios', component: AnunciosView },
  { path: '/instalaciones',component: ReservaInstalaciones},


  {
  path: '/superadmin',
  component: SuperAdminLayout,

  children: [

    {
      path: '',
      component: SuperAdminDashboard,
    },

    {
      path: 'comunidades',
      component: ComunidadesAdmin,
    },
    {
      path: 'comunidades/:id',
      component: () =>
        import('@/views/superadmin/ComunidadDetailAdmin.vue')
    }

  ]

},

  // SOLO ADMIN
  { path: '/solicitudes-admin', component: SolicitudesAdminView },

  { 
    path: '/set-password',
    component: () => import('@/views/SetPasswordView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

/* 🔐 PROTECCIÓN GLOBAL */
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  const user = JSON.parse(localStorage.getItem('user') || '{}')

  const publicRoutes = ['/', '/set-password']

  // rutas públicas
  if (publicRoutes.includes(to.path)) {
    return next()
  }

  // no logeado → fuera
  if (!token) {
    return next('/')
  }

  // 🔥 PROTEGER ADMIN
  if (to.path === '/solicitudes-admin') {
    if (!['admin', 'presidente', 'superadmin'].includes(user.role)) {
      return next('/dashboard')
    }
  }

  next()
})

export default router