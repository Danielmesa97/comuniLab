<template>

  <div id="app-container">

    <router-view />

    <!-- SOLO USUARIOS NORMALES -->

    <BottomNav
      v-if="
        isLogged() &&
        !hideNavRoutes.includes(route.path) &&
        !isSuperAdmin
      "
    />

  </div>

</template>

<script setup>

import { useRoute } from 'vue-router'
import BottomNav from '@/components/BottomNav.vue'

const route = useRoute()

const hideNavRoutes = [
  '/',
  '/login',
  '/set-password'
]

const isLogged = () => {
  return !!localStorage.getItem('auth_token')
}

const user = JSON.parse(localStorage.getItem('user'))

const isSuperAdmin = user?.role === 'superadmin'

</script>
<style>
/* CSS GLOBAL PARA TODA LA APLICACIÓN */
.bottom-nav {
    display: flex;
    justify-content: space-around;
    align-items: center;

    height: 60px;
    width: 100%;

    position: fixed;   /* 🔥 CAMBIO CLAVE */
    bottom: 0;
    left: 0;

    border-top: 2px solid #e5e5e5;
    background: white;

    z-index: 1000;     /* 🔥 para que esté encima */
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #8e8e8e;
    flex: 1;
}

.icon {
    font-size: 20px;
    margin-bottom: 5px;
}

.nav-item span:last-child {
    font-size: 12px;
    font-weight: 500;
}
.dashboard-container {
  padding-bottom: 80px;
}
</style>