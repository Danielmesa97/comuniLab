<template>
  <div class="instalaciones-container">
    <header class="vista-header">
      <h1>Reserva de Instalaciones</h1>
      <p>Selecciona un espacio comunitario para gestionar tu reserva</p>
    </header>

    <div v-if="isAdmin && user.comunidades?.length > 1" class="selector-comunidad">
  <label>Comunidad activa</label>

  <select v-model="comunidadActivaId" @change="cambiarComunidadActiva">
    <option
      v-for="c in user.comunidades"
      :key="c.id"
      :value="c.id"
    >
      {{ c.nombre }}
    </option>
  </select>
</div>

    <main class="grid-instalaciones">
      <div v-for="inst in instalaciones" :key="inst.id" class="card-instalacion">
        <div class="card-imagen">{{ inst.icono }}</div>
        
        <div class="card-contenido">
          <h2>{{ inst.nombre }}</h2>
          <p class="descripcion">{{ inst.descripcion }}</p>
          
          <div class="info-adicional">
            <span>⏱️ Franjas: <strong>{{ inst.duracion_franja }} min</strong></span>
            <span>👥 Aforo máx: <strong>{{ inst.aforo_max }}</strong></span>
          </div>

          <button class="btn-reservar" @click="abrirModalReserva(inst)">
            Reservar espacio
          </button>

          <button
            v-if="isAdmin"
            class="btn-eliminar"
            @click="eliminarInstalacion(inst.id)"
          >
            Eliminar
          </button>
        </div>
      </div>
      <div
  v-if="isAdmin"
  class="create-card"
>

  <input
    v-model="nuevaInstalacion.nombre"
    placeholder="Nombre instalación"
  >

  <input
    v-model="nuevaInstalacion.descripcion"
    placeholder="Descripción"
  >

  <input
    v-model="nuevaInstalacion.icono"
    placeholder="Icono"
  >

  <input
    v-model="nuevaInstalacion.aforo_max"
    type="number"
    placeholder="Aforo"
  >

  <button
    class="btn-crear"
    @click="crearInstalacion"
  >
    Crear instalación
  </button>

</div>
      <div v-if="instalaciones.length === 0" class="no-data">
        <p>No hay instalaciones disponibles en tu comunidad actualmente.</p>
      </div>
    </main>

    <div v-if="mostrarModal" class="modal-overlay" @click.self="cerrarModal">
      <div class="modal-content modal-calendario-ancho">
        <button class="close-btn" @click="cerrarModal">✕</button>
        
        <h3>Calendario de Reservas: {{ instalacionSeleccionada?.nombre }}</h3>
        <p class="modal-subtitle">Franjas de {{ instalacionSeleccionada?.duracion_franja }} min. Haz clic en una celda libre para reservar.</p>

        <div class="navegacion-semanas">
          <button @click="retrocederSemana" :disabled="semanaOffset === 0" class="btn-nav-semana">
            ⬅️ Anterior
          </button>
          <span class="titulo-semana">
            {{ semanaOffset === 0 ? 'Semana Actual' : 'Próxima Semana' }}
          </span>
          <button @click="avanzarSemana" :disabled="semanaOffset === 1" class="btn-nav-semana">
            Siguiente ➡️
          </button>
        </div>

        <div class="calendario-semanal">
          <div class="celda-header hora-col">Horario</div>
          
          <div v-for="dia in diasSemana" :key="dia.fecha" class="celda-header">
            <span class="dia-nombre">{{ dia.nombre }}</span>
            <span class="dia-fecha">{{ dia.fechaCorto }}</span>
          </div>

          <template v-for="franja in franjasHorarias" :key="franja.id">
            <div class="celda-hora hora-col">
              {{ franja.inicio }} - {{ franja.fin }}
            </div>
            
            <div 
              v-for="dia in diasSemana" 
              :key="dia.fecha + '-' + franja.id"
              :class="['celda-reserva', obtenerEstadoCelda(dia.fecha, franja.id)]"
              @click="seleccionarCelda(dia.fecha, franja)"
            >
              <span class="estado-texto">
                {{ obtenerEstadoCelda(dia.fecha, franja.id) === 'ocupado' ? '🚫 Reservado' : '🟢 Libre' }}
              </span>
            </div>
          </template>
        </div>

        <div v-if="reservaTemporal" class="confirmacion-reserva-caja">
          <p>Vas a reservar el día <strong>{{ ordenarFecha(reservaTemporal.fecha) }}</strong> de <strong>{{ reservaTemporal.franja.inicio }} a {{ reservaTemporal.franja.fin }}</strong>.</p>
          <div class="modal-actions">
            <button class="btn-cancelar" @click="reservaTemporal = null">Cambiar</button>
            <button class="btn-confirmar" @click="confirmarReserva">Confirmar Reserva</button>
          </div>
        </div>
        
        <div v-else class="modal-footer-info">
          <button class="btn-cancelar full-width" @click="cerrarModal">Cerrar Calendario</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { apiUrl } from '@/lib/api'

