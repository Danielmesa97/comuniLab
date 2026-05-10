<template>
  <div class="dashboard-container">
    <!-- HEADER -->
    <header class="dashboard-header">
      <div class="gretting">
        <h1>Incidencias</h1>
        <p>Gestiona los problemas de tu comunidad</p>
      </div>

      <button class="icon-btn" @click="mostrarForm = true">➕</button>
    </header>

    <!-- CONTENIDO -->
    <main class="main-container">
      <!-- LISTADO -->
      <section class="panel">
        <div v-if="incidencias.length === 0" class="empty">
          <h2>No hay incidencias</h2>
          <p>Las incidencias aparecerán aquí</p>
        </div>

        <div v-else>
          <div v-for="i in incidencias" :key="i.id" class="incidencia-item">
            <div class="top">
              <strong>{{ i.titulo }}</strong>

              <span :class="'estado ' + i.estado">
                {{ i.estado }}
              </span>
            </div>

            <p>{{ i.descripcion }}</p>

            <!-- CAMBIAR ESTADO -->
            <select v-if="puedeEditar(i)" v-model="i.estado" @change="cambiarEstado(i)">
              <option value="pendiente">Pendiente</option>
              <option value="en_proceso">En proceso</option>
              <option value="resuelto">Resuelto</option>
            </select>
          </div>
        </div>
      </section>
    </main>

    <!-- MODAL CREAR -->
    <div v-if="mostrarForm" class="modal-overlay">
      <div class="modal">
        <h3>➕ Nueva incidencia</h3>

        <input v-model="form.titulo" placeholder="Título" />

        <textarea v-model="form.descripcion" placeholder="Describe el problema..."></textarea>

        <div class="modal-actions">
          <button @click="crearIncidencia">Crear</button>
          <button @click="mostrarForm = false">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { apiUrl } from '@/lib/api'

export default {
  data() {
    return {
      incidencias: [],
      mostrarForm: false,

      form: {
        titulo: '',
        descripcion: '',
      },

      user: JSON.parse(localStorage.getItem('user') || '{}'),
    }
  },

  methods: {
    async cargarIncidencias() {
      const token = localStorage.getItem('auth_token')

      const res = await fetch(apiUrl('/api/incidencias'), {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })

      const data = await res.json()
      this.incidencias = data
    },

    async crearIncidencia() {
      const token = localStorage.getItem('auth_token')

      const res = await fetch(apiUrl('/api/incidencias'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(this.form),
      })

      const data = await res.json()

      if (!res.ok) {
        alert('Error creando incidencia')
        return
      }

      this.incidencias.unshift(data)
      this.mostrarForm = false

      this.form = {
        titulo: '',
        descripcion: '',
      }
    },

    puedeEditar(i) {
      return (
        i.user_id === this.user.id || ['admin', 'presidente', 'superadmin'].includes(this.user.role)
      )
    },

    async cambiarEstado(i) {
      const token = localStorage.getItem('auth_token')

      const res = await fetch(apiUrl(`/api/incidencias/${i.id}`), {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          estado: i.estado,
        }),
      })

      if (!res.ok) {
        alert('Error actualizando estado')
      }
    },
  },

  mounted() {
    this.cargarIncidencias()
  },
}
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background: #f2f2f7;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  padding: 20px;
  background: white;
}

.main-container {
  padding: 20px;
}

.panel {
  max-width: 700px;
  margin: auto;
  background: white;
  padding: 20px;
  border-radius: 20px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.incidencia-item {
  background: #f9f9f9;
  padding: 15px;
  border-radius: 12px;
  margin-bottom: 10px;
}

.top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* ESTADOS */
.estado {
  font-size: 12px;
  padding: 5px 10px;
  border-radius: 8px;
}

.estado.pendiente {
  background: #fff3cd;
}

.estado.en_proceso {
  background: #d1ecf1;
}

.estado.resuelto {
  background: #d4edda;
}

/* MODAL */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal {
  background: white;
  padding: 20px;
  border-radius: 20px;
  width: 90%;
  max-width: 400px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.modal input,
.modal textarea {
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ddd;
}

.modal-actions {
  display: flex;
  gap: 10px;
}

.modal-actions button {
  flex: 1;
  padding: 10px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
}

.modal-actions button:first-child {
  background: black;
  color: white;
}

.modal-actions button:last-child {
  background: #eee;
}
</style>
