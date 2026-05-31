<template>

  <div class="layout">

    <!-- SIDEBAR -->

    <aside class="sidebar">

      <div>

        <h2>⚙️ SuperAdmin</h2>

        <nav>

          <router-link to="/superadmin">
            🏠 Inicio
          </router-link>

          <router-link to="/superadmin/comunidades">
            🏢 Comunidades
          </router-link>

          <router-link to="/superadmin/viviendas">
            🏠 Viviendas
          </router-link>

          <router-link to="/superadmin/usuarios">
            👥 Usuarios
          </router-link>

        </nav>

      </div>

      <button @click="logout">
        🚪 Cerrar sesión
      </button>

    </aside>

    <!-- CONTENIDO -->

    <main class="content">

      <router-view />

    </main>

  </div>

</template>

<script setup>

import { useRouter } from 'vue-router'

const router = useRouter()

const logout = () => {

  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')

  router.push('/')

}

</script>

<style scoped>

.layout{
  display:flex;
  min-height:100vh;
}

/* SIDEBAR */

.sidebar{
  width:250px;
  background:#17233c;
  color:white;

  padding:30px 20px;

  display:flex;
  flex-direction:column;
  justify-content:space-between;
}

.sidebar h2{
  margin-bottom:40px;
}

.sidebar nav{
  display:flex;
  flex-direction:column;
  gap:15px;
}

.sidebar a{
  color:white;
  text-decoration:none;

  padding:12px;
  border-radius:10px;

  transition:0.2s;
}

.sidebar a:hover{
  background:rgba(255,255,255,0.1);
}

.router-link-active{
  background:#2563eb;
}

.sidebar button{
  background:#ef4444;
  border:none;
  color:white;

  padding:12px;
  border-radius:10px;

  cursor:pointer;
}

/* CONTENT */

.content{
  flex:1;
  background:#f3f4f6;
}

/* MOBILE */

@media (max-width:768px){

  .layout{
    flex-direction:column;
  }

  .sidebar{
    width:100%;
  }

}

</style>