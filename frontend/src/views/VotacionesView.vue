<template>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="gretting">
                <h1>Votaciones</h1>
                <p>Decide el futuro de tu comunidad</p>
            </div>

            <button 
                v-if="['presidente','admin','superadmin'].includes(user?.role)"
                class="btn-add-circular" 
                @click="abrirModalCrear"
            >
                <span>+</span>
            </button>
        </header>
        
        <div class="search-container">
            <input 
                type="text" 
                v-model="textoBusqueda" 
                @input="getVotaciones" 
                placeholder="Buscar votaciones antiguas o por título..."
                class="search-input"
            >
        </div>

        <main class="main-container">
            <section class="votaciones-wrapper">
                <div v-if="votaciones.length > 0" class="list-container">
                    <div v-for="v in votaciones" :key="v.id" class="votacion-card">
                                              
                        <span class="percentage-badge">
                            👍 {{ calcularPorcentajeSi(v) }}% Sí
                        </span>

                        <div class="card-info">
                            <h3>{{ v.titulo }}</h3>
                            <p>{{ v.descripcion }}</p>
                            
                            <div class="badges-container">
                                <span class="status-badge" :class="v.estado">{{ v.estado }}</span>
                                <!-- NUEVO: Etiqueta de fecha límite -->
                                <span v-if="v.fecha_limite" class="date-badge">
                                    ⏳ Finaliza: {{ v.fecha_limite }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="acciones-votacion" style="margin-top: 15px;">

                            <!-- CASO 1: El usuario ya ha votado -->
                            <button 
                                v-if="isVoted(v.id)" 
                                class="vote-action-btn voted-btn" 
                                disabled
                            >
                                ✅ Ya has votado
                            </button>

                            <!-- CASO 2: No ha votado y la votación sigue ACTIVA -->
                            <button 
                                v-else-if="estaActiva(v.fecha_limite)" 
                                class="vote-action-btn" 
                                @click="abrirModal(v)"
                            >
                                Votar ahora
                            </button>

                            <!-- CASO 3: No ha votado, pero la votación YA CADUCÓ -->
                            <button 
                                v-else 
                                class="vote-action-btn voted-btn" 
                                disabled
                            >
                                ⏳ Votación finalizada
                            </button>

                        </div>
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
        <!-- MODAL PARA CREAR NUEVA VOTACIÓN -->
        <div v-if="mostrarModalCrear" class="modal-overlay">
            <div class="modal-content crear-modal">
                <h3>Crear Nueva Votación</h3>
                <p>Añade una propuesta para la comunidad</p>
                
                <form @submit.prevent="guardarNuevaVotacion" class="form-crear">
                    <div class="form-group">
                        <label for="titulo">Título</label>
                        <input 
                            type="text" 
                            id="titulo" 
                            v-model="nuevaVotacion.titulo" 
                            required 
                            placeholder="Ej: Pintar fachada"
                        >
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea 
                            id="descripcion" 
                            v-model="nuevaVotacion.descripcion" 
                            required 
                            placeholder="Explica los detalles de la propuesta..."
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="fecha_limite">Fecha Límite (Opcional)</label>
                        <!-- Usamos datetime-local para que el usuario elija día y hora -->
                        <input 
                            type="datetime-local" 
                            id="fecha_limite" 
                            v-model="nuevaVotacion.fecha_limite"
                        >
                    </div>
                    
                    <div class="modal-actions">
                        <button type="submit" class="btn-si">Crear Votación</button>
                        <button type="button" class="btn-no" @click="mostrarModalCrear = false">Cancelar</button>
                    </div>
                </form>
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
const user = JSON.parse(localStorage.getItem("user"))
const textoBusqueda = ref('');
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
        
        // Magia aquí: Añadimos el parámetro de búsqueda a tu URL
        const url = `http://127.0.0.1:8000/api/votaciones?buscar=${textoBusqueda.value}`;
        
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}` // Enviamos el token para que Laravel sepa quiénes somos
            }
        });
        
        const data = await response.json();
        
        // Mantenemos tu lógica intacta porque está perfecta:
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

// CREAR VOTACIÓN ---
const mostrarModalCrear = ref(false);
const nuevaVotacion = ref({
    titulo: '',
    descripcion: '',
    fecha_limite: ''
});

// Calcular el porcentaje de Sí
const calcularPorcentajeSi = (votacion) => {
    // Si nadie ha votado aún, devolvemos 0 para evitar dividir por cero
    if (!votacion.votos_count || votacion.votos_count === 0) {
        return 0;
    }
    
    const porcentaje = (votacion.votos_si_count / votacion.votos_count) * 100;
    return Math.round(porcentaje);
};

// Función para abrir el panel y limpiar el formulario
const abrirModalCrear = () => {
    nuevaVotacion.value = { titulo: '', descripcion: '', fecha_limite: '' };
    mostrarModalCrear.value = true;
};

const guardarNuevaVotacion = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        
        // Hacemos la petición POST a tu API de Laravel
        const response = await fetch('http://127.0.0.1:8000/api/votaciones', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            // Convertimos nuestro objeto reactivo a formato JSON
            body: JSON.stringify(nuevaVotacion.value)
        });

        if (response.ok) {
            // El backend nos devuelve la votación recién creada
            const nuevaVotacionCreada = await response.json();
            
            // 1. Añadimos la nueva votación a nuestra lista para que aparezca en pantalla sin recargar
            votaciones.value.push(nuevaVotacionCreada.votacion || nuevaVotacionCreada); 
            
            // 2. Cerramos el panel
            mostrarModalCrear.value = false;
            
            // 3. Reseteamos los campos del formulario para la próxima vez
            nuevaVotacion.value = { titulo: '', descripcion: '', fecha_limite: '' };
            
        } else {
            // Si Laravel devuelve un error de validación (ej. falta el título)
            const errorData = await response.json();
            alert("Error: " + (errorData.message || "No se pudo crear la votación"));
            console.error("Errores de validación:", errorData.errors);
        }
    } catch (error) {
        console.error("Error de conexión:", error);
        alert("Fallo al conectar con el servidor.");
    }
};

// Función para saber si una votación sigue activa hoy
const estaActiva = (fechaLimite) => {
    // Si la fecha límite es null (no caduca nunca), siempre está activa
    if (!fechaLimite) return true; 

    const fechaFin = new Date(fechaLimite);
    const hoy = new Date();
    
    return hoy <= fechaFin; 
};

onMounted(getVotaciones);
</script>

<style scoped>
    /* ... (Tus estilos base) ... */
    .dashboard-container { display:flex; flex-direction: column; width: 100%; min-height: 100vh; background-color: #f2f2f7; }
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 5%; background: white; border-bottom: 2px solid #e5e5e5; }
    .main-container { flex: 1; padding: 20px 5%; }
    .votacion-card { background: white; border-radius: 15px; padding: 20px; margin-bottom: 15px; display: flex; flex-direction: column; gap: 15px; position: relative;}
    
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

   

        /* ESTILOS DEL FORMULARIO DE CREACIÓN */
    .crear-modal {
        max-width: 500px;
    }

    .form-crear {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 20px;
        text-align: left; 
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    .form-group input, 
    .form-group textarea {
        padding: 12px;
        border: 1px solid #d1d1d6;
        border-radius: 8px;
        font-size: 15px;
        font-family: inherit; 
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-group input:focus, 
    .form-group textarea:focus {
        outline: none;
        border-color: #007aff;
        box-shadow: 0 0 0 2px rgba(0, 122, 255, 0.2);
    }

    
    .search-container {
    padding: 0 20px;
    margin-top: 20px;
    margin-bottom: 20px;
    }

    .search-input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 20px;
        border: 1px solid #e5e5ea;
        background-color: #f2f2f7;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        background-color: #ffffff;
        border-color: #007aff;
        box-shadow: 0 2px 8px rgba(0, 122, 255, 0.15);
    }
    
    .percentage-badge {
    position: absolute;
    top: 15px;      /* Distancia desde arriba */
    right: 15px;    /* Distancia desde la derecha */
    background-color: #e0f7fa; 
    color: #00796b; 
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Sombra suave para darle volumen */
}
</style>