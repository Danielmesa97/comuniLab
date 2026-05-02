<template>
  <nav class="bottom-nav">

    <router-link to="/dashboard" class="nav-item">🏠 Inicio</router-link>
    <router-link to="/incidencias" class="nav-item">⚠️ Incidencias</router-link>
    <router-link to="/votaciones" class="nav-item">🗳️ Votaciones</router-link>

    <!-- 🔥 SOLO SI ES ADMIN O PRESIDENTE -->
    <router-link 
      v-if="user.role === 'admin' || user.role === 'presidente'" 
      to="/solicitudes-admin" 
      class="nav-item"
    >
      📩 Solicitudes
    </router-link>

  </nav>
</template>

<script setup>
const user = JSON.parse(localStorage.getItem('user') || '{}')
</script>