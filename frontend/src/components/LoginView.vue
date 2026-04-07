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
  
        <form @submit.prevent="handleSubmit" class="auth-form">
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
  const isLogin = ref(true)
  const email = ref('')
  const password = ref('')
  
  const handleSubmit = () => {
    console.log("Enviando...", email.value)
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
    background-color: #1a1a1a; /* Fondo oscuro para resaltar la tarjeta */
    padding: 20px;
  }
  
  /* La Tarjeta (Form Card) */
  .form-card {
    background-color: #ffffff;
    width: 100%;
    max-width: 400px; /* Tamaño máximo en PC */
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
      padding: 0; /* Quitamos espacio exterior en móvil */
      background-color: #ffffff; /* Opcional: fondo blanco en móvil para que parezca app nativa */
    }
    .form-card {
      max-width: 100%;
      height: 100vh; /* Ocupa todo el alto en móvil */
      border-radius: 0; /* Quitamos bordes redondeados si es pantalla completa */
      box-shadow: none;
    }
  }
  </style>