const user = JSON.parse(
  localStorage.getItem('user') || '{}'
)

const isAdmin =
  user.role === 'admin'
  ||
  user.role === 'presidente'

const comunidadActivaId = ref(
  localStorage.getItem('selected_comunidad_id') ||
  user.comunidades?.[0]?.id ||
  ''
)

const nuevaInstalacion = ref({
  nombre: '',
  descripcion: '',
  duracion_franja: 90,
  aforo_max: 10,
  icono: '🏟️'
})

const token = localStorage.getItem('auth_token')
const cambiarComunidadActiva = () => {
  localStorage.setItem(
    'selected_comunidad_id',
    comunidadActivaId.value
  )

  getInstalaciones()
}
// Estados reactivos (Ahora empiezan vacíos para llenarse con el backend)
const instalaciones = ref([])
const reservasExistentes = ref([])
const mostrarModal = ref(false)
const instalacionSeleccionada = ref(null)
const reservaTemporal = ref(null)

const fechaMinima = ref(new Date().toISOString().split('T')[0])

// Las franjas se mantienen fijas en el front porque es vuestro horario comercial
const franjasHorarias = ref([
  { id: 1, inicio: '09:00', fin: '10:30' },
  { id: 2, inicio: '10:30', fin: '12:00' },
  { id: 3, inicio: '12:00', fin: '13:30' },
  { id: 4, inicio: '17:00', fin: '18:30' },
  { id: 5, inicio: '18:30', fin: '20:00' },
  { id: 6, inicio: '20:00', fin: '21:30' }
])

const semanaOffset = ref(0)
const avanzarSemana = () => { if (semanaOffset.value < 1) semanaOffset.value = 1 }
const retrocederSemana = () => { if (semanaOffset.value > 0) semanaOffset.value = 0 }

const diasSemana = computed(() => {
  const nombresDias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb']
  const meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic']
  const lista = []
  
  const diasDesfase = semanaOffset.value * 7;
  
  for (let i = 0; i < 7; i++) {
    const d = new Date()
    d.setDate(d.getDate() + i + diasDesfase)
    
    lista.push({
      nombre: nombresDias[d.getDay()],
      fechaCorto: `${d.getDate()} ${meses[d.getMonth()]}`,
      fecha: d.toISOString().split('T')[0]
    })
  }
  return lista
})

// 1. CARGAR INSTALACIONES REALES
const getInstalaciones = async () => {
  try {
    const res = await fetch(apiUrl('/api/instalaciones'), {
      headers: {
        Authorization: `Bearer ${token}`,
        'X-Comunidad-Id': comunidadActivaId.value
      }
    })
    
    if (res.ok) {
      instalaciones.value = await res.json()
    } else {
      console.error('Error al obtener instalaciones de la base de datos')
    }
  } catch (error) {
    console.error('Error de red cargando instalaciones:', error)
  }
}

