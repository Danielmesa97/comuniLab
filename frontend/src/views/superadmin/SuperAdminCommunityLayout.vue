<template>
  <div class="community-layout">

    <aside class="sidebar">

      <div>
        <h2>🏢 Comunidad</h2>

        <p class="community-name">
          {{ nombreComunidad || 'Modo comunidad' }}
        </p>

        <nav>
          <router-link to="/superadmin/comunidad/dashboard">
            🏠 Dashboard
          </router-link>

          <router-link to="/superadmin/comunidad/anuncios">
            📢 Anuncios
          </router-link>

          <router-link to="/superadmin/comunidad/incidencias">
            ⚠️ Incidencias
          </router-link>

          <router-link to="/superadmin/comunidad/votaciones">
            🗳️ Votaciones
          </router-link>
        </nav>
      </div>

      <div class="bottom-actions">
        <button class="back" @click="volverPanel">
          ← Panel superadmin
        </button>

        <button class="danger" @click="salirModoComunidad">
          Salir comunidad
        </button>
      </div>

    </aside>

    <main class="content">
      <router-view />
    </main>

  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const nombreComunidad = localStorage.getItem('superadmin_comunidad_nombre')

const volverPanel = () => {
  router.push('/superadmin')
}

const salirModoComunidad = () => {
  localStorage.removeItem('superadmin_comunidad_id')
  localStorage.removeItem('superadmin_comunidad_nombre')

  router.push('/superadmin')
}
</script>

<style scoped>
.community-layout{
  display:flex;
  min-height:100vh;
  background:#f3f4f6;
}

.sidebar{
  width:260px;
  background:#17233c;
  color:white;
  padding:24px 18px;

  display:flex;
  flex-direction:column;
  justify-content:space-between;
}

.sidebar h2{
  margin:0 0 8px;
}

.community-name{
  font-size:14px;
  color:#fbbf24;
  margin-bottom:30px;
  font-weight:bold;
}

nav{
  display:flex;
  flex-direction:column;
  gap:12px;
}

nav a{
  color:white;
  text-decoration:none;
  padding:12px;
  border-radius:10px;
  transition:.2s;
}

nav a:hover{
  background:rgba(255,255,255,.12);
}

.router-link-active{
  background:#2563eb;
}

.bottom-actions{
  display:flex;
  flex-direction:column;
  gap:10px;
}

button{
  border:none;
  padding:12px;
  border-radius:10px;
  cursor:pointer;
  font-weight:bold;
}

.back{
  background:white;
  color:#17233c;
}

.danger{
  background:#ef4444;
  color:white;
}

.content{
  flex:1;
}

/* móvil */
@media(max-width:768px){
  .community-layout{
    flex-direction:column;
  }

  .sidebar{
    width:100%;
  }

  nav{
    display:grid;
    grid-template-columns:repeat(2,1fr);
  }
}
</style>