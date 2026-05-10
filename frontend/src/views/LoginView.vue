<template>
  <div class="page-container">
    <div class="form-card">
      <div class="logo-section">
        <img src="@/assets/comunilab.png" alt="ComuniLab" class="logo-image" />
      </div>

      <div class="tab-container">
        <button type="button" @click="isLogin = true" class="tab-btn" :class="{ active: isLogin }">
          Iniciar Sesión
        </button>
        <button
          type="button"
          @click="isLogin = false"
          class="tab-btn"
          :class="{ active: !isLogin }"
        >
          Solicitar Acceso
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="auth-form">
        <!-- 🔐 LOGIN -->
        <template v-if="isLogin">
          <!-- EMAIL -->
          <div class="input-group">
            <label>Email</label>
            <input type="email" v-model="email" required />
          </div>

          <!-- MENSAJE UX -->
          <p v-if="mensaje" style="color: red; margin-bottom: 10px">
            {{ mensaje }}
          </p>

          <!-- BOTÓN CONFIGURAR PASSWORD -->
          <button
            v-if="needsPassword && userActivo"
            type="button"
            @click="irAConfigurarPassword"
            class="main-btn"
            style="margin-bottom: 10px"
          >
            Configurar contraseña
          </button>

          <!-- CONTRASEÑA (solo si procede) -->
          <div class="input-group" v-if="!needsPassword">
            <label>Contraseña</label>
            <input type="password" v-model="password" required />
          </div>

          <!-- BOTÓN LOGIN -->
          <div class="action-area">
            <button type="submit" class="main-btn">Acceder</button>
          </div>
        </template>

        <!-- 📝 SOLICITUD -->
        <template v-else>
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
            <input
              type="text"
              inputmode="numeric"
              pattern="[0-9]*"
              v-model="form.comunidad_id"
              placeholder="Introduce ID de comunidad"
            />
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

          <div class="action-area">
            <button type="submit" class="main-btn">Enviar solicitud</button>
          </div>
        </template>
      </form>
    </div>
  </div>
</template>

<script setup>
const mensaje = ref('')
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { apiUrl } from '@/lib/api'

const isLogin = ref(true)
const viviendas = ref([])

//  LOGIN
const email = ref('')
const password = ref('')
// 🔥 UX PASSWORD
const needsPassword = ref(false)
const userActivo = ref(false)

// 📝 SOLICITUD
const form = ref({
  nombre: '',
  email: '',
  role: '',
  vivienda_id: '',
  comunidad_id: '',
})

const router = useRouter()

//  CARGAR VIVIENDAS SEGÚN COMUNIDAD
const cargarViviendas = async () => {
  if (!form.value.comunidad_id) {
    viviendas.value = []
    return
  }

  try {
    const res = await fetch(`${apiUrl('/api/viviendas')}?comunidad_id=${form.value.comunidad_id}`)

    const data = await res.json()

    viviendas.value = data
  } catch (error) {
    console.error('Error cargando viviendas:', error)
  }
}

const checkUser = async () => {
  if (!email.value) return

  try {
    const res = await fetch(apiUrl('/api/check-user'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        email: email.value,
      }),
    })

    const data = await res.json()

    // NO EXISTE
    if (data.exists === false) {
      mensaje.value = ''
      needsPassword.value = false
      userActivo.value = false
      return
    }

    // NO ACTIVO
    if (data.activo === false) {
      mensaje.value = 'Tu cuenta aún no ha sido aprobada'
      needsPassword.value = false
      userActivo.value = false
      return
    }

    // SIN PASSWORD
    if (data.status === 'needs_password') {
      mensaje.value = 'Debes crear tu contraseña primero'
      needsPassword.value = true
      userActivo.value = true
      return
    }

    // TODO OK
    mensaje.value = ''
    needsPassword.value = false
    userActivo.value = true
  } catch (err) {
    console.error(err)
  }
}

// OBSERVAR CAMBIOS EN comunidad_id
watch(
  () => form.value.comunidad_id,
  () => {
    cargarViviendas()
  },
)
watch(email, () => {
  checkUser()
})

