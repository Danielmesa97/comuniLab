<template>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <div class="gretting">
        <h1>Votaciones</h1>
        <p>Decide el futuro de tu comunidad</p>
      </div>

      <button
        v-if="['presidente', 'admin', 'superadmin'].includes(user?.role)"
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
        placeholder="Buscar votaciones antiguas por título..."
        class="search-input"
      />
    </div>

    <main class="main-container">
      <section class="votaciones-wrapper">
        <div v-if="votaciones.length > 0" class="list-container">
          <div v-for="v in votaciones" :key="v.id" class="votacion-card">
            <button class="percentage-badge-btn" @click="verResultados(v)">
              📊 Ver Resultados ({{ calcularPorcentajeSi(v) }}% Sí)
            </button>

            <div class="card-info">
              <h3>{{ v.titulo }}</h3>
              <p>{{ v.descripcion }}</p>

              <div class="badges-container">
                <span class="status-badge" :class="v.estado">{{ v.estado }}</span>
                <span v-if="v.fecha_limite" class="date-badge">
                  ⏳ Finaliza: {{ v.fecha_limite }}
                </span>
              </div>
            </div>

            <div class="acciones-votacion" style="margin-top: 15px">
              <button v-if="isVoted(v.id)" class="vote-action-btn voted-btn" disabled>
                ✅ Ya has votado
              </button>

              <div v-else-if="estaActiva(v.fecha_limite)" class="botones-activos">
                <button class="vote-action-btn" @click="abrirModal(v)">
                  Votar ahora
                </button>
                
                <button class="delegate-action-btn" @click="iniciarDelegacion(v)">
                  🤝 Delegar voto
                </button>
              </div>

              <button v-else class="vote-action-btn voted-btn" disabled>
                ⏳ Votación finalizada
              </button>

              <div 
                v-for="del in delegacionesPendientes.filter(d => d.votacion_id === v.id)" 
                :key="del.id" 
                style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ccc;"
              >
                <p style="font-size: 13px; color: #555; margin-bottom: 8px;">
                  🤝 El <strong>Piso {{ del.vivienda.nombre }}</strong> ha delegado su decisión en ti.
                </p>
                <button 
                  class="vote-action-btn" 
                  style="background-color: #ff9500; width: 100%;" 
                  @click="abrirModalDelegado(v, del)"
                >
                  Votar en nombre del Piso {{ del.vivienda.nombre }}
                </button>
              </div>
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
        <p>
          Estás votando en: <strong>{{ votacionSeleccionada?.titulo }}</strong>
        </p>

        <div class="modal-actions">
          <button class="btn-si" @click="enviarVoto('si')">👍 Sí</button>
          <button class="btn-no" @click="enviarVoto('no')">👎 No</button>
        </div>

        <button class="btn-cancelar" @click="mostrarModal = false">Cancelar</button>
      </div>
    </div>

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
            />
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
            <input type="datetime-local" id="fecha_limite" v-model="nuevaVotacion.fecha_limite" />
          </div>

          <div class="modal-actions">
            <button type="submit" class="btn-si">Crear Votación</button>
            <button type="button" class="btn-no" @click="mostrarModalCrear = false">
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="mostrarModalDelegar" class="modal-overlay">
      <div class="modal-content">
        <h3>Delegar Voto</h3>
        <p>
          Selecciona el piso al que deseas delegar tu voto para la propuesta: <br>
          <strong>{{ votacionSeleccionada?.titulo }}</strong>
        </p>

        <div class="form-group" style="margin-top: 20px; text-align: left;">
          <label for="vivienda-delegada">Vecino representante:</label>
          <select 
            id="vivienda-delegada" 
            v-model="viviendaDelegadaId" 
          >
            <option value="" disabled>Selecciona un piso...</option>
            <option v-for="viv in viviendasDisponibles" :key="viv.id" :value="viv.id">
              Piso {{ viv.nombre }}
            </option>
          </select>
        </div>

        <div class="modal-actions">
          <button 
            class="btn-si" 
            @click="confirmarDelegacion" 
            :disabled="!viviendaDelegadaId"
            :style="{ opacity: !viviendaDelegadaId ? 0.5 : 1, cursor: !viviendaDelegadaId ? 'not-allowed' : 'pointer' }"
          >
            🤝 Confirmar
          </button>
          <button class="btn-no" @click="mostrarModalDelegar = false">
            Cancelar
          </button>
        </div>
      </div>
    </div>

    <div v-if="mostrarModalResultados" class="modal-overlay">
      <div class="modal-content resultados-modal">
        <h3>Resultados: {{ tituloVotacionResultados }}</h3>
        
        <div class="tabla-resultados">
          <div class="tabla-header">
            <span>Vivienda</span>
            <span>Voto</span>
          </div>
          <div v-for="voto in resultadosSeleccionados" :key="voto.id" class="tabla-fila">
            <span class="piso-nombre">Piso {{ voto.vivienda.nombre }}</span>
            <span :class="['voto-tag', voto.opcion]">
              {{ voto.opcion === 'si' ? '👍 SÍ' : '👎 NO' }}
            </span>
          </div>
          <div v-if="resultadosSeleccionados.length === 0" class="no-votos">
            Aún no hay votos registrados.
          </div>
        </div>

        <button class="btn-cancelar" @click="mostrarModalResultados = false">Cerrar</button>
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
import { ref, onMounted } from 'vue'
import {
  apiUrl,
  authHeaders
} from '@/lib/api'

