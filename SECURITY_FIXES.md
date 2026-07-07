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

## Pendiente (NO aplicado — requiere cambios coordinados y pruebas en runtime)

Se dejó fuera deliberadamente para no romper el runtime sin poder probarlo:

- **M-6 · Verbos GET→POST** para mutaciones (`crearOrigenCruzamiento`,
  `enviarFlorAProyecto`, `send_common_bag`, …). Requiere cambiar la ruta **y** cada
  llamador del frontend a la vez; hay ~10 rutas afectadas.
- **M-2 (parte backend)** · emitir el *refresh* en cookie HttpOnly+Secure+SameSite
  para recuperar el "recordar sesión" de forma segura.
- **B-2 · Refactor de God-controllers** (`LibroCampoController`, `VarietyController`)
  a capa de servicios. Alto riesgo por el SQL complejo; necesita pruebas.
- **B-3 · Parámetros en el path** → mover a query/body con FormRequest.
- **B-4 · `where('var', '.*')`** → acotar el patrón (validar antes que no rompa nombres).

## Rollback

Todo está aislado en la rama `fix/security-audit`. Para descartar:
`git checkout main`. Para fusionar tras probar: `git merge fix/security-audit`.
