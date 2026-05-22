<template>

  <div class="container">

    <!-- HEADER -->

    <div class="topbar">

      <h1>
        🏠 Viviendas
      </h1>

      <button @click="toggleForm">

        {{
          showForm
            ? 'Cerrar'
            : 'Nueva vivienda'
        }}

      </button>

    </div>

    <!-- FORM -->

    <div
      v-if="showForm"
      class="form-card"
    >

      <input
        v-model="form.nombre"
        placeholder="Ej: 1ºA"
      />

      <button @click="guardarVivienda">

        {{
          editandoId
            ? 'Guardar cambios'
            : 'Crear vivienda'
        }}

      </button>

    </div>

    <!-- GRID -->

    <div class="grid">

      <div
        v-for="v in viviendas"
        :key="v.id"
        class="card"
      >

        <h3>
          {{ v.nombre }}
        </h3>

        <small>
          ID: {{ v.id }}
        </small>

        <div class="actions">

          <button
            class="edit"
            @click="editarVivienda(v)"
          >
            ✏️ Editar
          </button>

          <button
            class="danger"
            @click="eliminarVivienda(v.id)"
          >
            🗑 Eliminar
          </button>

        </div>

      </div>

    </div>

  </div>

</template>

<script setup>
import { ref, onMounted } from 'vue'
import { apiUrl } from '@/lib/api'

const viviendas = ref([])

const showForm = ref(false)

const editandoId = ref(null)

const token =
  localStorage.getItem('auth_token')

const form = ref({
  nombre: ''
})

/* =========================
   CARGAR
========================= */

const getViviendas = async () => {

  try {

    const user = JSON.parse(
      localStorage.getItem('user') || '{}'
    )

    const comunidadId =
      Object.values(user.comunidades || {})[0]?.id

    console.log(user)
    console.log(comunidadId)

    const res = await fetch(
      apiUrl(
        `/api/viviendas?comunidad_id=${comunidadId}`
      ),
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
   FORM
========================= */

const toggleForm = () => {

  showForm.value = !showForm.value

  if (!showForm.value) {

    resetForm()

  }

}

const resetForm = () => {

  form.value = {
    nombre: ''
  }

  editandoId.value = null

}

/* =========================
   CREAR / EDITAR
========================= */

const guardarVivienda = async () => {

  try {

    const endpoint = editandoId.value
      ? `/api/viviendas/${editandoId.value}`
      : '/api/viviendas'

    const method = editandoId.value
      ? 'PUT'
      : 'POST'

    const res = await fetch(
      apiUrl(endpoint),
      {
        method,

        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`
        },

        body: JSON.stringify(form.value)
      }
    )

    const data = await res.json()

    if (!res.ok) {

      alert(data.message || 'Error')

      return

    }

    resetForm()

    showForm.value = false

    getViviendas()

  } catch (err) {

    console.error(err)

  }

}

/* =========================
   EDITAR
========================= */

const editarVivienda = (v) => {

  form.value = {
    nombre: v.nombre
  }

  editandoId.value = v.id

  showForm.value = true

}

/* =========================
   ELIMINAR
========================= */

const eliminarVivienda = async (id) => {

  const ok = confirm(
    '¿Eliminar vivienda?'
  )

  if (!ok) return

  try {

    await fetch(
      apiUrl(`/api/viviendas/${id}`),
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

})
</script>

<style scoped>

.container{
  padding:20px;
}

/* TOPBAR */

.topbar{
  display:flex;
  justify-content:space-between;
  align-items:center;

  margin-bottom:30px;

  gap:20px;
  flex-wrap:wrap;
}

.topbar h1{
  margin:0;
}

.topbar button{
  background:#2563eb;
  color:white;

  border:none;

  padding:12px 18px;

  border-radius:12px;

  cursor:pointer;

  font-weight:bold;
}

/* FORM */

.form-card{
  background:white;

  padding:20px;

  border-radius:20px;

  margin-bottom:30px;

  display:flex;
  gap:10px;

  flex-wrap:wrap;

  box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.form-card input{
  flex:1;

  min-width:220px;

  padding:12px;

  border-radius:10px;

  border:1px solid #ddd;
}

.form-card button{
  background:#16a34a;
  color:white;

  border:none;

  padding:12px 18px;

  border-radius:10px;

  cursor:pointer;

  font-weight:bold;
}

/* GRID */

.grid{
  display:grid;

  grid-template-columns:
    repeat(auto-fit,minmax(260px,1fr));

  gap:20px;
}

/* CARD */

.card{
  background:white;

  border-radius:20px;

  padding:20px;

  border:1px solid #e5e7eb;

  box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.card h3{
  margin-top:0;
}

/* ACTIONS */

.actions{
  margin-top:15px;

  display:flex;

  gap:10px;
}

.actions button{
  flex:1;

  border:none;

  padding:10px;

  border-radius:10px;

  cursor:pointer;
}

.edit{
  background:#f3f4f6;
}

.danger{
  background:#ef4444;
  color:white;
}

</style>