<template>
  <div class="page-container">
    <div class="form-card">
      <div class="logo-section">
        <img src="@/assets/comunilab.png" alt="ComuniLab" class="logo-image" />
      </div>

      <h2 class="title">Crear contraseña</h2>
      <p class="subtitle">Configura tu acceso a ComuniLab</p>

      <form @submit.prevent="guardarPassword" class="auth-form">
        <div class="input-group">
          <label>Nueva contraseña</label>
          <input
            type="password"
            v-model="password"
            placeholder="Introduce tu contraseña"
            required
          />
        </div>

        <div class="input-group">
          <label>Confirmar contraseña</label>
          <input
            type="password"
            v-model="confirmPassword"
            placeholder="Repite la contraseña"
            required
          />
        </div>

        <div class="action-area">
          <button class="main-btn">Guardar contraseña</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { apiUrl } from '@/lib/api'

const password = ref('')
const confirmPassword = ref('')

const router = useRouter()
const route = useRoute()

const email = ref('')

// 🔥 CARGAR EMAIL BIEN
onMounted(() => {
  if (!route.query.email) {
    alert('Error: email no encontrado')
    router.push('/')
  } else {
    email.value = route.query.email
  }
})

const guardarPassword = async () => {
  if (password.value !== confirmPassword.value) {
    alert('Las contraseñas no coinciden')
    return
  }

  try {
    const res = await fetch(apiUrl('/api/set-password'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        email: email.value, // 🔥 IMPORTANTE
        password: password.value,
      }),
    })

    const data = await res.json()

    if (!res.ok) {
      alert(data.message || 'Error')
      return
    }

    alert('Contraseña creada correctamente 🎉')
    router.push('/')
  } catch (err) {
    console.error(err)
    alert('Error de conexión')
  }
}
</script>
<style scoped>
.page-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  min-height: 100vh;
  padding: 40px;
  background: #f2f2f7;
}

.form-card {
  background: white;
  width: 100%;
  max-width: 500px;
  padding: 50px;
  border-radius: 30px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.title {
  text-align: center;
  margin-bottom: 5px;
}

.subtitle {
  text-align: center;
  color: #777;
  margin-bottom: 30px;
}

.logo-section {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.logo-image {
  width: 150px;
}

.input-group {
  margin-bottom: 20px;
}

.input-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
}

.input-group input {
  width: 100%;
  padding: 14px;
  border-radius: 12px;
  border: 1px solid #ddd;
  background: #fafafa;
}

.main-btn {
  width: 100%;
  padding: 16px;
  background: #080a13;
  color: white;
  border: none;
  border-radius: 14px;
  font-weight: bold;
  cursor: pointer;
}

.main-btn:hover {
  background: #1c1f2b;
}
</style>
