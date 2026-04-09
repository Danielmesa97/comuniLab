import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../components/LoginView.vue'
import Incidencias from '../views/Incidencias.vue'
import Comunidades from '../views/Comunidades.vue'
import DashboardView from '../views/DashboardView.vue'

const routes = [
  {path: '/', name: 'login', component: LoginView},
  {path:'/dashboard', name:'dashboard', component: DashboardView },
  { path: '/incidencias', component: Incidencias },
  { path: '/comunidades', component: Comunidades }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})


export default router