<template>
  <form
    @submit.prevent="crearIncidencia"
    style="border: 1px solid #ccc; padding: 15px; margin: 15px 0"
  >
    <h3>Nueva incidencia</h3>

    <input
      v-model.trim="titulo"
      type="text"
      placeholder="Título"
      style="display: block; margin-bottom: 10px; width: 100%; max-width: 320px"
    />

    <textarea
      v-model.trim="descripcion"
      placeholder="Descripción"
      style="display: block; margin-bottom: 10px; width: 100%; max-width: 320px; min-height: 120px"
    ></textarea>

    <button type="submit">Crear</button>
  </form>
</template>

<script>
import { apiUrl } from '@/lib/api'

export default {
  name: 'IncidenciaForm',
  emits: ['crear-incidencia'],

  data() {
    return {
      titulo: '',
      descripcion: '',
    }
  },

  methods: {
    async crearIncidencia() {
      if (!this.titulo) {
        alert('El título es obligatorio')
        return
      }

      try {
        const res = await fetch(apiUrl('/api/incidencias'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
          },
          body: JSON.stringify({
            titulo: this.titulo,
            descripcion: this.descripcion,
          }),
        })

        const data = await res.json()

        if (!res.ok) {
          console.error(data)
          alert('Error creando incidencia')
          return
        }

        this.$emit('crear-incidencia', data)

        this.titulo = ''
        this.descripcion = ''
      } catch (err) {
        console.error('ERROR:', err)
      }
    },
  },
}
</script>
