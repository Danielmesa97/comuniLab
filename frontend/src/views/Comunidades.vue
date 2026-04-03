<template>
  <div style="padding:20px;">
    <h2>Comunidades</h2>

    <!-- FORMULARIO -->
    <input v-model="nombre" placeholder="Nombre" />
    <input v-model="descripcion" placeholder="Descripción" />

    <button @click="crearComunidad">
      Crear comunidad
    </button>

    <hr />

    <!-- LISTA -->
    <ul>
      <li v-for="c in comunidades" :key="c.id">
        <strong>{{ c.nombre }}</strong> - {{ c.descripcion }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      nombre: "",
      descripcion: "",
      comunidades: []
    }
  },

  methods: {
    async cargarComunidades() {
      const res = await fetch("http://127.0.0.1:8000/comunidades")
      this.comunidades = await res.json()
    },

    async crearComunidad() {
      await fetch("http://127.0.0.1:8000/comunidades", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          nombre: this.nombre,
          descripcion: this.descripcion
        })
      })

      this.nombre = ""
      this.descripcion = ""

      this.cargarComunidades()
    }
  },

  mounted() {
    this.cargarComunidades()
  }
}
</script>