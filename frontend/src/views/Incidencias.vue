<template>
  <div style="padding:20px;">
    <h2>Incidencias</h2>

    <button @click="mostrarForm = !mostrarForm">
      {{ mostrarForm ? "Cerrar formulario" : "Nueva incidencia" }}
    </button>

    <IncidenciaForm
      v-if="mostrarForm"
      @crear-incidencia="agregarIncidencia"
    />

    <p v-if="incidencias.length === 0">
      No hay incidencias todavía
    </p>

    <ul v-else>
      <li v-for="i in incidencias" :key="i.id" style="margin-bottom: 12px;">
        <strong>{{ i.titulo }}</strong><br>
        <span>{{ i.descripcion }}</span>
      </li>
    </ul>
  </div>
</template>

<script>
import IncidenciaForm from "../components/IncidenciaForm.vue"

export default {
  name: "Incidencias",
  components: {
    IncidenciaForm
  },
  data() {
    return {
      mostrarForm: false,
      incidencias: []
    }
  },
  methods: {
    agregarIncidencia(nueva) {
      console.log("RECIBIDO EN PADRE:", nueva)

      this.incidencias.push({
        id: Date.now(),
        ...nueva
      })

      this.mostrarForm = false
      console.log("ARRAY ACTUAL:", this.incidencias)
    }
  }
}
</script>