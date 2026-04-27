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
                        
                        <button 
                            class="vote-action-btn" 
                            :class="{ 'voted-btn': isVoted(v.id) }"
                            :disabled="isVoted(v.id)"
                            @click="abrirModal(v)"
                        >
                            {{ isVoted(v.id) ? 'Ya has votado' : 'Votar ahora' }}
                        </button>
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
const mostrarModal = ref(false); 
const votacionSeleccionada = ref(null);

// 1. VARIABLE PARA GUARDAR LOS IDs VOTADOS
const votacionesVotadas = ref([]);

// Función para saber si ya se votó una encuesta específica
const isVoted = (id) => {
    return votacionesVotadas.value.includes(id);
};

const getVotaciones = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        
        const response = await fetch('http://127.0.0.1:8000/api/votaciones', {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}` // Enviamos el token para que Laravel sepa quiénes somos
            }
        });
        
        const data = await response.json();
        
        // Ahora data tiene dos partes:
        votaciones.value = data.votaciones;       // Las tarjetas
        votacionesVotadas.value = data.mis_votos; // Los IDs que ya votamos antes
        
    } catch (error) {
        console.error("Error cargando votaciones:", error);
    }
};

const abrirModal = (votacion) => {
    votacionSeleccionada.value = votacion;
    mostrarModal.value = true;
};

const enviarVoto = async (opcion) => {
    try {
        const token = localStorage.getItem('auth_token'); 

        const response = await fetch('http://127.0.0.1:8000/api/votaciones/votar', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                votacion_id: votacionSeleccionada.value.id,
                opcion: opcion
            })
        });

        if (response.ok) {
            // 2. AÑADIMOS EL ID A LA LISTA DE VOTADOS
            votacionesVotadas.value.push(votacionSeleccionada.value.id);
            
            // 3. CERRAMOS EL MODAL
            mostrarModal.value = false;
            
            alert("¡Voto registrado correctamente!");
        } else {
            const errorData = await response.json();
            alert("Error: " + (errorData.message || "No se pudo procesar el voto"));
        }
    } catch (error) {
        console.error("Error al enviar el voto:", error);
    }
};

onMounted(getVotaciones);
</script>

<style scoped>
    /* ... (Tus estilos base) ... */
    .dashboard-container { display:flex; flex-direction: column; width: 100%; min-height: 100vh; background-color: #f2f2f7; }
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 5%; background: white; border-bottom: 2px solid #e5e5e5; }
    .main-container { flex: 1; padding: 20px 5%; }
    .votacion-card { background: white; border-radius: 15px; padding: 20px; margin-bottom: 15px; display: flex; flex-direction: column; gap: 15px; }
    
    /* BOTÓN NORMAL */
    .vote-action-btn { 
        background: #007aff; 
        color: white; 
        border: none; 
        padding: 10px; 
        border-radius: 10px; 
        font-weight: 600; 
        cursor: pointer; 
        transition: background 0.3s;
    }

    /* ESTILO PARA BOTÓN GRIS (CUANDO YA SE VOTÓ) */
    .voted-btn, .vote-action-btn:disabled {
        background-color: #c7c7cc !important; /* Gris iOS */
        color: #8e8e93 !important;
        cursor: not-allowed;
    }

    /* ESTILOS DEL MODAL */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(3px); }
    .modal-content { background: white; padding: 30px; border-radius: 20px; width: 80%; max-width: 400px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    .modal-actions { display: flex; gap: 10px; margin: 20px 0; }
    .btn-si, .btn-no { flex: 1; padding: 15px; border: none; border-radius: 12px; font-size: 16px; font-weight: bold; cursor: pointer; }
    .btn-si { background: #e1ffdc; color: #28a745; }
    .btn-no { background: #ffdce0; color: #d73a49; }
    .btn-cancelar { background: none; border: none; color: #8e8e93; font-size: 14px; cursor: pointer; margin-top: 10px; }

    /* NAV INFERIOR */
    .bottom-nav { display: flex; justify-content: space-around; align-items: center; height: 60px; border-top: 1px solid #e5e5e5; background: white; width: 100%; position: sticky; bottom: 0; }
    .nav-item { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #8e8e8e; flex: 1; font-size: 12px; }
    .nav-item.active { color: #007aff; }
</style>