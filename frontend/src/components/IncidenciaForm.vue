<template>
  <form @submit.prevent="crearIncidencia" style="border:1px solid #ccc; padding:15px; margin:15px 0;">
    <h3>Nueva incidencia</h3>

    <input
      v-model.trim="titulo"
      type="text"
      placeholder="Título"
      style="display:block; margin-bottom:10px; width: 100%; max-width: 320px;"
    />

    <textarea
      v-model.trim="descripcion"
      placeholder="Descripción"
      style="display:block; margin-bottom:10px; width: 100%; max-width: 320px; min-height: 120px;"
    ></textarea>

    <button type="submit">
      Crear
    </button>
  </form>
</template>

<script>
export default {
  name: "IncidenciaForm",
  emits: ["crear-incidencia"],
  data() {
    return {
      titulo: "",
      descripcion: ""
    }
  },
  methods: {
    crearIncidencia() {
      if (!this.titulo) {
        alert("El título es obligatorio")
        return
      }

      const payload = {
        titulo: this.titulo,
        descripcion: this.descripcion
      }

      console.log("EMITIENDO:", payload)
      this.$emit("crear-incidencia", payload)

      this.titulo = ""
      this.descripcion = ""
    }
  }
}
</script>