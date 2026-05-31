<template>

  <div class="dashboard">

    <h1>Panel SuperAdmin</h1>

    <div class="cards">

      <router-link
        to="/superadmin/comunidades"
        class="card"
      >
        <h3>🏢 Comunidades</h3>
        <p>Gestionar comunidades</p>
      </router-link>

      <router-link
        to="/superadmin/viviendas"
        class="card"
      >
        <h3>🏠 Viviendas</h3>
        <p>Gestionar viviendas</p>
      </router-link>

      <router-link
        to="/superadmin/usuarios"
        class="card"
      >
        <h3>👥 Usuarios</h3>
        <p>Gestionar usuarios</p>
      </router-link>

    </div>

  </div>

</template>

<style scoped>

.admin-layout{
  display:flex;
  min-height:100vh;
  background:#f5f6fa;
}

/* SIDEBAR */

.sidebar{
  width:250px;
  background:#1e293b;
  color:white;
  padding:30px 20px;

  display:flex;
  flex-direction:column;
  gap:15px;
}

.sidebar h2{
  margin-bottom:20px;
}

.sidebar a{
  color:white;
  text-decoration:none;
  padding:12px;
  border-radius:10px;
  transition:0.2s;
}

.sidebar a:hover{
  background:#334155;
}

.sidebar button{
  margin-top:auto;
  padding:12px;
  border:none;
  border-radius:10px;
  background:#ef4444;
  color:white;
  cursor:pointer;
}

/* CONTENT */

.content{
  flex:1;
  padding:40px;
}

.cards{
  margin-top:30px;

  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
  gap:20px;
}

.card{
  background:white;
  padding:25px;
  border-radius:20px;
  box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

</style>