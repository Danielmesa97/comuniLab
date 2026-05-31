const API_BASE_URL = (
  import.meta.env.VITE_API_URL ||
  'http://127.0.0.1:8000'
).replace(/\/$/, '')

export const apiUrl = (path = '') => {

  if (!path) {
    return API_BASE_URL
  }

  return `${API_BASE_URL}${
    path.startsWith('/')
      ? path
      : `/${path}`
  }`

}

/* HEADERS AUTH */

export const authHeaders = () => {

  const token =
    localStorage.getItem('auth_token')

  const comunidadId =
    localStorage.getItem(
      'superadmin_comunidad_id'
    )

  return {

    Authorization: `Bearer ${token}`,

    'Content-Type': 'application/json',

    Accept: 'application/json',

    ...(comunidadId && {
      'X-Comunidad-Id': comunidadId
    })

  }

}