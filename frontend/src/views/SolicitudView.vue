<template>
  <div class="page-container">
    <div class="form-card">
      <h2>Solicitar acceso</h2>
      <p>Rellena tus datos para unirte a la comunidad</p>

      <form @submit.prevent="enviarSolicitud">
        <div class="input-group">
          <label>Nombre</label>
          <input v-model="form.nombre" required />
        </div>

        <div class="input-group">
          <label>Email</label>
          <input type="email" v-model="form.email" required />
        </div>

        <div class="input-group">
          <label>Rol</label>
          <select v-model="form.role" required>
            <option disabled value="">Selecciona</option>
            <option value="inquilino">Inquilino</option>
            <option value="propietario">Propietario</option>
            <option value="presidente">Presidente</option>
          </select>
        </div>

        <div class="input-group">
          <label>ID Comunidad</label>
          <input type="number" v-model="form.comunidad_id" @change="getViviendas" required />
        </div>

        <div class="input-group">
          <label>Vivienda</label>
          <select v-model="form.vivienda_id" required>
            <option disabled value="">Selecciona vivienda</option>
            <option v-for="v in viviendas" :key="v.id" :value="v.id">
              {{ v.nombre }}
            </option>
          </select>
        </div>

        <button class="main-btn">Enviar solicitud</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { apiUrl } from '@/lib/api'

const viviendas = ref([])

const form = ref({
  nombre: '',
  email: '',
  role: '',
  vivienda_id: '',
  comunidad_id: '',
})

// 🔥 CARGAR VIVIENDAS SEGÚN COMUNIDAD
const getViviendas = async () => {
  try {
    if (!form.value.comunidad_id) return

    const res = await fetch(`${apiUrl('/api/viviendas')}?comunidad_id=${form.value.comunidad_id}`)

    const data = await res.json()
    viviendas.value = data
  } catch (error) {
    console.error(error)
  }
}

const enviarSolicitud = async () => {
  try {
    const response = await fetch(apiUrl('/api/solicitudes'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(form.value),
    })

    const data = await response.json()

    if (!response.ok) {
      alert(data.message || 'Error al enviar solicitud')
      return
    }

    alert('Solicitud enviada correctamente 👍')

    form.value = {
      nombre: '',
      email: '',
      role: '',
      vivienda_id: '',
      comunidad_id: '',
    }

    viviendas.value = []
  } catch (error) {
    console.error(error)
    alert('Error de conexión con el servidor')
  }
}
</script>

<style scoped>
.page-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: #f2f2f7;
}

.form-card {
  background: white;
  padding: 40px;
  border-radius: 20px;
  width: 100%;
  max-width: 400px;
}

.input-group {
  margin-bottom: 15px;
}

input,
select {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
}

.main-btn {
  width: 100%;
  padding: 15px;
  background: #080a13;
  color: white;
  border: none;
  border-radius: 10px;
  margin-top: 10px;
}
</style>
