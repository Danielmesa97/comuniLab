<template>
    <div class="main-container">
      <div class="form-card">
        
        <div class="logo-container">
          <div class="logo-circle">
            <span class="logo-placeholder">LOGO</span>
          </div>
        </div>
  
        <div class="tab-selector">
          <button 
            @click="isLogin = true" 
            :class="['tab-button', isLogin ? 'active' : 'inactive']"
          >
            Iniciar Sesión
          </button>
          <button 
            @click="isLogin = false" 
            :class="['tab-button', !isLogin ? 'active' : 'inactive']"
          >
            Registrarse
          </button>
        </div>
  
        <form @submit.prevent="handleSubmit" class="login-form">
          
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" v-model="email" class="form-input" placeholder="correo@ejemplo.com" required>
          </div>
  
          <div class="form-group">
            <label class="form-label">Contraseña</label>
            <input type="password" v-model="password" class="form-input" placeholder="Ingresa tu contraseña" required>
          </div>
  
          <div class="forgot-password-container">
            <a href="#" class="forgot-password-link">¿Olvidaste tu contraseña?</a>
          </div>
  
          <div class="submit-container">
            <button type="submit" class="submit-button">
              {{ isLogin ? 'Acceder' : 'Crear cuenta' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  const isLogin = ref(true)
  const email = ref('')
  const password = ref('')
  
  const handleSubmit = () => {
    console.log('Enviando datos:', { 
      email: email.value, 
      password: password.value, 
      mode: isLogin.value ? 'login' : 'register' 
    })
  }
  </script>
  
  <style scoped>
  /* Pegamos aquí el CSS anterior, pero adaptado para Vue */
  .main-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #333; /* Un gris oscuro para resaltar la tarjeta */
  }
  
  .form-card {
    background-color: #ffffff;
    width: 350px;
    min-height: 600px;
    border-radius: 40px;
    padding: 40px 25px;
    display: flex;
    flex-direction: column;
  }
  
  .logo-container {
    display: flex;
    justify-content: center;
    margin-bottom: 25px;
  }
  
  .logo-circle {
    width: 80px;
    height: 80px;
    background-color: #f2f2f7;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .tab-selector {
    display: flex;
    background-color: #f2f2f7;
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 30px;
  }
  
  .tab-button {
    flex: 1;
    border: none;
    padding: 10px;
    font-size: 14px;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
  }
  
  .active { background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
  .inactive { background-color: transparent; color: #888; }
  
  .form-group { margin-bottom: 20px; text-align: left; }
  .form-label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 13px; }
  .form-input {
    width: 100%;
    padding: 15px;
    border-radius: 12px;
    border: 1px solid #eee;
    background-color: #f9f9f9;
  }
  
  .forgot-password-container { text-align: right; margin-bottom: 20px; }
  .forgot-password-link { font-size: 12px; color: #555; text-decoration: none; }
  
  .submit-container { margin-top: auto; }
  .submit-button {
    width: 100%;
    padding: 15px;
    background-color: #080a13;
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: bold;
    cursor: pointer;
  }
  </style>