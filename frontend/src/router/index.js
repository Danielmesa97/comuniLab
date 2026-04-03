import { createRouter, createWebHistory } from 'vue-router'
import Incidencias from '../views/Incidencias.vue'

const routes = [
  { path: '/', component: Incidencias }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router