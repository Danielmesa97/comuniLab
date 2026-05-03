<<template>
  <div class="page-container">

    <!-- HEADER -->
    <div class="header">
      <h2>📢 Tablero de anuncios</h2>

      <!-- 🔍 FILTRO ADMIN -->
      <div v-if="isAdmin" class="filters">
        <input type="date" v-model="filtroDesde">
        <input type="date" v-model="filtroHasta">
        <button @click="getAnuncios">Filtrar</button>
      </div>

      <!-- ➕ CREAR -->
      <button 
        v-if="isAdmin"
        class="create-btn"
        @click="mostrarModal = true"
      >
        ➕ Crear anuncio
      </button>
    </div>

    <!-- LISTADO -->
    <div class="main-container">

      <div v-if="anuncios.length === 0" class="empty">
        <p>No hay anuncios disponibles</p>
      </div>

      <div 
  v-for="a in anunciosOrdenados" 
  :key="a.id" 
  class="anuncio-card"
  :class="a.tipo"
>

  <!-- ICONO GRANDE -->
  <div class="icono">
    {{ getIcono(a.tipo) }}
  </div>

  <div class="contenido">
    <h3>{{ a.titulo }}</h3>

    <p class="descripcion">{{ a.descripcion }}</p>

    <div class="footer">
      <span class="tipo">{{ formatTipo(a.tipo) }}</span>
      <span class="fechas">
        {{ a.fecha_inicio }} → {{ a.fecha_fin }}
      </span>
    </div>
  </div>
 </div>
</div>

    <!-- MODAL CREAR -->
    <div v-if="mostrarModal" class="modal-overlay">
      <div class="modal">

        <h3>Nuevo anuncio</h3>

        <input v-model="form.titulo" placeholder="Título">

        <textarea v-model="form.descripcion" placeholder="Descripción"></textarea>

        <select v-model="form.tipo">
          <option value="noticia">Noticia</option>
          <option value="evento">Evento</option>
          <option value="aviso">Aviso</option>
          <option value="documento">Documento</option>
        </select>

        <input type="date" v-model="form.fecha_inicio">
        <input type="date" v-model="form.fecha_fin">

        <div class="modal-actions">
          <button @click="crearAnuncio">Guardar</button>
          <button @click="mostrarModal = false">Cancelar</button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const anuncios = ref([])
const mostrarModal = ref(false)

const filtroDesde = ref('')
const filtroHasta = ref('')

// USER
const user = JSON.parse(localStorage.getItem('user') || '{}')

const isAdmin = computed(() =>
  ['admin','presidente','superadmin'].includes(user.role)
)

// FORM
const form = ref({
  titulo: '',
  descripcion: '',
  tipo: 'noticia',
  fecha_inicio: '',
  fecha_fin: ''
})

/* ======================================
   🔥 NUEVO: ICONOS POR TIPO
====================================== */
const getIcono = (tipo) => {
  const map = {
    noticia: "📰",
    evento: "📅",
    aviso: "⚠️",
    documento: "📄"
  }
  return map[tipo] || "📌"
}

/* ======================================
   🔥 NUEVO: ORDEN POR PRIORIDAD
====================================== */
const anunciosOrdenados = computed(() => {
  const prioridad = {
    aviso: 1,
    evento: 2,
    noticia: 3,
    documento: 4
  }

  return [...anuncios.value].sort((a, b) => {
    return prioridad[a.tipo] - prioridad[b.tipo]
  })
})

/* ======================================
   FETCH
====================================== */
const getAnuncios = async () => {
  try {
    const token = localStorage.getItem('auth_token')

    let url = 'http://127.0.0.1:8000/api/anuncios'

    if (filtroDesde.value && filtroHasta.value) {
      url += `?desde=${filtroDesde.value}&hasta=${filtroHasta.value}`
    }

    const res = await fetch(url, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    const data = await res.json()
    anuncios.value = data

  } catch (err) {
    console.error(err)
  }
}

/* ======================================
   CREAR
====================================== */
const crearAnuncio = async () => {
  try {
    const token = localStorage.getItem('auth_token')

    const res = await fetch('http://127.0.0.1:8000/api/anuncios', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(form.value)
    })

    if (!res.ok) {
      alert("Error creando anuncio")
      return
    }

    mostrarModal.value = false
    resetForm()
    getAnuncios()

  } catch (err) {
    console.error(err)
  }
}

