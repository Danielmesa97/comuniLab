<template>

  <div class="container">

    <!-- HEADER -->

    <div class="header">

      <div>

        <h1>
          🏢 {{ comunidad.nombre }}
        </h1>

        <p>
          {{ comunidad.descripcion }}
        </p>

      </div>

      <div class="badge">
        ID: {{ comunidad.id }}
      </div>

    </div>

    <!-- STATS -->

    <div class="stats">

      <div class="stat-card">
        <h3>{{ stats.viviendas }}</h3>
        <p>Viviendas</p>
      </div>

      <div class="stat-card">
        <h3>{{ stats.usuarios }}</h3>
        <p>Usuarios</p>
      </div>

      <div class="stat-card">
        <h3>{{ stats.incidencias }}</h3>
        <p>Incidencias</p>
      </div>

      <div class="stat-card">
        <h3>{{ stats.votaciones }}</h3>
        <p>Votaciones</p>
      </div>

    </div>

    <!-- ACCIONES -->

    <div class="actions">

      <button @click="entrarComoAdmin">
        🚀 Entrar en comunidad
      </button>

    </div>

  </div>

</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { apiUrl } from '@/lib/api'

const route = useRoute()
const router = useRouter()
const token = localStorage.getItem('auth_token')

const comunidad = ref({})

const stats = ref({
  viviendas: 0,
  usuarios: 0,
  incidencias: 0,
  votaciones: 0
})

const getComunidad = async () => {

  try {

    const res = await fetch(
      apiUrl(`/api/superadmin/comunidades/${route.params.id}`),
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    const data = await res.json()

    comunidad.value = data.comunidad

    stats.value = data.stats

  } catch (err) {

    console.error(err)

  }

}

const entrarComoAdmin = async () => {

  localStorage.setItem(
    'superadmin_comunidad_id',
    comunidad.value.id
  )

  localStorage.setItem(
    'superadmin_comunidad_nombre',
    comunidad.value.nombre
  )

  await router.push({
    path: '/superadmin/comunidad/dashboard'
  })

}

onMounted(() => {

  getComunidad()

})
</script>

<style scoped>

.container{
  padding:20px;
}

.header{
  display:flex;
  justify-content:space-between;
  align-items:center;

  margin-bottom:30px;

  flex-wrap:wrap;
  gap:20px;
}

.header h1{
  margin:0;
}

.header p{
  color:#666;
}

.badge{
  background:#2563eb;
  color:white;

  padding:10px 15px;

  border-radius:12px;

  font-weight:bold;
}

.stats{
  display:grid;

  grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));

  gap:20px;

  margin-bottom:30px;
}

.stat-card{
  background:white;

  border-radius:20px;

  padding:25px;

  box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.stat-card h3{
  font-size:36px;
  margin:0;
}

.stat-card p{
  color:#666;
}

.actions{
  display:flex;
  gap:15px;
}

.actions button{
  background:#16a34a;
  color:white;

  border:none;

  padding:14px 20px;

  border-radius:12px;

  cursor:pointer;

  font-weight:bold;
}

</style>