const irAConfigurarPassword = () => {
  router.push(`/set-password?email=${email.value}`)
}
// SUBMIT
const handleSubmit = async () => {
  const url = isLogin.value ? apiUrl('/api/login') : apiUrl('/api/solicitudes')

  // 🔥 BLOQUEAR LOGIN SI NO TIENE PASSWORD
  if (isLogin.value && needsPassword.value && userActivo.value) {
    alert('Debes configurar tu contraseña primero')
    return
  }

  const payload = isLogin.value
    ? {
        email: email.value,
        password: password.value,
      }
    : form.value

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(payload),
    })

    const data = await response.json()

    if (!response.ok) {
      // SI NECESITA PASSWORD
      if (data.needs_password) {
        localStorage.setItem('email_temp', data.email)
        router.push('/set-password')
        return
      }

      alert(data.message)
      return
    }

    
    // LOGIN
    if (isLogin.value) {

      localStorage.setItem('auth_token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))

      // SUPERADMIN
      if (data.user.role === 'superadmin') {

        router.push('/superadmin').then(() => {
          window.location.reload()
        })

      }

      //  RESTO DE USUARIOS
      else {

        router.push('/dashboard').then(() => {
          window.location.reload()
        })

      }

    }
    // SOLICITUD
    else {
      alert('Solicitud enviada correctamente 👍')

      form.value = {
        nombre: '',
        email: '',
        role: '',
        vivienda_id: '',
        comunidad_id: '',
      }

      viviendas.value = []

      isLogin.value = true
    }
  } catch (error) {
    console.error(error)
    alert('Error de conexión con el servidor')
  }
}
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.page-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  min-height: 100vh;
  margin: 0 auto;
  padding: 40px;
  background: #f2f2f7;
}

/* ---------- DESKTOP ---------- */
.form-card {
  background: white;
  width: 100%;
  max-width: 800px;
  padding: 70px 100px;
  border-radius: 30px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.password-warning {
  margin-top: 10px;
  padding: 12px;
  background: #fff3cd;
  border-radius: 10px;
}

.auth-form {
  max-width: 700px;
  margin: auto;
}

/* logo */
.logo-section {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  margin-bottom: 30px;
}

.logo-image {
  width: 200px;
  max-width: 100%;
  height: auto;
  display: block;
}

/* tabs */
.tab-container {
  display: flex;
  background: #f2f2f7;
  border-radius: 14px;
  padding: 5px;
  margin-bottom: 30px;
}

.tab-btn {
  flex: 1;
  border: none;
  background: transparent;
  padding: 14px;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
}

.tab-btn.active {
  background: white;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

/* inputs */
.input-group {
  margin-bottom: 20px;
}

.input-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #333;
}

/* 🔥 INPUT + SELECT UNIFICADOS */
.input-group input,
.input-group select {
  width: 100%;
  padding: 14px 16px;
  border-radius: 12px;
  border: 1px solid #ddd;
  background: #fafafa;
  font-size: 16px;
  transition: all 0.2s ease;
}

/* focus bonito */
.input-group input:focus,
.input-group select:focus {
  outline: none;
  border-color: #007aff;
  background: white;
  box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
}

/* placeholder estilo */
.input-group input::placeholder {
  color: #aaa;
}

/* select flechita más limpia */
.input-group select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='%238e8e93' viewBox='0 0 16 16'%3E%3Cpath d='M3.204 5h9.592L8 10.481 3.204 5z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 16px;
  cursor: pointer;
}

/* estado deshabilitado */
.input-group select:disabled {
  background: #eee;
  cursor: not-allowed;
}

/* botón */
.main-btn {
  width: 100%;
  padding: 18px;
  background: #080a13;
  color: white;
  border: none;
  border-radius: 14px;
  font-size: 17px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s ease;
}

.main-btn:hover {
  background: #1c1f2b;
  transform: translateY(-1px);
}

/* ---------- TABLET ---------- */
@media (max-width: 768px) {
  .form-card {
    max-width: 500px;
    padding: 40px;
  }
}

/* ---------- MÓVIL ---------- */
@media (max-width: 480px) {
  .page-container {
    padding: 0;
    background: white;
  }

  .form-card {
    max-width: 100%;
    min-height: 100vh;
    padding: 35px 25px;
    border-radius: 0;
    box-shadow: none;
  }
}
</style>
