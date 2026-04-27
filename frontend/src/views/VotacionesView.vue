<template>
  <div class="votaciones-container">
    <h2>Sección de Votaciones</h2>
    <p>Aquí pondremos la lista de votaciones de la comunidad.</p>
    
    <button @click="$router.push('/dashboard')">Volver al Inicio</button>
  </div>
</template>

<style scoped>
.votaciones-container {
  padding: 40px;
  text-align: center;
}
</style>