/* ======================================
   RESET FORM
====================================== */
const resetForm = () => {
  form.value = {
    titulo: '',
    descripcion: '',
    tipo: 'noticia',
    fecha_inicio: '',
    fecha_fin: ''
  }
}

/* ======================================
   FORMATO TEXTO
====================================== */
const formatTipo = (tipo) => {
  const map = {
    noticia: "📰 Noticias",
    evento: "📅 Evento",
    aviso: "⚠️ Aviso",
    documento: "📄 Documento"
  }
  return map[tipo] || tipo
}

onMounted(getAnuncios)
</script>
<style scoped>

.page-container {
  padding: 20px;
  padding-bottom: 80px;
  background: #f2f2f7;
  min-height: 100vh;
}

/* HEADER */
.header {
  margin-bottom: 20px;
}

.header h2 {
  margin-bottom: 10px;
}

/* BOTÓN CREAR */
.create-btn {
  margin-top: 10px;
  background: #080a13;
  color: white;
  padding: 12px;
  border-radius: 12px;
  border: none;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s ease;
}

.create-btn:hover {
  background: #1c1f2b;
  transform: translateY(-1px);
}

/* FILTROS */
.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
}

.filters input {
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ddd;
  background: white;
}

.filters button {
  background: #007aff;
  color: white;
  border: none;
  padding: 10px 14px;
  border-radius: 10px;
  cursor: pointer;
}

/* TARJETAS */
.anuncio-card {
  background: white;
  border-radius: 20px;
  padding: 20px;
  margin-bottom: 15px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.08);
  transition: all 0.2s ease;
  position: relative;
}

.anuncio-card:hover {
  transform: translateY(-4px);
}

/* TIPOS CON COLOR + FONDO SUAVE */
.anuncio-card.noticia {
  border-left: 6px solid #3b82f6;
  background: linear-gradient(to right, #eff6ff, white);
}

.anuncio-card.evento {
  border-left: 6px solid #22c55e;
  background: linear-gradient(to right, #ecfdf5, white);
}

.anuncio-card.aviso {
  border-left: 6px solid #f59e0b;
  background: linear-gradient(to right, #fffbeb, white);
}

.anuncio-card.documento {
  border-left: 6px solid #8b5cf6;
  background: linear-gradient(to right, #f5f3ff, white);
}

/* TÍTULO */
.anuncio-card h3 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 8px;
}

/* DESCRIPCIÓN */
.descripcion {
  margin: 10px 0;
  color: #555;
}

/* FOOTER */
.footer {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  margin-top: 10px;
}

.tipo {
  font-weight: bold;
}

.fechas {
  color: #888;
}

/* EMPTY STATE */
.empty {
  text-align: center;
  margin-top: 40px;
  color: #888;
}

/* MODAL */
.modal-overlay {
  position: fixed;
  top:0; left:0;
  width:100%; height:100%;
  background: rgba(0,0,0,0.4);
  display:flex;
  justify-content:center;
  align-items:center;
  z-index: 2000;
}

.modal {
  background:white;
  padding:25px;
  border-radius:20px;
  width:90%;
  max-width:400px;
  display:flex;
  flex-direction:column;
  gap:12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

/* INPUTS MODAL */
.modal input,
.modal textarea,
.modal select {
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #ddd;
  background: #fafafa;
}

.modal textarea {
  resize: none;
}

/* BOTONES MODAL */
.modal-actions {
  display:flex;
  gap:10px;
}

.modal-actions button {
  flex:1;
  padding:12px;
  border:none;
  border-radius:10px;
  cursor:pointer;
  font-weight:bold;
}

.modal-actions button:first-child {
  background:#080a13;
  color:white;
}

.modal-actions button:last-child {
  background:#eee;
}

</style>