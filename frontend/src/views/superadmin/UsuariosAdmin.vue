<template>

  <div class="usuarios-container">

    <!-- HEADER -->

    <div class="header">

      <h1>
        👥 Usuarios
      </h1>

      <button
        class="primary-btn"
        @click="showForm = !showForm"
      >
        {{
          showForm
            ? 'Cerrar'
            : 'Nuevo usuario'
        }}
      </button>

    </div>

    <!-- FORMULARIO -->

    <div
      v-if="showForm"
      class="form-card"
    >

      <input
        v-model="form.name"
        placeholder="Nombre"
      />

      <input
        v-model="form.email"
        placeholder="Email"
      />

      <select v-model="form.role">

        <option value="superadmin">
          Superadmin
        </option>

        <option value="admin">
          Admin
        </option>

        <option value="presidente">
          Presidente
        </option>

        <option value="propietario">
          Propietario
        </option>

        <option value="inquilino">
          Inquilino
        </option>

      </select>

      <select v-model="form.comunidad_id">

        <option :value="null">
          Sin comunidad
        </option>

        <option
          v-for="c in comunidades"
          :key="c.id"
          :value="c.id"
        >
          {{ c.nombre }}
        </option>

      </select>

      <select v-model="form.vivienda_id">

        <option :value="null">
          Sin vivienda
        </option>

        <option
          v-for="v in viviendas"
          :key="v.id"
          :value="v.id"
        >
          {{ v.nombre }}
        </option>

      </select>

      <button
        class="primary-btn"
        @click="crearUsuario"
      >
        Crear usuario
      </button>

    </div>

    <!-- TABLA -->

    <div class="table-card">

      <table>

        <thead>

          <tr>

            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Role</th>
            <th>Comunidad</th>
            <th>Vivienda</th>
            <th>Estado</th>
            <th>Acciones</th>

          </tr>

        </thead>

        <tbody>

          <tr
            v-for="u in usuarios"
            :key="u.id"
          >

           <td>{{ u.id }}</td>

            <td>{{ u.name }}</td>

            <td>{{ u.email }}</td>

            <td>
                <span class="role-badge">
                {{ u.role }}
                </span>
            </td>

            <td>
                {{
                u.comunidades?.length
                    ? u.comunidades[0].nombre
                    : '-'
                }}
            </td>

            <td>
                {{
                u.vivienda?.nombre || '-'
                }}
            </td>

            <td>
                <span
                :class="[
                    'status-badge',
                    u.activo
                    ? 'active'
                    : 'inactive'
                ]"
                >
                {{ u.activo ? 'Activo' : 'Inactivo' }}
                </span>
            </td>

            <td>
                <button
                class="toggle-btn"
                @click="toggleActivo(user.id)"
                >
                {{
                    u.activo
                    ? 'Desactivar'
                    : 'Activar'
                }}
                </button>
            </td>

            </tr>

        </tbody>

      </table>

    </div>

  </div>

</template>

<script setup>

import {
  ref,
  onMounted
} from 'vue'

import { apiUrl } from '@/lib/api'

const token =
  localStorage.getItem('auth_token')

const usuarios = ref([])

const comunidades = ref([])

const viviendas = ref([])

const showForm = ref(false)

const form = ref({

  name: '',
  email: '',
  role: 'propietario',

  comunidad_id: null,
  vivienda_id: null

})

/*
|--------------------------------------------------------------------------
| GET USERS
|--------------------------------------------------------------------------
*/

const getUsuarios = async () => {

  const res = await fetch(
    apiUrl(
      '/api/superadmin/usuarios'
    ),
    {
      headers: {
        Authorization:
          `Bearer ${token}`
      }
    }
  )

  const data = await res.json()

  console.log(data)

  usuarios.value = data

}

/*
|--------------------------------------------------------------------------
| GET COMUNIDADES
|--------------------------------------------------------------------------
*/