/*
|--------------------------------------------------------------------------
| CREAR INSTALACIÓN
|--------------------------------------------------------------------------
*/

const crearInstalacion = async () => {

  try {

    const res = await fetch(
      apiUrl('/api/instalaciones'),
      {
        method: 'POST',

        headers: {
          'Content-Type': 'application/json',

          Authorization: `Bearer ${token}`,

          'X-Comunidad-Id':
            comunidadActivaId.value
        },

        body: JSON.stringify(
          nuevaInstalacion.value
        )
      }
    )

    if (res.ok) {

      nuevaInstalacion.value = {
        nombre: '',
        descripcion: '',
        duracion_franja: 90,
        aforo_max: 10,
        icono: '🏟️'
      }

      getInstalaciones()

      alert('Instalación creada')

    }

  } catch (error) {

    console.error(error)

  }

}

/*
|--------------------------------------------------------------------------
| ELIMINAR
|--------------------------------------------------------------------------
*/

const eliminarInstalacion = async (id) => {

  if (!confirm('¿Eliminar instalación?'))
    return

  try {

    await fetch(
      apiUrl(`/api/instalaciones/${id}`),
      {
        method: 'DELETE',

        headers: {
          Authorization: `Bearer ${token}`,
          'X-Comunidad-Id': comunidadActivaId.value
        }
      }
    )

    getInstalaciones()

  } catch (error) {

    console.error(error)

  }

}


const obtenerEstadoCelda = (fecha, franjaId) => {
  const ocupado = reservasExistentes.value.some(r => r.fecha === fecha && r.franja_id === franjaId)
  return ocupado ? 'ocupado' : 'libre'
}

const seleccionarCelda = (fecha, franja) => {
  if (obtenerEstadoCelda(fecha, franja.id) === 'ocupado') {
    alert('Esta franja ya está reservada por otro propietario.')
    return
  }
  reservaTemporal.value = { fecha, franja }
}

// 2. ABRIR MODAL Y CARGAR RESERVAS REALES DE ESA PISTA
const abrirModalReserva = async (instalacion) => {
  instalacionSeleccionada.value = instalacion
  reservaTemporal.value = null
  semanaOffset.value = 0
  mostrarModal.value = true

  // Llamada al backend para traer las reservas de la instalación seleccionada
  try {
    const res = await fetch(apiUrl(`/api/instalaciones/${instalacion.id}/reservas`), {
      headers: { Authorization: `Bearer ${token}` }
    })
    if (res.ok) {
      reservasExistentes.value = await res.json()
    }
  } catch (error) {
    console.error('Error al cargar las reservas:', error)
  }
}

const cerrarModal = () => {
  mostrarModal.value = false
  instalacionSeleccionada.value = null
  reservaTemporal.value = null
  reservasExistentes.value = [] // Limpiamos la memoria
}

