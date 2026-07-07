# SIVAR — Correcciones de seguridad aplicadas

Rama: `fix/security-audit` (no toca `main`). Base del análisis: informe
`Auditoria_SIVAR_Seguridad_Arquitectura.pdf`.

## Qué se aplicó (por commit)

1. **`fix(node)` — biojava-runner.js endurecido** · C-1, C-2, C-3, C-4, M-1, M-7, B-5
   - Autenticación por **JWT HS256** reutilizando el `JWT_SECRET` de Laravel.
   - CORS con **allow-list** de orígenes; el servicio escucha solo en `127.0.0.1`.
   - Se elimina `/list-directory`; nuevo `/list-inputs` **confinado a `DATA_ROOT`**.
   - Entradas validadas (dentro de `DATA_ROOT` + extensiones permitidas); las
     **salidas las decide el servidor** en `OUTPUT_ROOT/<jobId>`.
   - Ring buffer de logs, límite de concurrencia, timeout de proceso, limpieza de jobs.
   - Manejador de errores central (no filtra internals); `body` limitado a 32 kB.
   - SSE autorizado con **`streamToken` de un solo uso** (evita JWT en la URL).

2. **`fix(frontend)` — wiring del micro-servicio** · C-1..C-4 (lado cliente)
   - `ServerFilePicker` usa `/list-inputs` con `Authorization: Bearer` y rutas relativas.
   - `CompGenView` envía el Bearer al lanzar el job y usa `streamToken` en el SSE;
     lee `resultUrl` del evento `success`. Los presets pasan a **rutas relativas**.

3. **`fix(laravel)`** · C-5, M-4, M-5
   - `AppServiceProvider` **lanza excepción si `APP_DEBUG=true` en producción**.
   - `config/cors.php` restringe orígenes vía `CORS_ALLOWED_ORIGINS` / `FRONTEND_URL`.
   - Se eliminan los `file_put_contents(... debug_*.log ...)` de `EnsayoController`
     (se usa `Log::error` para el caso de excepción).

4. **`fix(frontend)`** · M-2, M-3, B-1
   - El store **ya no persiste el JWT/refresh** en localStorage (solo `userInfo`).
   - Descargas de adjuntos y export a Excel via **blob autenticado por cabecera**
     (`api.getForDownload`); se elimina `?token=` de las URLs.
   - Se elimina la config CSRF estilo Django (`xsrfCookieName`) en `api.ts`.

## Configuración requerida antes de ejecutar (IMPORTANTE)

**Micro-servicio Node** (`sivar/.env`, ver `sivar/.env.example`):
- `BIOJAVA_JWT_SECRET` = **mismo valor** que `JWT_SECRET` de `api_sivar/.env`.
- `FRONTEND_ORIGIN` = orígenes del frontend (coma-separados).
- `BIOJAVA_DATA_ROOT` = raíz de datos genómicos (p.ej. `/biodata5/proyectos/genomica_comparativa`).
- `BIOJAVA_HOST=127.0.0.1` (exponer por Nginx si hace falta acceso externo).

**Laravel** (`api_sivar/.env`):
- `CORS_ALLOWED_ORIGINS` = orígenes permitidos.
- En producción: `APP_ENV=production` y `APP_DEBUG=false`.

## Cambios de comportamiento a probar

- **Sesión:** al recargar la página el usuario **deberá volver a iniciar sesión**
  (el token ya no se guarda en localStorage). Para "recordar sesión" de forma segura
  falta emitir el *refresh* en cookie **HttpOnly** desde Laravel (pendiente, ver abajo).
- **Comp-Gen:** el visor de resultados ahora se sirve en `/public/results/<jobId>/`.
- **Selector de archivos:** solo lista dentro de `DATA_ROOT` (rutas relativas).
- **Descargas/Export:** ahora van por descarga autenticada (blob), no por enlace directo.

## Segunda ronda (aplicado)

5. **`fix(m-6)` — `save_weight` GET→POST** (ruta + `saveWeight` en el frontend).
   Era la **única** ruta mutante-por-GET con un llamador real en el frontend.

## Nota sobre CSRF y verbos GET (contexto de riesgo real)

La API autentica con **JWT en la cabecera `Authorization`**, no con cookies. Por eso
el **CSRF clásico no es explotable**: una petición forjada (`<img>`, form, prefetch)
**no** lleva el Bearer, así que el backend la rechaza. El problema de "mutar por GET"
queda como cuestión de **corrección REST / caché / logs**, no como agujero de seguridad.
Por eso las demás rutas mutantes-por-GET (`crearOrigenCruzamiento`, `change_proyect_flower`,
`send_common_bag`, `send_mail`, `crossing/modify`) — que **no tienen llamador en el
frontend** — se dejan sin tocar hasta confirmar sus consumidores (evita romper
integraciones externas desconocidas).

## Estado de los demás pendientes

- **M-2 (seguridad): RESUELTO.** El vector real era el JWT en `localStorage` (robo por
  XSS), ya eliminado. Emitir el *refresh* en cookie **HttpOnly** es solo una mejora de
  **UX** ("recordar sesión"); no se aplica aquí porque el flujo de refresh actual de la
  app es ambiguo (el login no devuelve un *refresh token* separado) y reescribirlo sin
  poder probar login en runtime podría dejar a los usuarios fuera. Recomendado hacerlo
  con pruebas: `Set-Cookie: refresh=<jwt>; HttpOnly; Secure; SameSite=Strict` +
  `supports_credentials=true` + `axios withCredentials`.
- **M-6 (resto):** ver nota de CSRF arriba — diferido por falta de consumidores confirmados.
- **B-2 · Refactor de God-controllers** (`LibroCampoController`, `VarietyController`):
  alto riesgo por el SQL complejo, valor de seguridad bajo; necesita pruebas de regresión.
- **B-3 · Parámetros en el path** → query/body con FormRequest (cosmético).
- **B-4 · `where('var', '.*')`** → acotar el patrón; el `.*` parece intencional
  (nombres de variedad con caracteres especiales), cambiarlo sin datos reales puede
  romper búsquedas. Requiere validar contra la BD.

## Rollback

Todo está aislado en la rama `fix/security-audit`. Para descartar:
`git checkout main`. Para fusionar tras probar: `git merge fix/security-audit`.
