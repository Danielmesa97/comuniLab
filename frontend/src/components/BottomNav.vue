<template>
  <nav class="bottom-nav">

    <router-link to="/dashboard" class="nav-item">🏠 Inicio</router-link>
    
    <router-link to="/anuncios" class="nav-item">
      <span class="icon">📢</span>
      <span>Anuncios</span>
    </router-link>
    
    <router-link to="/incidencias" class="nav-item">⚠️ Incidencias</router-link>
    
    <router-link to="/votaciones" class="nav-item">🗳️ Votaciones</router-link>

    <router-link 
      v-if="user.role === 'admin' || user.role === 'presidente'" 
      to="/solicitudes-admin" 
      class="nav-item"
    >
      📩 Solicitudes
    </router-link>

    <router-link to="/instalaciones" class="nav-item">
      📅 Instalaciones
    </router-link>

  </nav>
</template>

<script setup>
const user = JSON.parse(localStorage.getItem('user') || '{}')
</script>