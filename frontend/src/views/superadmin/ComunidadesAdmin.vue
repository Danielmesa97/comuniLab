<template>
  <div class="container">

    <!-- HEADER -->
    <div class="topbar">

      <h1>🏢 Comunidades</h1>

      <button @click="showForm = !showForm">
        {{ showForm ? 'Cerrar' : 'Nueva comunidad' }}
      </button>

    </div>

    <!-- FORMULARIO -->
    <div v-if="showForm" class="form-card">

      <input
        v-model="form.nombre"
        placeholder="Nombre comunidad"
      />

      <input
        v-model="form.direccion"
        placeholder="Dirección"
      />

      <button @click="crearComunidad">
        Crear comunidad
      </button>

    </div>

    <!-- LISTADO -->
    <div class="grid">

      <div
        v-for="c in comunidades"
        :key="c.id"
        class="card"
      >
        <h3>{{ c.nombre }}</h3>

        <p>{{ c.direccion }}</p>

        <small>ID: {{ c.id }}</small>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { apiUrl } from '@/lib/api'

const comunidades = ref([])

const showForm = ref(false)

const form = ref({
  nombre: '',
  direccion: ''
})

const token = localStorage.getItem('auth_token')


// 📋 CARGAR
const getComunidades = async () => {

  try {

    const res = await fetch(
      apiUrl('/api/superadmin/comunidades'),
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


// ➕ CREAR
const crearComunidad = async () => {

  try {

    const res = await fetch(
      apiUrl('/api/superadmin/comunidades'),
      {
        method: 'POST',

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

    form.value = {
      nombre: '',
      direccion: ''
    }

    showForm.value = false

    getComunidades()

  } catch (err) {
    console.error(err)
  }

}


onMounted(() => {
  getComunidades()
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
  gap:15px;
  flex-wrap:wrap;
}

.topbar h1{
  font-size:32px;
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
  transition:.2s;
}

.topbar button:hover{
  background:#1d4ed8;
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

  box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.form-card input{
  flex:1;
  min-width:220px;

  padding:12px;
  border-radius:10px;
  border:1px solid #ddd;
  outline:none;
}

.form-card input:focus{
  border-color:#2563eb;
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

.form-card button:hover{
  background:#15803d;
}

/* GRID */

.grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
  gap:20px;
}

/* CARD */

.card{
  background:white;
  padding:20px;
  border-radius:20px;

  border:1px solid #e5e7eb;

  box-shadow:0 5px 15px rgba(0,0,0,0.08);

  transition:.2s;
}

.card:hover{
  transform:translateY(-3px);
  box-shadow:0 10px 25px rgba(0,0,0,0.12);
}

.card h3{
  margin:0 0 10px;
}

.card p{
  color:#666;
  margin:10px 0;
}

/* ID */

.community-id{
  margin-top:10px;
  font-size:13px;
  color:#2563eb;
  font-weight:bold;
}

/* MOBILE */

@media (max-width: 600px){

  .container{
    padding:15px;
  }

  .topbar{
    flex-direction:column;
    align-items:stretch;
  }

  .topbar button{
    width:100%;
  }

  .topbar h1{
    font-size:28px;
  }

  .form-card{
    flex-direction:column;
  }

  .form-card input,
  .form-card button{
    width:100%;
  }

}

</style>