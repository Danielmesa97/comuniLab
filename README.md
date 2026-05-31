# ComuniLab Docker

## Arranque rápido

```sh
docker compose up --build
```

## Servicios

- Frontend: http://localhost:5173
- Backend: http://localhost:8000
- phpMyAdmin: http://localhost:8080
- MySQL: localhost:3306

## Qué hace el arranque

- Levanta MySQL y crea la base `habitapp`.
- Espera a que la base esté lista.
- Instala dependencias si hace falta.
- Genera `APP_KEY` si todavía no existe.
- Ejecuta las migraciones del backend.
- Arranca Vue y Laravel en modo desarrollo.