// ============================================================================
// 1. VARIABLES GLOBALES Y ESTADO (STATE)
// ============================================================================
const user = JSON.parse(localStorage.getItem('user'))
const textoBusqueda = ref('')

// Listados de datos
const votaciones = ref([])
const votacionesVotadas = ref([])
const delegacionesPendientes = ref([]) 
const viviendasDisponibles = ref([]) 

// --- NUEVAS CONSTANTES PARA RESULTADOS PÚBLICOS ---
const mostrarModalResultados = ref(false)   // Controla el nuevo modal de la matriz
const resultadosSeleccionados = ref([])     // Guarda los votos detallados (Piso + Opción)
const tituloVotacionResultados = ref('')    // Título para la cabecera del modal
// --------------------------------------------------

// Estados de los Modales
const mostrarModal = ref(false)         // Modal para votar (propio o delegado)
const mostrarModalCrear = ref(false)    // Modal para crear nueva votación
const mostrarModalDelegar = ref(false)  // Modal para ceder tu voto a un vecino

// Elementos seleccionados temporalmente
const votacionSeleccionada = ref(null)
const delegacionActiva = ref(null)      // Guarda qué delegación estamos resolviendo
const viviendaDelegadaId = ref('')      // Guarda a qué piso le vamos a ceder el voto
const nuevaVotacion = ref({             // Formulario de nueva votación
  titulo: '',
  descripcion: '',
  fecha_limite: '',
})


// ============================================================================
// 2. FUNCIONES DE LECTURA (FETCH Y HELPERS)
// ============================================================================

const getVotaciones = async () => {
  try {
    const url = `${apiUrl('/api/votaciones')}?buscar=${textoBusqueda.value}`

    const response = await fetch(url, {
      headers: authHeaders(),
    })

    const data = await response.json()

    if (!response.ok) {
      console.error(data)
      return
    }
    
    votaciones.value = data.votaciones || []
    votacionesVotadas.value = data.mis_votos || []
    delegacionesPendientes.value = data.delegaciones_pendientes || []
    
  } catch (error) {
    console.error('Error cargando votaciones:', error)
  }
}

// NUEVA FUNCIÓN PARA LA MATRIZ DE RESULTADOS
const verResultados = (votacion) => {
  tituloVotacionResultados.value = votacion.titulo
  // Filtramos los votos que ya tienen una opción elegida (ignoramos delegaciones vacías)
  resultadosSeleccionados.value = votacion.votos ? votacion.votos.filter(v => v.opcion !== null) : []
  mostrarModalResultados.value = true
}

const isVoted = (id) => votacionesVotadas.value.includes(id)

const estaActiva = (fechaLimite) => {
  if (!fechaLimite) return true
  return new Date() <= new Date(fechaLimite)
}

const calcularPorcentajeSi = (votacion) => {
  if (!votacion.votos_count || votacion.votos_count === 0) return 0
  return Math.round((votacion.votos_si_count / votacion.votos_count) * 100)
}

// ============================================================================
// 3. LÓGICA DE VOTACIÓN (PROPIA Y EN NOMBRE DE OTRO)
// ============================================================================

const abrirModal = (votacion) => {
  votacionSeleccionada.value = votacion
  delegacionActiva.value = null // Nos aseguramos de que es voto propio
  mostrarModal.value = true
}

