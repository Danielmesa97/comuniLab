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

      fetch('http://127.0.0.1:8000/api/incidencias', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          titulo: this.titulo,
          descripcion: this.descripcion,
          user_id:1 //para las pruebas que quiero hacer !!!!!!!
        })
      })
      .then(async res => {
        const data = await res.json()
        console.log("STATUS:", res.status)
        console.log("RESPUESTA:", data)

        if (!res.ok) {
          throw new Error("Error en backend")
        }

        return data
      })
      .then(data => {
        this.$emit("crear-incidencia", data.data)

        this.titulo = ""
        this.descripcion = ""
      })
      .catch(err => {
        console.error("ERROR:", err)
      })
    }
  }
}
</script>