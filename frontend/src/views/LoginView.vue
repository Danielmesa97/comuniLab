<template>
  <div class="page-container">
    <div class="form-card">
      
      <div class="logo-section">
        <img src="@/assets/comunilab.png" alt="ComuniLab" class="logo-image">
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

      <form @submit.prevent="handleSubmit" class="auth-form">
        
        <div class="input-group" v-if="!isLogin">
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
const name = ref('') // Añadido para guardar el nombre en el registro
const email = ref('')
const password = ref('')
const router = useRouter()

const handleSubmit = async () => {
  // Elegimos la ruta según la pestaña en la que estemos
  const url = isLogin.value
    ? 'http://127.0.0.1:8000/api/login'
    : 'http://127.0.0.1:8000/api/registro'

  // Preparamos el paquete de datos
  const payload = {
    email: email.value,
    password: password.value
  }

  // Si estamos registrando, añadimos el nombre al paquete
  if (!isLogin.value) {
    payload.name = name.value
  }

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })

    const data = await response.json()

    // Si Laravel devuelve un error (ej: correo repetido, contraseña corta)
    if (!response.ok) {
      alert(data.message || "Error en la autenticación")
      return
    }

    // Si todo va bien, guardamos el Token VIP
    if (data.token) {
      localStorage.setItem('auth_token', data.token)
    }

    // Y teletransportamos al usuario al Dashboard
    router.push('/dashboard')

  } catch (error) {
    alert("Error de conexión. ¿Está el servidor de Laravel encendido?")
    console.error(error)
  }
}
</script>

<style scoped>
*{
box-sizing:border-box;
}

.page-container{
display:flex;
justify-content:center;
align-items:center;
width:100%;
min-height:100vh;
margin:0 auto;
padding:40px;
background:#f2f2f7;
}


/* ---------- DESKTOP ---------- */
.form-card{
background:white;
width:100%;
max-width:800px;   /* antes 540 */
padding:70px 100px;
border-radius:30px;
box-shadow:0 15px 35px rgba(0,0,0,.15);
}

/* centra mejor el formulario dentro de la tarjeta */
.auth-form{
max-width:700px;
margin:auto;
}


/* logo */
.logo-section{
display:flex;
justify-content:center;
align-items:center;
width:100%;
margin-bottom:30px;
}

.logo-image{
width:200px;
max-width:100%;
height:auto;
display:block;
}



/* tabs */
.tab-container{
display:flex;
background:#f2f2f7;
border-radius:14px;
padding:5px;
margin-bottom:30px;
}

.tab-btn{
flex:1;
border:none;
background:transparent;
padding:14px;
border-radius:12px;
cursor:pointer;
}

.tab-btn.active{
background:white;
box-shadow:0 4px 10px rgba(0,0,0,.08);
}


/* inputs */
.input-group{
margin-bottom:20px;
}

.input-group label{
display:block;
margin-bottom:8px;
font-weight:600;
}

.input-group input{
width:100%;
padding:18px;
border-radius:12px;
border:1px solid #ddd;
background:#fafafa;
font-size:18px;
}


/* links */
.extra-links{
text-align:right;
margin-bottom:30px;
}

.action-area{
margin-top:auto;
}


/* botón */
.main-btn{
width:100%;
padding:20px;
background:#080a13;
color:white;
border:none;
border-radius:14px;
font-size:18px;
font-weight:bold;
cursor:pointer;
}



/* ---------- PANTALLAS MUY GRANDES ---------- */
@media (min-width:1200px){

.form-card{
width:min(90vw,700px);
padding:50px 60px;
margin:auto;
}

}



/* ---------- TABLET ---------- */
@media (max-width:768px){

.form-card{
max-width:500px;
padding:40px;
}

.auth-form{
max-width:100%;
}

}



/* ---------- MÓVIL ---------- */
@media (max-width:480px){

.page-container{
padding:0;
background:white;
}

.form-card{
max-width:100%;
min-height:100vh;
padding:35px 25px;
border-radius:0;
box-shadow:none;
}

}

</style>