const abrirModalDelegado = (votacion, delegacion) => {
  votacionSeleccionada.value = votacion
  delegacionActiva.value = delegacion // Guardamos el poder que nos ha dado el vecino
  mostrarModal.value = true
}

const enviarVoto = async (opcion) => {
  try {
    // Verificamos si estamos votando por nosotros o por un vecino
    const esDelegado = delegacionActiva.value !== null
    
    // Si somos delegados, mandamos a la nueva ruta especial de Laravel
    const endpoint = esDelegado ? '/api/votaciones/ejecutar-delegado' : '/api/votaciones/votar'
    
    const bodyData = {
      votacion_id: votacionSeleccionada.value.id,
      opcion: opcion
    }
    
    // Si somos delegados, le mandamos a Laravel el ID de ese registro específico
    if (esDelegado) {
      bodyData.voto_id = delegacionActiva.value.id
    }

    const response = await fetch(apiUrl(endpoint), {
      method: 'POST',
      headers: authHeaders(),
      body: JSON.stringify(bodyData),
    })

    if (response.ok) {
      // 1. Cerramos el modal primero para dar sensación de velocidad
      mostrarModal.value = false
      delegacionActiva.value = null

      // Volvemos a pedir los datos al servidor para actualizar porcentajes y matriz
      await getVotaciones()
      if (esDelegado) {
        // Ocultamos visualmente la delegación que acabamos de resolver
        delegacionesPendientes.value = delegacionesPendientes.value.filter(d => d.id !== delegacionActiva.value.id)
        alert('¡Voto del vecino registrado correctamente!')
      } else {
        // Apuntamos nuestro propio voto para bloquear el botón
        votacionesVotadas.value.push(votacionSeleccionada.value.id)
        alert('¡Tu voto ha sido registrado correctamente!')
      }

      // Limpiamos y cerramos
      delegacionActiva.value = null
      mostrarModal.value = false
      
    } else {
      const errorData = await response.json()
      alert('Error: ' + (errorData.message || 'No se pudo procesar el voto'))
    }
  } catch (error) {
    console.error('Error al enviar el voto:', error)
  }
}

// ============================================================================
// 4. LÓGICA DE CESIÓN DE VOTO (DAR TU VOTO A UN VECINO)
// ============================================================================

const iniciarDelegacion = async (votacion) => {
  votacionSeleccionada.value = votacion
  viviendaDelegadaId.value = '' 
  
  try {
    const response = await fetch(apiUrl('/api/viviendas'), {
      headers: authHeaders() 
    })

    if (response.ok) {
      const data = await response.json()
      
      // Usamos 'data' directamente porque en la rama dev Laravel devuelve el array limpio
      viviendasDisponibles.value = data.filter(v => v.id !== user?.vivienda_id)
      
      mostrarModalDelegar.value = true
    } else {
      alert('Error al cargar la lista de vecinos.')
    }
  } catch (error) {
    console.error('Error cargando viviendas:', error)
  }
}

const confirmarDelegacion = async () => {
  try {
    const response = await fetch(apiUrl('/api/votaciones/delegar'), {
      method: 'POST',
      headers: authHeaders(), // <-- Limpiado también
      body: JSON.stringify({
        votacion_id: votacionSeleccionada.value.id,
        vivienda_delegada_id: viviendaDelegadaId.value
      })
    })

    if (response.ok) {
      mostrarModalDelegar.value = false
      
      // Refrescamos para que el botón de la votación cambie a "Ya has participado"
      await getVotaciones() 
      
      alert('¡Has delegado tu voto correctamente!')
    } else {
      const errorData = await response.json()
      alert('Error: ' + (errorData.message || 'No se pudo procesar la delegación'))
    }
  } catch (error) {
    console.error('Error al delegar:', error)
  }
}


// ============================================================================
// 5. LÓGICA DE CREACIÓN DE VOTACIONES (ADMIN/PRESIDENTE)
// ============================================================================

const abrirModalCrear = () => {
  nuevaVotacion.value = { titulo: '', descripcion: '', fecha_limite: '' }
  mostrarModalCrear.value = true
}

