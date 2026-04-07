import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../componentes/LoginView.vue'
import Incidencias from '../views/Incidencias.vue'
import Comunidades from '../views/Comunidades.vue'

const routes = [
  {path: '/', name: 'login', component: LoginView},
  { path: '/', component: Incidencias },
  { path: '/comunidades', component: Comunidades }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router