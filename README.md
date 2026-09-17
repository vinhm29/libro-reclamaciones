# Sistema de Libro de Reclamaciones

## Descripción general

Este proyecto implementa una solución web para gestionar el libro de reclamaciones de una institución o empresa. Está orientado a dos canales principales:

- Canal público: el ciudadano puede buscar su información por documento, completar un formulario de reclamo y registrar la queja o reclamo en el sistema.
- Canal administrativo: un usuario administrador puede autenticarse, consultar reclamos, responder oficialmente y cerrar la atención del caso.

La aplicación está desarrollada con Laravel 13 y utiliza SQLite como base de datos local por defecto en el proyecto, con autenticación por Sanctum para la parte administrativa.

---

## Objetivo funcional

El sistema permite:

- Registrar ciudadanos con datos básicos y documentales.
- Asociar cada reclamo a un establecimiento, clasificación y persona.
- Generar un correlativo único por reclamo.
- Consultar el detalle de un reclamo por su correlativo.
- Mantener un registro de respuestas oficiales emitidas por la empresa.
- Restrigir el acceso administrativo a usuarios con permisos de administrador.

---

## Flujo de uso de la aplicación

### 1. Canal público

La persona interesada ingresa a la página del formulario y realiza estos pasos:

1. Indica el tipo y número de documento.
2. El sistema busca la persona en la base de datos mediante la API de consulta.
3. Si ya existe, se cargan sus datos para completar el formulario.
4. Si no existe, se registra como ciudadano con sus datos básicos.
5. Selecciona el establecimiento, la clasificación del reclamo y escribe el detalle del problema.
6. El sistema genera un correlativo único y guarda el reclamo.
7. Se devuelve un mensaje de éxito con el código del reclamo para seguimiento.

El flujo principal está soportado por los controladores de la API REST definida en `routes/api.php`.

### 2. Canal administrativo

El personal administrativo accede a la sección protegida con credenciales autorizadas:

1. Inicia sesión con correo y contraseña.
2. El backend valida las credenciales y exige que el usuario tenga `is_admin = true`.
3. Si es válido, genera un token de acceso Sanction/Passport-like (Sanctum).
4. El panel administrativo listas los reclamos, revisa detalles y registra la respuesta.
5. Se puede cerrar la gestión del reclamo con un estado final, archivo adjunto y observaciones.

---

## Arquitectura principal

### Modelos clave

- `Persona`: almacena los datos del ciudadano o usuario afectado.
- `Reclamo`: representa el reclamo generado, con relación al ciudadano, establecimiento y clasificación.
- `Establecimiento`: representa el punto de atención relacionado al reclamo.
- `ClasifReclamo`: clasificación del tipo de reclamo.
- `RespuestaReclamo`: respuesta oficial emitida por la empresa.
- `User`: usuario del sistema con permisos administrativos.

### Relación de entidades

- Una `Persona` puede tener varios `Reclamo`.
- Un `Reclamo` pertenece a una `Persona`.
- Un `Reclamo` pertenece a un `Establecimiento`.
- Un `Reclamo` pertenece a una clasificación de reclamo.
- Un `Reclamo` puede tener una `RespuestaReclamo` asociada.

---

## Rutas principales

### Públicas

| Método | Ruta | Descripción |
| --- | --- | --- |
| `GET` | `/api/personas/buscar` | Busca una persona por tipo y número de documento. |
| `PUT` | `/api/personas/{n_documento}` | Actualiza los datos de una persona existente. |
| `POST` | `/api/reclamos` | Registra un nuevo reclamo. |
| `GET` | `/api/establecimientos` | Lista establecimientos activos. |
| `GET` | `/api/clasificaciones` | Lista clasificaciones de reclamo activas. |
| `GET` | `/api/reclamos/{correlativo}` | Consulta detalle de un reclamo por correlativo. |
| `POST` | `/api/login` | Inicia sesión administrativa. |

### Administrativas

