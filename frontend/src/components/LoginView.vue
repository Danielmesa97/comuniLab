<template>
  <div class="page-container">
    <div class="form-card">
      
      <div class="logo-section">
        <div class="logo-circle">
          <span>LOGO</span>
        </div>
      </div>

      <div class="tab-container">
        <button 
          type="button" 
          @click="isLogin = true" 
          class="tab-btn" 
          :class="{ 'active': isLogin }"
        >
          Iniciar Sesión
        </button>
        <button 
          type="button" 
          @click="isLogin = false" 
          class="tab-btn" 
          :class="{ 'active': !isLogin }"
        >
          Registrarse
        </button>
      </div>

      <div v-if="errorMessage" class="error-box">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleSubmit" class="auth-form">
        
        <div v-if="!isLogin" class="input-group">
          <label>Nombre</label>
          <input type="text" v-model="name" placeholder="Tu nombre completo" required>
        </div>

        <div class="input-group">
          <label>Email</label>
          <input type="email" v-model="email" placeholder="correo@ejemplo.com" required>
        </div>

        <div class="input-group">
          <label>Contraseña</label>
          <input type="password" v-model="password" placeholder="Ingresa tu contraseña" required>
        </div>

        <div class="extra-links">
          <a href="#">¿Olvidaste tu contraseña?</a>
        </div>

        <div class="action-area">
          <button type="submit" class="main-btn">
            {{ isLogin ? 'Acceder' : 'Crear cuenta' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router';

const isLogin = ref(true)
const name = ref('') // Añadimos la variable para el nombre
const email = ref('')
const password = ref('')
const errorMessage = ref('') // Añadimos la variable para los errores
const router = useRouter()

const handleSubmit = async () => {
  // 1. Limpiamos errores de intentos anteriores
  errorMessage.value = '';

  // 2. Decidimos a qué ruta apuntar
  const url = isLogin.value
    ? 'http://127.0.0.1:8000/api/login'
    : 'http://127.0.0.1:8000/api/registro';

  // 3. Preparamos la mochila de datos
  const payload = {
    email: email.value,
    password: password.value
  };

  // Si estamos en la pestaña de registro, metemos el nombre en la mochila
  if (!isLogin.value) {
    payload.name = name.value;
  }

  try {
    // 4. Enviamos la petición a Laravel
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    const data = await response.json();

    // 5. Si Laravel se enfada (ej. correo repetido o credenciales falsas)
    if (!response.ok) {
      if (data.message) {
        errorMessage.value = data.message;
      } else {
        errorMessage.value = "Contraseña o correo erroneo.";
      }
      return; // Cortamos la función aquí
    }

    // 6. ¡Todo ha ido perfecto! Guardamos el token VIP
    if (data.token) {
      localStorage.setItem('auth_token', data.token);
    }

    // 7. Navegamos al Dashboard
    console.log("Acceso concedido, redirigiendo...");
    router.push('/dashboard');

  } catch (error) {
    errorMessage.value = "Error de conexión. ¿Está el servidor Laravel encendido?";
    console.error(error);
  }
}
</script>

<style scoped>
/* Contenedor que ocupa toda la pantalla */
.page-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100%;
  background-color: #1a1a1a;
  padding: 20px;
}

/* La Tarjeta (Form Card) */
.form-card {
  background-color: #ffffff;
  width: 100%;
  max-width: 400px;
  min-height: 550px;
  border-radius: 40px;
  padding: 40px 30px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 15px 35px rgba(0,0,0,0.3);
}

/* Logo */
.logo-section {
  display: flex;
  justify-content: center;
  margin-bottom: 30px;
}
.logo-circle {
  width: 80px;
  height: 80px;
  background-color: #f0f0f5;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #888;
  font-weight: bold;
}

/* Tabs Estilizadas */
.tab-container {
  display: flex;
  background-color: #f2f2f7;
  border-radius: 15px;
  padding: 5px;
  margin-bottom: 30px;
}
.tab-btn {
  flex: 1;
  border: none;
  padding: 12px;
  border-radius: 12px;
  cursor: pointer;
  background: transparent;
  color: #8e8e93;
  transition: all 0.3s ease;
  font-size: 14px;
}
.tab-btn.active {
  background-color: #ffffff;
  color: #000;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* ESTILO NUEVO PARA LA CAJA DE ERROR */
.error-box {
  background-color: #ff4d4f;
  color: white;
  padding: 12px;
  border-radius: 12px;
  margin-bottom: 20px;
  font-size: 14px;
  text-align: center;
  font-weight: 500;
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Inputs */
.input-group {
  margin-bottom: 20px;
}
.input-group label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
  font-weight: 600;
}
.input-group input {
  width: 100%;
  padding: 15px;
  border-radius: 12px;
  border: 1px solid #e5e5ea;
  background-color: #f9f9fb;
  font-size: 16px;
}

/* Links */
.extra-links {
  text-align: right;
  margin-bottom: 30px;
}
.extra-links a {
  font-size: 13px;
  color: #666;
  text-decoration: none;
}

/* Botón Principal al final */
.action-area {
  margin-top: auto;
}
.main-btn {
  width: 100%;
  padding: 18px;
  background-color: #080a13;
  color: white;
  border: none;
  border-radius: 15px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.1s;
}
.main-btn:active {
  transform: scale(0.98);
}

/* --- MEDIA QUERIES PARA MÓVIL --- */
@media (max-width: 480px) {
  .page-container {
    padding: 0;
  }
  .form-card {
    max-width: 100%;
    height: 100vh;
    border-radius: 0;
    box-shadow: none;
  }
}
</style>