const guardarNuevaVotacion = async () => {
  try {
    // Hacemos la petición POST a tu API de Laravel
    const response = await fetch(apiUrl('/api/votaciones'), {
      method: 'POST',
      headers: authHeaders(),
      // Convertimos nuestro objeto reactivo a formato JSON
      body: JSON.stringify(nuevaVotacion.value),
    })

    if (response.ok) {
      const nuevaVotacionCreada = await response.json()
      votaciones.value.unshift(nuevaVotacionCreada.votacion || nuevaVotacionCreada) // unshift lo pone el primero
      mostrarModalCrear.value = false
      nuevaVotacion.value = { titulo: '', descripcion: '', fecha_limite: '' }
    } else {
      const errorData = await response.json()
      alert('Error: ' + (errorData.message || 'No se pudo crear la votación'))
    }
  } catch (error) {
    console.error('Error de conexión:', error)
    alert('Fallo al conectar con el servidor.')
  }
}

// ============================================================================
// 6. INICIALIZACIÓN
// ============================================================================
onMounted(() => {
  getVotaciones()
})

</script>

<style scoped>
/* ... ... */
.dashboard-container {
  display: flex;
  flex-direction: column;
  width: 100%;
  min-height: 100vh;
  background-color: #f2f2f7;
}
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 5%;
  background: white;
  border-bottom: 2px solid #e5e5e5;
}
.main-container {
  flex: 1;
  padding: 20px 5%;
}
.votacion-card {
  background: white;
  border-radius: 15px;
  padding: 20px;
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
  gap: 15px;
  position: relative;
}

/* CONTENEDOR PARA LOS BOTONES DE ACCIÓN */
.botones-activos {
  display: flex;
  gap: 10px;
  width: 100%;
}

.botones-activos .vote-action-btn,
.botones-activos .delegate-action-btn {
  flex: 1;
}

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

/* BOTÓN DE DELEGAR (SECUNDARIO) */
.delegate-action-btn {
  background: transparent;
  color: #007aff;
  border: 2px solid #007aff;
  padding: 10px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.delegate-action-btn:hover {
  background: #e5f1ff; 
}

/* ESTILO PARA BOTÓN GRIS (CUANDO YA SE VOTÓ) */
.voted-btn,
.vote-action-btn:disabled {
  background-color: #c7c7cc !important; /* Gris iOS */
  color: #8e8e93 !important;
  cursor: not-allowed;
}

/* ESTILOS DEL MODAL */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(3px);
}
.modal-content {
  background: white;
  padding: 30px;
  border-radius: 20px;
  width: 80%;
  max-width: 400px;
  text-align: center;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}
.modal-actions {
  display: flex;
  gap: 10px;
  margin: 20px 0;
}
.btn-si,
.btn-no {
  flex: 1;
  padding: 15px;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
}
.btn-si {
  background: #e1ffdc;
  color: #28a745;
}
.btn-no {
  background: #ffdce0;
  color: #d73a49;
}
.btn-cancelar {
  background: none;
  border: none;
  color: #8e8e93;
  font-size: 14px;
  cursor: pointer;
  margin-top: 10px;
}

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
  top: 15px; 
  right: 15px; 
  background-color: #e0f7fa;
  color: #00796b;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); 
}

.form-group input,
.form-group textarea,
.form-group select {
  padding: 12px;
  border: 1px solid #d1d1d6;
  border-radius: 8px;
  font-size: 15px;
  font-family: inherit;
  background-color: white; 
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #007aff;
  box-shadow: 0 0 0 2px rgba(0, 122, 255, 0.2);
}

.percentage-badge-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background-color: #e0f7fa;
  color: #00796b;
  border: none;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s;
}

.percentage-badge-btn:hover {
  transform: scale(1.05);
  background-color: #b2ebf2;
}

.resultados-modal {
  max-width: 450px;
}

.tabla-resultados {
  margin: 20px 0;
  max-height: 300px;
  overflow-y: auto;
  border: 1px solid #e5e5ea;
  border-radius: 12px;
}

.tabla-header {
  display: flex;
  justify-content: space-between;
  padding: 10px 15px;
  background: #f2f2f7;
  font-weight: bold;
  border-bottom: 1px solid #e5e5ea;
}

.tabla-fila {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 15px;
  border-bottom: 1px solid #f2f2f7;
}

.piso-nombre {
  font-weight: 500;
  color: #333;
}

.voto-tag {
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: bold;
}

.voto-tag.si {
  background: #e1ffdc;
  color: #28a745;
}

.voto-tag.no {
  background: #ffdce0;
  color: #d73a49;
}
</style>