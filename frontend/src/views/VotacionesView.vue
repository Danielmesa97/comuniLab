<template>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="gretting">
                <h1>Votaciones</h1>
                <p>Decide el futuro de tu comunidad</p>
            </div>
        </header>

        <main class="main-container">
            <section class="votaciones-wrapper">
                <div v-if="votaciones.length > 0" class="list-container">
                    <div v-for="v in votaciones" :key="v.id" class="votacion-card">
                        <div class="card-info">
                            <h3>{{ v.titulo }}</h3>
                            <p>{{ v.descripcion }}</p>
                            <span class="status-badge" :class="v.estado">{{ v.estado }}</span>
                        </div>
                        <button class="vote-action-btn" @click="abrirModal(v)">Votar ahora</button>
                    </div>
                </div>

                <div v-else class="panel">
                    <h2>No hay votaciones activas</h2>
                    <p>Las propuestas aparecerán aquí.</p>
                </div>
            </section>
        </main>

        <div v-if="mostrarModal" class="modal-overlay">
            <div class="modal-content">
                <h3>¿Cuál es tu voto?</h3>
                <p>Estás votando en: <strong>{{ votacionSeleccionada?.titulo }}</strong></p>
                
                <div class="modal-actions">
                    <button class="btn-si" @click="enviarVoto('si')">👍 Sí</button>
                    <button class="btn-no" @click="enviarVoto('no')">👎 No</button>
                </div>
                
                <button class="btn-cancelar" @click="mostrarModal = false">Cancelar</button>
            </div>
        </div>

        <nav class="bottom-nav">
            <router-link to="/dashboard" class="nav-item">
                <span class="icon">🏠</span><span> Inicio</span> 
            </router-link>
            <router-link to="/incidencias" class="nav-item">
                <span class="icon">⚠️</span><span>Incidencias</span>
            </router-link>
            <router-link to="/votaciones" class="nav-item active">
                <span class="icon">🗳️</span><span>Votaciones</span>
            </router-link>
            <router-link to="/incidencias" class="nav-item">
                <span class="icon">👤</span><span>Perfil</span>
            </router-link>
        </nav>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const votaciones = ref([]);
const mostrarModal = ref(false); // Controla si el pop-up se ve
const votacionSeleccionada = ref(null); // Guarda la votación que el usuario clicó

const getVotaciones = async () => {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/votaciones');
        const data = await response.json();
        votaciones.value = data;
    } catch (error) {
        console.error("Error:", error);
    }
};

// Función para preparar el modal
const abrirModal = (votacion) => {
    votacionSeleccionada.value = votacion;
    mostrarModal.value = true;
};

// Función para procesar el voto (por ahora solo alerta, luego lo conectamos a la API)
const enviarVoto = (opcion) => {
    alert(`Has votado "${opcion.toUpperCase()}" en: ${votacionSeleccionada.value.titulo}`);
    
    // Aquí es donde haríamos el fetch al backend para guardar el voto
    
    mostrarModal.value = false; // Cerramos el modal
};

onMounted(getVotaciones);
</script>

<style scoped>
    /* ... (Tus estilos anteriores se mantienen) ... */

    .dashboard-container { display:flex; flex-direction: column; width: 100%; min-height: 100vh; background-color: #f2f2f7; }
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 5%; background: white; border-bottom: 2px solid #e5e5e5; }
    .main-container { flex: 1; padding: 20px 5%; }
    .votacion-card { background: white; border-radius: 15px; padding: 20px; margin-bottom: 15px; display: flex; flex-direction: column; gap: 15px; }
    .vote-action-btn { background: #007aff; color: white; border: none; padding: 10px; border-radius: 10px; font-weight: 600; cursor: pointer; }
    
    /* ESTILOS DEL MODAL */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Oscurece el fondo */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        backdrop-filter: blur(3px); /* Efecto cristal de iOS */
    }

    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 20px;
        width: 80%;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        margin: 20px 0;
    }

    .btn-si, .btn-no {
        flex: 1;
        padding: 15px;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.1s;
    }

    .btn-si { background: #e1ffdc; color: #28a745; }
    .btn-no { background: #ffdce0; color: #d73a49; }
    
    .btn-si:active, .btn-no:active { transform: scale(0.95); }

    .btn-cancelar {
        background: none;
        border: none;
        color: #8e8e93;
        font-size: 14px;
        cursor: pointer;
        margin-top: 10px;
    }

    /* NAV INFERIOR */
    .bottom-nav { display: flex; justify-content: space-around; align-items: center; height: 60px; border-top: 1px solid #e5e5e5; background: white; width: 100%; position: sticky; bottom: 0; }
    .nav-item { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #8e8e8e; flex: 1; font-size: 12px; }
    .nav-item.active { color: #007aff; }
</style>