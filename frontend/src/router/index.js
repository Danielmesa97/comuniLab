import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import Incidencias from '../views/Incidencias.vue'
import Comunidades from '../views/Comunidades.vue'
import DashboardView from '../views/DashboardView.vue'
import VotacionesView from '../views/VotacionesView.vue' 
import SolicitudView from '@/views/SolicitudView.vue'
import SolicitudesAdminView from '@/views/SolicitudesAdminView.vue'

const routes = [
  { path: '/', name: 'login', component: LoginView },
  { path: '/dashboard', name: 'dashboard', component: DashboardView },
  { path: '/incidencias', component: Incidencias },
  { path: '/comunidades', component: Comunidades },
  { path: '/votaciones', name: 'votaciones', component: VotacionesView }, 
  { path: '/solicitud', component: SolicitudView },
  { path: '/solicitudes-admin', component: SolicitudesAdminView },


  { 
    path: '/set-password', 
    name: 'set-password',
    component: () => import('@/views/SetPasswordView.vue') 
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router