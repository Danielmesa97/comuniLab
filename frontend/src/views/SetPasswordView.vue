<template>
  <div class="page-container">
    <div class="form-card">
      <h2>Crear contraseña</h2>

      <form @submit.prevent="guardarPassword">
        <div class="input-group">
          <label>Nueva contraseña</label>
          <input type="password" v-model="password" required>
        </div>

        <button class="main-btn">Guardar contraseña</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const password = ref('')
const router = useRouter()

const guardarPassword = async () => {
  const email = localStorage.getItem('email_temp')

  try {
    const res = await fetch('http://127.0.0.1:8000/api/set-password', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        email,
        password: password.value
      })
    })

    const data = await res.json()

    if (!res.ok) {
      alert(data.message || 'Error')
      return
    }

    alert('Contraseña creada correctamente')

    localStorage.removeItem('email_temp')
    router.push('/')
  } catch (err) {
    console.error(err)
  }
}
</script>