const getComunidades = async () => {

  const res = await fetch(
    apiUrl(
      '/api/superadmin/comunidades'
    ),
    {
      headers: {
        Authorization:
          `Bearer ${token}`
      }
    }
  )

  comunidades.value =
    await res.json()

}

/*
|--------------------------------------------------------------------------
| GET VIVIENDAS
|--------------------------------------------------------------------------
*/

const getViviendas = async () => {

  const res = await fetch(
    apiUrl(
      '/api/viviendas'
    ),
    {
      headers: {
        Authorization:
          `Bearer ${token}`
      }
    }
  )

  viviendas.value =
    await res.json()

}

/*
|--------------------------------------------------------------------------
| CREAR
|--------------------------------------------------------------------------
*/

const crearUsuario = async () => {

  const res = await fetch(
    apiUrl(
      '/api/superadmin/usuarios'
    ),
    {
      method: 'POST',

      headers: {

        'Content-Type':
          'application/json',

        Authorization:
          `Bearer ${token}`

      },

      body: JSON.stringify(
        form.value
      )
    }
  )

  if (!res.ok) {

    alert('Error')
    return

  }

  showForm.value = false

  form.value = {

    name: '',
    email: '',
    role: 'propietario',

    comunidad_id: null,
    vivienda_id: null

  }

  getUsuarios()

}

/*
|--------------------------------------------------------------------------
| TOGGLE ACTIVO
|--------------------------------------------------------------------------
*/

const toggleActivo = async (id) => {

  await fetch(
    apiUrl(
      `/api/superadmin/usuarios/${id}/toggle-activo`
    ),
    {
      method: 'PUT',

      headers: {
        Authorization:
          `Bearer ${token}`
      }
    }
  )

  getUsuarios()

}

onMounted(() => {

  getUsuarios()

  getComunidades()

  getViviendas()

})

</script>
<style scoped>

.usuarios-container {

  padding: 40px;
  color: #1e293b;

}

.header {

  display: flex;
  justify-content: space-between;
  align-items: center;

  margin-bottom: 30px;

}

.header h1 {

  font-size: 42px;
  font-weight: 700;

}

.primary-btn {

  background: #2563eb;
  color: white;

  border: none;

  padding: 14px 24px;

  border-radius: 12px;

  cursor: pointer;

  font-weight: 600;

  transition: 0.2s;

}

.primary-btn:hover {

  background: #1d4ed8;

}

.secondary-btn {

  background: #0f172a;
  color: white;

  border: none;

  padding: 10px 14px;

  border-radius: 10px;

  cursor: pointer;

}

.form-card {

  background: white;

  border-radius: 20px;

  padding: 25px;

  margin-bottom: 30px;

  display: grid;

  grid-template-columns:
    repeat(auto-fit, minmax(200px, 1fr));

  gap: 15px;

  box-shadow:
    0 4px 20px rgba(0,0,0,0.08);

}

.form-card input,
.form-card select {

  padding: 14px;

  border-radius: 12px;

  border: 1px solid #dbe2ea;

  font-size: 15px;

}

.table-card {

  background: white;

  border-radius: 20px;

  overflow: hidden;

  box-shadow:
    0 4px 20px rgba(0,0,0,0.08);

}

table {

  width: 100%;
  border-collapse: collapse;

}

thead {

  background: #0f172a;
  color: white;

}

th {

  padding: 18px;
  text-align: left;

}

td {

  padding: 18px;
  border-bottom:
    1px solid #eef2f7;

}

.role-badge {

  background: #dbeafe;
  color: #1d4ed8;

  padding: 6px 12px;

  border-radius: 999px;

  font-size: 14px;
  font-weight: 600;

}

.badge-active {

  background: #dcfce7;
  color: #166534;

  padding: 6px 12px;

  border-radius: 999px;

  font-size: 14px;
  font-weight: 600;

}

.badge-inactive {

  background: #fee2e2;
  color: #991b1b;

  padding: 6px 12px;

  border-radius: 999px;

  font-size: 14px;
  font-weight: 600;

}

</style>