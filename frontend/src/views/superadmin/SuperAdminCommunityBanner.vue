<template>

  <div
    v-if="comunidadId"
    class="banner"
  >

    <div>

      ⚠️ Modo comunidad activo

      <strong v-if="nombre">
        · {{ nombre }}
      </strong>

    </div>

    <button @click="salir">

      Salir

    </button>

  </div>

</template>

<script setup>
import { ref, onMounted } from 'vue'

const comunidadId =
  localStorage.getItem(
    'superadmin_comunidad_id'
  )

const nombre = ref('')

const salir = () => {

  localStorage.removeItem(
    'superadmin_comunidad_id'
  )

  window.location.href =
    '/superadmin'

}

onMounted(() => {

  const comunidad =
    localStorage.getItem(
      'superadmin_comunidad_nombre'
    )

  if (comunidad) {

    nombre.value = comunidad

  }

})
</script>

<style scoped>

.banner{

  background:#f59e0b;
  color:white;

  padding:12px 20px;

  display:flex;

  justify-content:space-between;
  align-items:center;

  gap:20px;

  font-weight:bold;

}

.banner button{

  background:white;
  color:#111827;

  border:none;

  padding:8px 14px;

  border-radius:10px;

  cursor:pointer;

  font-weight:bold;

}

</style>