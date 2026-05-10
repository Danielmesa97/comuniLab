<template>
  <div class="dashboard-container">
    <!-- HEADER -->
    <header class="dashboard-header">
      <div class="gretting">
        <h1>Bienvenido</h1>
        <p>Tu comunidad está al día</p>
      </div>

      <!-- 🔥 ACCIONES DERECHA -->
      <div class="header-actions">
        <button class="icon-btn">🔔</button>

        <button class="logout-btn" @click="logout">🚪</button>
      </div>
    </header>

    <!-- CONTENIDO -->
    <main class="main-container">
      <!-- 📢 ANUNCIOS -->
      <section class="card">
        <h2>📢 Últimos anuncios</h2>

        <div v-if="anuncios.length === 0">
          <p>No hay anuncios activos</p>
        </div>

        <div v-for="a in anuncios" :key="a.id" class="mini-item">
          <strong>{{ a.titulo }}</strong>
          <p>{{ a.descripcion }}</p>
        </div>

        <router-link to="/anuncios" class="ver-mas"> Ver todos → </router-link>
      </section>

      <!-- ⚠️ INCIDENCIAS -->
      <section class="card">
        <h2>⚠️ Últimas incidencias</h2>

        <div v-if="incidencias.length === 0">
          <p>No hay incidencias</p>
        </div>

        <div v-for="i in incidencias" :key="i.id" class="mini-item">
          <strong>{{ i.titulo }}</strong>
          <p>{{ i.descripcion }}</p>
        </div>

        <router-link to="/incidencias" class="ver-mas"> Ver todas → </router-link>
      </section>

      <!-- 🗳️ VOTACIONES -->
      <section class="card">
        <h2>🗳️ Votaciones activas</h2>

        <div v-if="votaciones.length === 0">
          <p>No hay votaciones activas</p>
        </div>

        <div v-for="v in votaciones" :key="v.id" class="mini-item">
          <strong>{{ v.titulo || v.nombre }}</strong>
        </div>

        <router-link to="/votaciones" class="ver-mas"> Ver todas → </router-link>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { apiUrl } from '@/lib/api'

const router = useRouter()

const logout = () => {
  const confirmar = confirm('¿Seguro que quieres cerrar sesión?')
  if (!confirmar) return

  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')

  router.push('/').then(() => {
    window.location.reload()
  })
}

const anuncios = ref([])
const incidencias = ref([])
const votaciones = ref([])

const token = localStorage.getItem('auth_token')

const getData = async () => {
  //console.log("VOTACIONES:", votaciones.value)
  try {
    // ANUNCIOS
    const resA = await fetch(apiUrl('/api/anuncios'), {
      headers: { Authorization: `Bearer ${token}` },
    })
    anuncios.value = await resA.json()

    // INCIDENCIAS
    const resI = await fetch(apiUrl('/api/incidencias'), {
      headers: { Authorization: `Bearer ${token}` },
    })
    incidencias.value = await resI.json()

    // VOTACIONES
    const resV = await fetch(apiUrl('/api/votaciones'), {
      headers: { Authorization: `Bearer ${token}` },
    })
    const data = await resV.json()
    votaciones.value = (data.votaciones || data || []).filter((v) => v.estado === 'activa')
  } catch (err) {
    console.error(err)
  }
}

onMounted(getData)
</script>

<style scoped>
.dashboard-container {
  display: flex;
  flex-direction: column;
  width: 100%;
  min-height: 100vh;
  background-color: #f2f2f7;
}

/* HEADER */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 5%;
  background: white;
  border-bottom: 2px solid #e5e5e5;
}

.gretting h1 {
  font-size: 24px;
  margin: 0;
}

.gretting p {
  font-size: 12px;
  margin-top: 5px;
  color: #8e8e8e;
}

.icon-btn {
  height: 35px;
  width: 35px;
  background: #f2f2f7;
  border-radius: 50%;
  border: none;
  cursor: pointer;
}
.header-actions {
  display: flex;
  gap: 10px;
}

.logout-btn {
  height: 35px;
  width: 35px;
  background: #ffe5e5;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.2s ease;
}

.logout-btn:hover {
  background: #ffcccc;
  transform: scale(1.05);
}

/* CONTENIDO */
.main-container {
  flex: 1;
  display: grid;
  gap: 20px;
  padding: 20px 5%;
}

/* 📱 móvil */
@media (max-width: 768px) {
  .main-container {
    grid-template-columns: 1fr;
  }
}

/* 💻 tablet */
@media (min-width: 769px) {
  .main-container {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* 🖥️ desktop grande */
@media (min-width: 1200px) {
  .main-container {
    grid-template-columns: repeat(3, 1fr);
  }
}

/* TARJETAS */
..card {
  background: white;
  border-radius: 20px;
  padding: 20px;
  width: 100%;

  max-height: 50vh; /* 🔥 clave */
  overflow: auto; /* 🔥 scroll interno */

  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
}

.card h2 {
  margin-bottom: 10px;
}

/* ITEMS */
.mini-item {
  background: #f5f5f7;
  padding: 12px;
  border-radius: 10px;
  margin-bottom: 10px;
  transition: all 0.2s ease;
}

.mini-item:hover {
  transform: translateY(-2px);
}

.mini-item strong {
  display: block;
  font-size: 14px;
}

.mini-item p {
  font-size: 12px;
  color: #666;
  margin-top: 3px;
}

/* LINK */
.ver-mas {
  display: block;
  margin-top: 10px;
  font-size: 13px;
  color: #007aff;
  text-decoration: none;
}

/* DESKTOP */
@media (min-width: 1024px) {
  .dashboard-header,
  .main-container {
    padding-left: 10%;
    padding-right: 10%;
  }
}
</style>
