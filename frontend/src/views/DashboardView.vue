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
            <router-link to="/incidencias" class="nav-item">
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
        background-color: #f2f2f7;
        overflow:hidden
    }
    .dashboard-header{
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e5e5e5;
        padding: 30px 15px 15px;
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
        height: 30px;
        width: 30px;
        font-size: 15px;
        background: #f2f2f7;
        border-radius: 50%;
        position: relative;
        cursor: pointer;

    }
    .main-container{
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 15px;
        overflow-y: auto;
    }
    .panel{
        text-align:center;
        padding: 30px 15px;
        border-radius: 20px;
        margin-top: 10px;
        background: white;
    }

    .panel h2{
        font-size: 14px;
        font-weight: 400px;
        margin-bottom: 10px;
    }

    .panel p{
        font-size:10px;
        color:#8e8e8e;
        margin-bottom: 20px;
    }
    .bottom-nav{
        display: flex;
        justify-content: space-around;
        height: 80px;
        padding-top: 10px;
        border-top: 2px, solid #e5e5e5;
        background: white;
    }
    .nav-item{
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 8px;
        font-weight: 400px;
        color: #8e8e8e;
    }
    .icon{
        font-size: 20px;
        margin-bottom: 5px;
    }

    /* Responsive en escritorio*/
    @media (min-width: 780px){
        .dashboard-container{
            max-width: 450px;
            margin: o auto;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
            box-shadow: 0 0 50px;
        }
    }
</style>