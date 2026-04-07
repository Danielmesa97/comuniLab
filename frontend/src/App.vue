<template>
  <div id="app-container">
    <nav v-if="$route.path !== '/'">
      <router-link to="/">LoginView</router-link> |
      <router-link to="/">Incidencias</router-link> |
      <router-link to ="/comunidades">Comunidades</router-link>
    </nav>

    <router-view />
  </div>
</template>

<script setup>
  import {useRoute} from 'vue-router'
  const route= useRoute()
</script>