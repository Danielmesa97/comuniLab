<template>
  <div class="dashboard-container">

    <!-- HEADER -->
    <header class="dashboard-header">
      <div class="gretting">
        <h1>Incidencias</h1>
        <p>Gestiona los problemas de tu comunidad</p>
      </div>

      <div class="header-actions">
        <button class="icon-btn" @click="mostrarForm = !mostrarForm">
          ➕
        </button>
      </div>
    </header>

    <!-- CONTENIDO -->
    <main class="main-container">

      <!-- FORMULARIO -->
      <IncidenciaForm
        v-if="mostrarForm"
        @crear-incidencia="agregarIncidencia"
      />

      <!-- PANEL -->
      <section class="panel">

        <div v-if="incidencias.length === 0">
          <h2>No hay incidencias</h2>
          <p>Las incidencias reportadas aparecerán aquí</p>
        </div>

        <div v-else>
          <div
            v-for="i in incidencias"
            :key="i.id"
            class="incidencia-item"
          >
            <strong>{{ i.titulo }}</strong>
            <p>{{ i.descripcion }}</p>
          </div>
        </div>

      </section>
    </main>

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

      <router-link to="/incidencias" class="nav-item">
        <span class="icon">🗳️</span>
        <span>Votaciones</span>
      </router-link>

      <router-link to="/incidencias" class="nav-item">
        <span class="icon">👤</span>
        <span>Perfil</span>
      </router-link>
    </nav>

  </div>
</template>

<script>
import IncidenciaForm from "../components/IncidenciaForm.vue"

export default {
  name: "Incidencias",
  components: { IncidenciaForm },

  data() {
    return {
      mostrarForm: false,
      incidencias: []
    }
  },

  methods: {
    agregarIncidencia(nueva) {
      // lo que viene del backend
      this.incidencias.push(nueva)
      this.mostrarForm = false
    },

    cargarIncidencias() {
      fetch('http://127.0.0.1:8000/api/incidencias')
        .then(res => res.json())
        .then(data => {
          this.incidencias = data
        })
    }
  },

  mounted() {
    this.cargarIncidencias()
  }
}
</script>

<style scoped>
/* MISMO ESTILO QUE DASHBOARD */

.dashboard-container{
  display:flex;
  flex-direction: column;
  width: 100%;
  min-height: 100vh;
  background-color: #f2f2f7;
}

.dashboard-header{
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 5%;
  background: white;
  border-bottom: 2px solid #e5e5e5;
}

.gretting h1{
  font-size: 24px;
  margin: 0;
}

.gretting p{
  font-size: 12px;
  margin-top: 5px;
  color: #8e8e8e;
}

.icon-btn{
  height: 35px;
  width: 35px;
  background: #f2f2f7;
  border-radius: 50%;
  border: none;
  cursor: pointer;
}

.main-container{
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px 5%;
}

.panel{
  text-align:center;
  padding: 30px;
  border-radius: 20px;
  background: white;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  width: 100%;
}

.incidencia-item{
  background: #f9f9f9;
  padding: 10px;
  margin-bottom: 10px;
  border-radius: 10px;
  text-align: left;
}

.bottom-nav{
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

.nav-item{
  display: flex;
  flex-direction: column;
  align-items: center;
  text-decoration: none;
  color: #8e8e8e;
  flex: 1;
}

.icon{
  font-size: 20px;
  margin-bottom: 5px;
}

.nav-item span:last-child{
  font-size: 12px;
}
</style>