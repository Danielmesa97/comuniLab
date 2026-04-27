<template>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="gretting">
                <h1> Bienvenido</h1>
                <p> Tu comunidad esta al dia</p>
                <div class="header-actions">
                    <button class="icon-btn">🔔</button>
                </div>
            </div>
        </header>
        <main class="main-container">
            <section class="panel">
                <h2> No hay actividades</h2>
                <p> Las incidencias reportadas y las votaciones apareceran aquí</p>
            </section>
        </main>
        <nav class="bottom-nav">
            <router-link to="/dashboard" class="nav-item">
                <span class="icon">🏠</span>
                <span> Inicio</span> 
            </router-link>
            <router-link to="/incidencias" class="nav-item">
                <span class="icon">⚠️</span>
                <span>Incidencias</span>
            </router-link>
            <router-link to="/votaciones" class="nav-item">
                <span class="icon">🗳️</span>
                <span>Votaciones</span>
            </router-link>
            <router-link to="/incidencias" class="nav-item">
                <span class="icon">👤</span>
                <span>Perfil</span>
            </router-link>
        </nav>
    </div>
</template>

<style scoped>
    .dashboard-container{
        display:flex;
        flex-direction: column;
        width: 100%;
        min-height: 100vh;
        background-color: #f2f2f7;
    }
    .dashboard-header{
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 5%;
        background: white;
        border-bottom: 2px solid #e5e5e5;
    }
    .gretting h1{
        font-size: 24px; 
        font-weight: 600px; 
        margin: 0; 
    }

    .gretting p{
        font-size: 12px; 
        margin: 3px 0 0; color: 
        #8e8e8e;
    }
    .icon-btn{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 30px;
        width: 30px;
        font-size: 15px;
        background: #f2f2f7;
        border-radius: 50%;
        cursor: pointer;

    }
    .main-container{
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 20px 5%;
        box-sizing: border-box;
        
    }
    .panel{
        text-align:center;
        padding: 40px 20px;
        border-radius: 20px;
        background: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        width: 100%;
    }

    .panel h2{
        font-size: 18px;
        font-weight: 400px;
        margin-bottom: 10px;
    }

    .panel p{
        font-size:14px;
        color:#8e8e8e;
        max-width: 400px;
        margin: 0 auto;
    }
    .bottom-nav{
        display: flex;
        justify-content: space-around;
        align-items: center;
        height: 60px;
        border-top: 2px, solid #e5e5e5;
        background: white;
        width: 100%;
        position: sticky;
        bottom: 0;
    }
    .nav-item{
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #8e8e8e;
        flex: 1;
    }
    .icon{
        font-size: 20px;
        margin-bottom: 5px;
    }

    .nav-item span:last-child{
        font-size: 12px;
        font-weight: 500px;
    }
    /* Responsive en escritorio*/
    @media (min-width: 1024px){
        .dashboard-header, .main-container{         
            padding-left: 10%;
            padding-right: 10%;
        }
        
    }
</style>