<template>

  <div id="app-container">
    <SuperAdminCommunityBanner />
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
import SuperAdminCommunityBanner
  from '@/views/superadmin/SuperAdminCommunityBanner.vue'

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

    display:flex;
    align-items:center;

    overflow-x:auto;
    overflow-y:hidden;

    white-space:nowrap;
    -webkit-overflow-scrolling:touch;

}
.bottom-nav::-webkit-scrollbar{
  display:none;
}

.nav-item {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;

    text-decoration:none;
    color:#8e8e8e;

    min-width:80px;
    flex-shrink:0;

    padding:0 10px;
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