<template>
  <nav class="bottom-nav">
    <router-link to="/dashboard" class="nav-item">
      <span class="icon">🏠</span>
      <span>Inicio</span>
    </router-link>

    <router-link to="/anuncios" class="nav-item">
      <span class="icon">📢</span>
      <span>Anuncios</span>
    </router-link>

    <router-link to="/incidencias" class="nav-item">
      <span class="icon">⚠️</span>
      <span>Incidencias</span>
    </router-link>

    <router-link to="/votaciones" class="nav-item">
      <span class="icon">🗳️</span>
      <span>Votaciones</span>
    </router-link>

    <router-link
      v-if="user.role === 'admin' || user.role === 'presidente'"
      to="/viviendas"
      class="nav-item"
    >
      <span class="icon">🏢</span>
      <span>Viviendas</span>
    </router-link>

    <router-link 
      v-if="user.role === 'admin' || user.role === 'presidente'"
      to="/solicitudes-admin"
      class="nav-item"
    >
      <span class="icon">📩</span>
      <span>Solicitudes</span>
    </router-link>

    <router-link to="/instalaciones" class="nav-item">
      <span class="icon">📅</span>
      <span>Instalaciones</span>
    </router-link>
  </nav>
</template>

<script setup>
const user = JSON.parse(localStorage.getItem('user') || '{}')
</script>