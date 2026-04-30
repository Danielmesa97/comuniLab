<template> 
  <div id="app-container"> 
    <router-view />
  </div> 
</template>

<script setup>
  import {useRoute} from 'vue-router'
  const route = useRoute()
</script>

<style>
/* CSS GLOBAL PARA TODA LA APLICACIÓN */
.bottom-nav {
    display: flex;
    justify-content: space-around;
    align-items: center;
    height: 60px;
    border-top: 2px solid #e5e5e5;
    background: white;
    width: 100%;
    position: sticky;
    bottom: 0;
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
</style>