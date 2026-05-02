<template>
  <div class="page-container">

    <div class="form-card">
      <h2>Solicitudes pendientes</h2>

      <div v-if="solicitudes.length === 0">
        <p>No hay solicitudes pendientes</p>
      </div>

      <div v-for="s in solicitudes" :key="s.id" class="card">
        <h3>{{ s.nombre }}</h3>
        <p>{{ s.email }}</p>
        <p><strong>Rol:</strong> {{ s.role }}</p>
        <p><strong>Vivienda:</strong> {{ s.vivienda?.nombre }}</p>

        <div class="actions">
          <button @click="aceptar(s.id)" class="btn-aceptar">Aceptar</button>
          <button @click="rechazar(s.id)" class="btn-rechazar">Rechazar</button>
        </div>
      </div>
    </div>

    <!-- MENÚ INFERIOR -->
    <nav class="bottom-nav">
      <router-link to="/dashboard" class="nav-item">
        <span class="icon">🏠</span>
        <span>Inicio</span> 
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
        v-if="['admin','presidente','superadmin'].includes(user.role)"
        to="/solicitudes-admin" 
        class="nav-item"
      >
        <span class="icon">📩</span>
        <span>Solicitudes</span>
      </router-link>
    </nav>

  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = JSON.parse(localStorage.getItem('user'))
const solicitudes = ref([])

const getSolicitudes = async () => {
  try {
    const token = localStorage.getItem('auth_token')

    const res = await fetch('http://127.0.0.1:8000/api/solicitudes', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (!res.ok) {
      console.warn("No autorizado o error")
      return
    }

    const data = await res.json()
    solicitudes.value = data

  } catch (err) {
    console.error(err)
  }
}

const aceptar = async (id) => {
  const token = localStorage.getItem('auth_token')

  await fetch(`http://127.0.0.1:8000/api/solicitudes/${id}/aceptar`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  })

  getSolicitudes()
}

const rechazar = async (id) => {
  const token = localStorage.getItem('auth_token')

  await fetch(`http://127.0.0.1:8000/api/solicitudes/${id}/rechazar`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    }
  })

  getSolicitudes()
}

onMounted(getSolicitudes)
</script>

<style scoped>

.page-container {
  width: 100%;
  min-height: 100vh;
  padding: 20px;
  padding-bottom: 80px; /* 👈 para el menú */
  background: #f2f2f7;
}

.form-card {
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 16px;
  margin-bottom: 15px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.08);
}

.card h3 {
  margin: 0 0 5px;
}

.card p {
  margin: 5px 0;
  color: #555;
}

.actions {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.btn-aceptar {
  flex: 1;
  background: #22c55e;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 8px;
  cursor: pointer;
}

.btn-rechazar {
  flex: 1;
  background: #ef4444;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 8px;
  cursor: pointer;
}
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;          /* 🔥 IMPORTANTE */
  width: 100%;
  height: 60px;     /* opcional pero recomendado */

  display: flex;
  justify-content: space-around;
  align-items: center;

  background: white;
  border-top: 1px solid #ddd;
  z-index: 999;     /* 🔥 para que esté siempre encima */
}
</style>