// 3. GUARDAR RESERVA REAL EN LA BASE DE DATOS
const confirmarReserva = async () => {
  if (!reservaTemporal.value) return

  try {
    const res = await fetch(apiUrl('/api/reservas'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`
      },
      body: JSON.stringify({
        instalacion_id: instalacionSeleccionada.value.id,
        fecha: reservaTemporal.value.fecha,
        franja_id: reservaTemporal.value.franja.id
      })
    })

    if (res.ok) {
      alert(`¡Reserva realizada con éxito!\nInstalación: ${instalacionSeleccionada.value.nombre}\nFecha: ${ordenarFecha(reservaTemporal.value.fecha)}\nHora: ${reservaTemporal.value.franja.inicio} - ${reservaTemporal.value.franja.fin}`)
      
      // Lo añadimos localmente para que se pinte en rojo al instante sin recargar la página
      reservasExistentes.value.push({
        fecha: reservaTemporal.value.fecha,
        franja_id: reservaTemporal.value.franja.id
      })
      
      cerrarModal()
    } else {
      const errorData = await res.json()
      alert(errorData.message || 'Error al confirmar la reserva. Puede que ya esté ocupada.')
    }
  } catch (error) {
    console.error('Error conectando con el servidor:', error)
    alert('Error de conexión al procesar la reserva.')
  }
}

const ordenarFecha = (f) => f.split('-').reverse().join('/')

onMounted(() => {
  getInstalaciones()
})
</script>

<style scoped>
.instalaciones-container {
  width: 100%;
  min-height: 100vh;
  background-color: #f2f2f7;
  padding: 30px 5% 80px 5%; /* Padding abajo para que la navbar inferior no tape nada */
}

.vista-header {
  margin-bottom: 30px;
}

.vista-header h1 {
  font-size: 28px;
  margin: 0;
  color: #1c1c1e;
}

.vista-header p {
  font-size: 14px;
  color: #8e8e93;
  margin-top: 5px;
}

/* GRID RESPONSIVO PARA LAS CARDS */
.grid-instalaciones {
  display: grid;
  gap: 20px;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
}

/* CARDS ESTILO IOS */
.card-instalacion {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s ease;
}

.card-instalacion:hover {
  transform: translateY(-3px);
}

.card-imagen {
  background: #e5e5ea;
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 50px;
}

.card-contenido {
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.card-contenido h2 {
  font-size: 20px;
  margin: 0 0 10px 0;
  color: #1c1c1e;
}

.descripcion {
  font-size: 13px;
  color: #666;
  line-height: 1.4;
  margin: 0 0 15px 0;
  flex: 1; /* Empuja la info hacia abajo para alinear los botones */
}

.info-adicional {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #8e8e93;
  background: #f2f2f7;
  padding: 10px;
  border-radius: 10px;
  margin-bottom: 15px;
}

.btn-reservar {
  background-color: #007aff;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-reservar:hover {
  background-color: #0063cc;
}

/* MODALES STYLING */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 24px;
  width: 90%;
  max-width: 450px;
  position: relative;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: #f2f2f7;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 14px;
  color: #8e8e93;
}

.modal-subtitle {
  font-size: 13px;
  color: #8e8e93;
  margin: -5px 0 20px 0;
}

.form-reserva {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group label {
  font-size: 13px;
  font-weight: 600;
  color: #1c1c1e;
}

.form-group input, .form-group select {
  padding: 10px;
  border: 1px solid #c7c7cc;
  border-radius: 10px;
  font-size: 14px;
  background: #fff;
}

.modal-actions {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.btn-cancelar {
  flex: 1;
  background: #f2f2f7;
  color: #007aff;
  border: none;
  padding: 12px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
}

.btn-confirmar {
  flex: 1;
  background: #007aff;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
}

.btn-confirmar:hover {
  background-color: #0063cc;
}

.no-data {
  grid-column: 1 / -1;
  text-align: center;
  padding: 40px;
  color: #8e8e93;
}

/* ADAPTACIÓN ESCRITORIO */
@media (min-width: 1024px) {
  .instalaciones-container {
    padding-left: 10%;
    padding-right: 10%;
  }
}

/* Amplitud para alojar las columnas del calendario cómodamente */
.modal-content.modal-calendario-ancho {
  max-width: 750px;
  width: 95%;
}

/* NAVEGACIÓN DE SEMANAS (NUEVO) */
.navegacion-semanas {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 15px 0 10px 0;
  padding: 10px;
  background: #f2f2f7;
  border-radius: 12px;
}

.titulo-semana {
  font-weight: 600;
  font-size: 14px;
  color: #1c1c1e;
}

.btn-nav-semana {
  background: white;
  border: 1px solid #e5e5ea;
  padding: 6px 12px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  color: #007aff;
  transition: all 0.2s;
}

.btn-nav-semana:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  color: #8e8e93;
}

/* Rejilla de la Matriz: 1 columna para las horas + 5 columnas para los días */
/* Rejilla de la Matriz: 1 columna para las horas + 7 columnas para los días */
.calendario-semanal {
  display: grid;
  /* CAMBIA EL 5 POR UN 7 AQUÍ: */
  grid-template-columns: 100px repeat(7, 1fr);
  gap: 8px;
  margin: 10px 0 20px 0;
  overflow-x: auto;   
  overflow-y: hidden; 
  padding: 5px;       
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.calendario-semanal::-webkit-scrollbar {
  display: none;
}

/* Celdas Cabecera (Días y etiqueta horario) */
.celda-header {
  background: #e5e5ea;
  padding: 10px;
  text-align: center;
  border-radius: 8px;
  font-weight: bold;
  display: flex;
  flex-direction: column;
  justify-content: center;
  font-size: 13px;
}

.dia-fecha {
  font-size: 11px;
  color: #666;
  font-weight: normal;
}

/* Columna de las horas (Izquierda) */
.celda-hora {
  background: #f2f2f7;
  padding: 12px 6px;
  font-size: 11px;
  font-weight: 600;
  text-align: center;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #1c1c1e;
  border: 1px solid #e5e5ea;
}

/* Celdas interactivas de las reservas */
.celda-reserva {
  padding: 15px 5px;
  text-align: center;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  border: 1px solid transparent;
}

/* 🟥 Estado Ocupado: Rojo suave (Estilo Pastel iOS) */
.celda-reserva.ocupado {
  background-color: #ffdce0;
  color: #d73a49;
  cursor: not-allowed;
}

/* 🟩 Estado Libre: Gris claro / Verde sutil */
.celda-reserva.libre {
  background-color: #f5f5f7;
  color: #007aff;
  border: 1px dashed #c7c7cc;
}

.celda-reserva.libre:hover {
  background-color: #e1ffdc;
  color: #28a745;
  border-style: solid;
  border-color: #28a745;
  /* 🌟 SOLUCIÓN: Cambiamos scale() por box-shadow para que no empuje el grid */
  box-shadow: inset 0 0 0 1px #28a745;
}

.estado-texto {
  font-size: 12px;
  font-weight: 600;
}

/* Caja inferior de confirmación */
.confirmacion-reserva-caja {
  margin-top: 15px;
  padding: 15px;
  background: #e8f4ff;
  border-radius: 14px;
  border: 1px solid #b3d7ff;
  text-align: center;
  font-size: 14px;
}

.modal-footer-info {
  margin-top: 10px;
}

.full-width {
  width: 100%;
}

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

.create-card{
  background:white;
  padding:20px;
  border-radius:20px;
  display:flex;
  gap:10px;
  margin-bottom:20px;
  box-shadow:0 4px 10px rgba(0,0,0,0.05);
  flex-wrap:wrap;
}

.create-card input{
  padding:10px;
  border-radius:10px;
  border:1px solid #ddd;
  flex:1;
}

.btn-crear{
  background:#34c759;
  color:white;
  border:none;
  padding:10px 18px;
  border-radius:10px;
  cursor:pointer;
  font-weight:600;
}

.btn-eliminar{
  margin-top:10px;
  background:#ff3b30;
  color:white;
  border:none;
  padding:10px;
  border-radius:10px;
  cursor:pointer;
  font-weight:600;
}
.selector-comunidad{
  background:white;
  padding:16px;
  border-radius:16px;
  margin-bottom:20px;
  display:flex;
  gap:12px;
  align-items:center;
  box-shadow:0 4px 10px rgba(0,0,0,0.05);
}

.selector-comunidad label{
  font-weight:700;
}

.selector-comunidad select{
  padding:10px;
  border-radius:10px;
  border:1px solid #ddd;
}

</style>