| Método | Ruta | Descripción |
| --- | --- | --- |
| `GET` | `/api/admin/reclamos` | Lista todos los reclamos con relaciones. |
| `POST` | `/api/admin/respuestas` | Registra la respuesta oficial de la empresa. |
| `GET` | `/api/admin/respuestas/{correlativo}` | Consulta la respuesta asociada a un reclamo. |
| `GET` | `/api/admin/clasificaciones-respuesta` | Lista clasificaciones de respuesta. |
| `GET` | `/api/admin/estados-reclamo` | Lista estados posibles del reclamo. |
| `POST` | `/api/admin/logout` | Cierra la sesión del administrador. |

> La validación del acceso administrativo viene dada por `AdminMiddleware`, que exige que el usuario autenticado tenga `is_admin` activo.

---

## Estructura del proyecto

```text
app/
  Http/
    Controllers/API/
      AuthController.php
      ReclamoController.php
      RespuestaReclamoController.php
    Middleware/AdminMiddleware.php
  Models/
    Persona.php
    Reclamo.php
    User.php
config/
  app.php
  auth.php
  sanctum.php
  database.php
routes/
  api.php
  web.php
resources/
  views/
  js/
  css/
public/
  build/
  index.php
storage/
  app/
  logs/
  framework/
```

---

## Reglas de negocio importantes

### Generación del correlativo

Cada reclamo recibe un código único con formato similar a:

```text
REC-2026-A1B2C3
```

Se genera aleatoriamente y se valida para evitar duplicados dentro de la base de datos.

### Manejo de transacciones

Las operaciones críticas de creación de reclamo y respuesta se ejecutan dentro de transacciones de base de datos para garantizar consistencia. Si ocurre un error, se hace rollback.

### Validación de datos

La API valida estrictamente campos como:

- tipo y número de documento
- establecimiento existente
- clasificación válida
- correo con formato correcto
- fecha de nacimiento si es proporcionada
- archivo adjunto en respuestas administrativas

### Cierre del reclamo

Para registrar una respuesta de cierre, el administrador debe incluir:

- archivo adjunto
- canal de notificación
- clasificación de respuesta
- fecha de notificación
- estado del reclamo

Si el estado es de evaluación, el sistema permite mantener abierto el reclamo sin requerir esos campos adicionales.

---

## Instalación y ejecución

### Prerrequisitos

- PHP 8.3 o superior
- Composer
- Node.js y npm (si se usa build de assets)

### Pasos

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Si deseas iniciar la aplicación localmente:

```bash
php artisan serve
```

Para compilar assets frontales si el proyecto usa Vite:

```bash
npm install
npm run build
```

También puede ejecutarse en modo desarrollo con:

```bash
composer run dev
```

---

## Uso práctico del sistema

### Registrar un reclamo desde la web

1. Abrir el formulario público.
2. Ingresar documento y tipo de documento.
3. Completar nombre, domicilio, correo y teléfono si aplica.
4. Seleccionar establecimiento y clasificación.
5. Escribir el detalle del reclamo.
6. Enviar el formulario.
7. Guardar el correlativo devuelto para seguimiento.

### Consultar un reclamo

- La consulta puede hacerse por correlativo usando la ruta pública `/api/reclamos/{correlativo}`.
- El sistema retorna la información del reclamo y sus relaciones.

### Responder un reclamo como administrador

1. Iniciar sesión con un usuario admin.
2. Consultar la lista `/api/admin/reclamos`.
3. Seleccionar el reclamo a atender.
4. Enviar una respuesta a `/api/admin/respuestas` con los campos necesarios.
5. Guardar la respuesta y cerrar el caso según el estado indicado.

---

## Seguridad

- La parte administrativa está protegida con autenticación de Sanctum.
- La validación de permisos se centraliza en `AdminMiddleware`.
- Las operaciones sensibles se ejecutan con validación y transacciones.
- Los archivos adjuntos se almacenan en el disco público de Laravel.

---

## Consideraciones técnicas

- El proyecto usa Eloquent para el acceso a datos.
- Los nombres de tablas y columnas están definidos de forma específica para el dominio del libro de reclamaciones.
- La API está diseñada para ser consumida por un frontend web o una aplicación cliente.
- El backend no incluye una vista funcional completa del sitio web por sí mismo; la interfaz pública y administrativa debe integrarse como consumidor de la API.

---

## Licencia

Este proyecto se distribuye bajo la licencia MIT, tal como corresponde a una aplicación Laravel base.
