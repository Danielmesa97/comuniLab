<template>
  <div id="app-container">
    <nav v-if="route && route.path !== '/'">
      <router-link to="/incidencias">Incidencias</router-link> |
      <router-link to ="/comunidades">Comunidades</router-link>
    </nav>

    <router-view />
  </div>
</template>

<script setup>
  import {useRoute} from 'vue-router'
  const route= useRoute()
</script>