<script setup>
import { ref, onMounted } from 'vue'

const API = 'http://127.0.0.1:8000'

const token = localStorage.getItem('auth_token')

const viviendas = ref([])
const comunidades = ref([])

const showModal = ref(false)

const nuevaVivienda = ref({
  nombre: '',
  comunidad_id: ''
})

/* =========================
   CARGAR VIVIENDAS
========================= */

const getViviendas = async () => {

  try {

    const res = await fetch(
      `${API}/api/superadmin/viviendas`,
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    const data = await res.json()

    viviendas.value = data

  } catch (err) {

    console.error(err)

  }

}

/* =========================
   CARGAR COMUNIDADES
========================= */

const getComunidades = async () => {

  try {

    const res = await fetch(
      `${API}/api/superadmin/comunidades`,
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    const data = await res.json()

    comunidades.value = data

  } catch (err) {

    console.error(err)

  }

}

/* =========================
   CREAR VIVIENDA
========================= */

const crearVivienda = async () => {

  try {

    await fetch(
      `${API}/api/superadmin/viviendas`,
      {
        method: 'POST',

        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`
        },

        body: JSON.stringify(
          nuevaVivienda.value
        )
      }
    )

    nuevaVivienda.value = {
      nombre: '',
      comunidad_id: ''
    }

    showModal.value = false

    getViviendas()

  } catch (err) {

    console.error(err)

  }

}

/* =========================
   ELIMINAR
========================= */

const eliminarVivienda = async (id) => {

  if (!confirm('¿Eliminar vivienda?'))
    return

  try {

    await fetch(
      `${API}/api/superadmin/viviendas/${id}`,
      {
        method: 'DELETE',

        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )

    getViviendas()

  } catch (err) {

    console.error(err)

  }

}

onMounted(() => {

  getViviendas()
  getComunidades()

})
</script>

<template>

<div class="page">

  <!-- HEADER -->
  <div class="top-bar">

    <div class="title-section">
      <span class="icon">🏠</span>
      <h1>Viviendas</h1>
    </div>

    <button
      class="create-btn"
      @click="showModal = true"
    >
      Nueva vivienda
    </button>

  </div>

  <!-- TABLA -->
  <div class="table-card">

    <table>

      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Comunidad</th>
          <th>Usuarios</th>
          <th>Acciones</th>
        </tr>
      </thead>

      <tbody>

        <tr
          v-for="vivienda in viviendas"
          :key="vivienda.id"
        >

          <td>{{ vivienda.id }}</td>

          <td>
            {{ vivienda.nombre }}
          </td>

          <td>
            {{
              vivienda.comunidad?.nombre
              || '-'
            }}
          </td>

          <td>
            {{ vivienda.users?.length || 0 }}
          </td>

          <td>

            <button
              class="delete-btn"
              @click="eliminarVivienda(vivienda.id)"
            >
              Eliminar
            </button>

          </td>

        </tr>

      </tbody>

    </table>

  </div>

  <!-- MODAL -->
  <div
    v-if="showModal"
    class="modal-overlay"
  >

    <div class="modal">

      <h2>Nueva vivienda</h2>

      <input
        v-model="nuevaVivienda.nombre"
        placeholder="Nombre"
      >

      <select
        v-model="nuevaVivienda.comunidad_id"
      >

        <option value="">
          Selecciona comunidad
        </option>

        <option
          v-for="comunidad in comunidades"
          :key="comunidad.id"
          :value="comunidad.id"
        >
          {{ comunidad.nombre }}
        </option>

      </select>

      <div class="modal-actions">

        <button
          class="cancel-btn"
          @click="showModal = false"
        >
          Cancelar
        </button>

        <button
          class="save-btn"
          @click="crearVivienda"
        >
          Crear
        </button>

      </div>

    </div>

  </div>

</div>

</template>

<style scoped>

.page{
  padding:40px;
}

.top-bar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:30px;
}

.title-section{
  display:flex;
  align-items:center;
  gap:16px;
}

.title-section h1{
  font-size:56px;
  color:#0b1739;
  font-weight:800;
}

.icon{
  font-size:42px;
}

.create-btn{
  background:#3163ea;
  color:white;
  border:none;
  padding:18px 28px;
  border-radius:18px;
  font-size:18px;
  font-weight:700;
  cursor:pointer;
}

.table-card{
  background:white;
  border-radius:26px;
  overflow:hidden;
  box-shadow:0 4px 20px rgba(0,0,0,0.08);
}

table{
  width:100%;
  border-collapse:collapse;
}

thead{
  background:#071133;
  color:white;
}

th{
  text-align:left;
  padding:24px;
  font-size:18px;
}

td{
  padding:24px;
  border-bottom:1px solid #edf0f5;
  font-size:17px;
}

.delete-btn{
  background:#071133;
  color:white;
  border:none;
  padding:12px 18px;
  border-radius:14px;
  cursor:pointer;
  font-weight:600;
}

/* ======================
   MODAL
====================== */

.modal-overlay{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.4);

  display:flex;
  justify-content:center;
  align-items:center;

  z-index:1000;
}

.modal{
  width:450px;
  background:white;
  border-radius:24px;
  padding:30px;

  display:flex;
  flex-direction:column;
  gap:18px;
}

.modal h2{
  color:#0b1739;
  font-size:30px;
}

.modal input,
.modal select{
  padding:16px;
  border-radius:14px;
  border:1px solid #d9dfea;
  font-size:16px;
}

.modal-actions{
  display:flex;
  justify-content:flex-end;
  gap:12px;
  margin-top:10px;
}

.cancel-btn{
  background:#e8edf8;
  border:none;
  padding:14px 20px;
  border-radius:14px;
  cursor:pointer;
  font-weight:600;
}

.save-btn{
  background:#3163ea;
  color:white;
  border:none;
  padding:14px 20px;
  border-radius:14px;
  cursor:pointer;
  font-weight:700;
}

</style>