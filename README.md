# Sistema de Préstamo de Equipos de Laboratorio

Aplicación web desarrollada con **Laravel** para el control de préstamos de equipos de laboratorio (laptops, proyectores, kits de electrónica, multímetros, etc.).

El sistema cuenta con autenticación por sesión y dos roles: el **Encargado**, que administra el inventario (CRUD), y el **Estudiante**, que solicita equipos disponibles y devuelve los que tiene a su cargo.

El proyecto parte de **Laravel Breeze** y sobre esa base se incorporaron los roles de usuario, la tabla de equipos, los seeders de datos, las vistas con Blade/MVC y todo el módulo de préstamos con sus paneles por rol.

---

## Índice

1. [Descripción del Proyecto](#1-descripción-del-proyecto)
2. [Instrucciones de Instalación](#2-instrucciones-de-instalación)
3. [Base de Datos y Usuarios de Prueba](#3-base-de-datos-y-usuarios-de-prueba)
4. [Temas Aplicados del Curso (Semanas 5 a 8)](#4-temas-aplicados-del-curso-semanas-5-a-8)
5. [Funcionalidades](#5-funcionalidades)
6. [Estructura del Proyecto](#6-estructura-del-proyecto)
7. [Rutas del Sistema](#7-rutas-del-sistema)
8. [Manejo de Errores de Base de Datos](#8-manejo-de-errores-de-base-de-datos)
9. [Pruebas Automatizadas](#9-pruebas-automatizadas)

---

## 1. Descripción del Proyecto

El **Sistema de Préstamo de Equipos de Laboratorio** es una aplicación web que permite controlar la entrega y devolución del material de laboratorio de forma ordenada y segura.

El **Encargado** registra, lista, edita y elimina equipos (CRUD completo), mientras que el **Estudiante** consulta los equipos disponibles, los solicita (queda en estado `Prestado` a su nombre) y los devuelve cuando termina de usarlos.

El acceso está protegido por autenticación y por roles, de modo que cada usuario solo puede entrar al panel y a las acciones que le corresponden.

---

## 2. Instrucciones de Instalación

### Requisitos previos

- PHP **8.3** o superior
- Composer
- Node.js y npm
- MySQL o MariaDB

### Pasos de instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/gerargram2006/Sistema-prestamo-equipos.git
cd Sistema-prestamo-equipos

# 2. Instalar las dependencias de PHP
composer install

# 3. Instalar las dependencias del frontend
npm install

# 4. Crear el archivo de entorno
cp .env.example .env

# 5. Generar la clave de la aplicación
php artisan key:generate
```

### Configurar la base de datos

Edita el `.env` con los datos de tu MySQL/MariaDB:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prestamo_equipos
DB_USERNAME=root
DB_PASSWORD=tu_password
```

> **Nota para MariaDB en Ubuntu/WSL:** si al migrar aparece el error
> `SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost'`, el usuario `root`
> está usando el plugin `unix_socket` y no acepta contraseña por TCP. Soluciónalo con:
>
> ```bash
> sudo mariadb -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('tu_password'); FLUSH PRIVILEGES;"
> ```

### Migrar, poblar y levantar el servidor

```bash
# Crea las tablas y carga los datos de prueba (usuarios y equipos)
php artisan migrate:fresh --seed

# Levanta el servidor de desarrollo
php artisan serve
```

La aplicación quedará disponible en **http://localhost:8000**.

> **Opcional (frontend en modo desarrollo):** en otra terminal ejecuta `npm run dev`.
> Para compilar los estilos una sola vez, usa `npm run build`.

---

## 3. Base de Datos y Usuarios de Prueba

### Comandos de base de datos

```bash
# Crear todas las tablas y cargar los seeders
php artisan migrate:fresh --seed

# Ejecutar solo las migraciones
php artisan migrate

# Recargar únicamente los seeders
php artisan db:seed --force

# Recargar un seeder específico
php artisan db:seed --class=UserSeeder
```

### Credenciales de prueba

Todos los usuarios se crean con el `UserSeeder` (el **registro público está desactivado**).

| Rol | Nombre | Correo | Contraseña | ¿Qué puede hacer? |
| --- | --- | --- | --- | --- |
| **Encargado** | Encargado Lab | `encargado@tecsup.edu.pe` | `encargado123` | CRUD completo del inventario: registrar, ver, editar y eliminar equipos. |
| **Estudiante** | Estudiante Uno | `estudiante1@tecsup.edu.pe` | `estudiante123` | Ver equipos disponibles, **solicitarlos** (préstamo) y **devolver** los suyos. |
| **Estudiante** | Estudiante Dos | `estudiante2@tecsup.edu.pe` | `estudiante123` | Igual que el anterior (útil para probar que no puede devolver equipos ajenos). |

> Las contraseñas se guardan cifradas automáticamente gracias al *cast* `password => hashed` del modelo `User`.

### Matriz de permisos

| Acción | Encargado | Estudiante |
| --- | :---: | :---: |
| Ver todos los equipos | Sí | No |
| Ver equipos disponibles | Sí | Sí |
| Registrar equipo | Sí | No |
| Editar equipo | Sí | No |
| Eliminar equipo (si no está prestado) | Sí | No |
| Solicitar (prestar) un equipo disponible | No | Sí |
| Devolver su propio equipo prestado | No | Sí |

### Datos de ejemplo (equipos)

El `EquipoSeeder` carga 8 equipos, todos en estado `Disponible`:

| Código | Nombre | Categoría |
| --- | --- | --- |
| `LAP-001` | Laptop Dell Latitude | Laptop |
| `LAP-002` | Laptop HP ProBook | Laptop |
| `PRO-001` | Proyector Epson X41 | Proyector |
| `PRO-002` | Proyector BenQ MS550 | Proyector |
| `ARD-001` | Kit Arduino UNO | Electrónica |
| `ARD-002` | Kit Arduino Mega | Electrónica |
| `MUL-001` | Multímetro Fluke 117 | Medición |
| `MUL-002` | Multímetro Uni-T UT61E | Medición |

---

## 4. Temas Aplicados del Curso (Semanas 5 a 8)

Esta sección explica, de forma teórica y práctica, cómo el proyecto aplica los temas vistos en clase.

### Semana 5 — Programación Orientada a Objetos (POO)

**Teoría.** La POO organiza el código en clases que agrupan datos (atributos) y comportamiento (métodos). Dos pilares que se aplican aquí son la **herencia** (una clase reutiliza y especializa a otra) y el **encapsulamiento** (ocultar los detalles internos y exponer solo lo necesario).

**Práctica en el proyecto.**

- **Herencia:** todas las clases del sistema extienden una clase base, reutilizando su comportamiento:
  - `App\Models\User extends Illuminate\Foundation\Auth\User` (a su vez, extiende `Illuminate\Database\Eloquent\Model`).
  - `App\Models\Equipo extends Illuminate\Database\Eloquent\Model`.
  - `App\Http\Controllers\EquipoController extends Controller`.
  - Los seeders (`DatabaseSeeder`, `UserSeeder`, `EquipoSeeder`) extienden `Illuminate\Database\Seeder`.
  - Las migraciones extienden `Illuminate\Database\Migrations\Migration`.
  - El middleware `App\Http\Middleware\CheckRole` implementa el contrato de middleware de Laravel.

- **Encapsulamiento:**
  - En `Equipo`, la propiedad protegida `$fillable` define **qué campos se pueden asignar masivamente**, evitando que un usuario malicioso envíe columnas no previstas.
  - En `User` se usan los atributos `#[Fillable([...])]` y `#[Hidden([...])]` para ocultar `password` y `remember_token` al serializar el modelo.
  - El método `casts()` encapsula la conversión de tipos (`password => hashed`, `email_verified_at => datetime`).
  - Las relaciones se encapsulan en métodos del modelo: `Equipo::estudiante()` y `User::equiposPrestados()`.
  - En el controlador, `mensajes()` es un método `private`: solo se usa internamente.
  - El middleware `CheckRole` encapsula **toda** la lógica de autorización por rol en un único lugar.

### Semana 6 — Conexión con MySQL y CRUD (Eloquent + Migraciones)

**Teoría.** En lugar de usar PDO "a mano", Laravel usa un **ORM (Eloquent)** y **migraciones** para definir el esquema de la base de datos como código. El ORM genera *consultas preparadas* con parámetros vinculados, lo que evita la inyección SQL.

**Práctica en el proyecto.**

- **Migraciones** (`database/migrations/`): definen las tablas `users` (con la columna `rol`) y `equipos` (con `codigo` único, `nombre`, `categoria`, `estado` y la clave foránea `estudiante_id` que referencia a `users` con `nullOnDelete`).
- **CRUD con Eloquent** sobre la tabla `equipos`:

  | Operación | Código usado |
  | --- | --- |
  | **C**rear | `Equipo::create($datos)` |
  | **L**eer | `Equipo::with('estudiante')->get()` / `Equipo::where('estado', 'Disponible')->get()` |
  | **A**ctualizar | `$equipo->update($datos)` |
  | **E**liminar | `$equipo->delete()` |

- **Seguridad contra inyección SQL:** todas las consultas pasan por Eloquent/Query Builder, que usa *bindings* automáticamente. **No hay SQL concatenado** en el proyecto.
- **Route Model Binding:** en las rutas `{equipo}` se inyecta directamente el modelo `Equipo $equipo`, sin consultas manuales.
- **Validación** con `$request->validate(...)` y reglas como `Rule::unique('equipos', 'codigo')->ignore($equipo->id)` para editar sin chocar con el código propio.

### Semana 7 — Sesiones y Seguridad

**Teoría.** Las sesiones permiten recordar al usuario autenticado entre peticiones. Los **middlewares** son filtros que se ejecutan antes de llegar al controlador y permiten bloquear el acceso no autorizado.

**Práctica en el proyecto.**

- **Sesiones y login:** Breeze gestiona la sesión con `AuthenticatedSessionController` y `Auth::attempt()`. Todo el sistema se apoya en `auth()->user()` y `auth()->id()`.
- **Middleware `auth`:** obliga a iniciar sesión; cualquier ruta interna sin sesión redirige al login.
- **Middleware `CheckRole` (alias `rol`):** definido en `app/Http/Middleware/CheckRole.php` y registrado en `bootstrap/app.php`. Verifica el rol y, si no corresponde, **redirige al panel propio** del usuario; si no hay sesión, va al login.
- **Rutas protegidas por grupo** (`routes/web.php`):
  ```php
  Route::middleware(['auth', 'rol:encargado'])->prefix('encargado')->group(...);
  Route::middleware(['auth', 'rol:estudiante'])->prefix('estudiante')->group(...);
  ```
  Esto impide que un estudiante acceda por URL a las rutas del encargado (y viceversa).
- **CSRF:** todos los formularios usan `@csrf` (y `@method('PUT'/'DELETE')` cuando corresponde), protegiendo contra peticiones falsificadas.
- **Contraseñas cifradas** con el *cast* `password => hashed` (bcrypt) y nunca se muestran (atributo `Hidden`).
- **Registro público desactivado:** la ruta `/register` está comentada; los usuarios se crean por seeder.
- **Escape de salida:** Blade escapa las variables con `{{ }}`, previniendo XSS.
- **Validación de negocio:** solo se puede solicitar un equipo `Disponible`, y solo el dueño puede devolver `su` equipo.

### Semana 8 — Framework Laravel (MVC, Routing y Blade)

**Teoría.** Laravel implementa el patrón **MVC**: el **Modelo** maneja los datos, la **Vista** la presentación y el **Controlador** la lógica. El **enrutamiento** (`routes/web.php`) conecta URLs con controladores, y **Blade** es el motor de plantillas que separa el HTML de la lógica.

**Práctica en el proyecto.**

- **Modelos (M):** `app/Models/User.php` y `app/Models/Equipo.php`.
- **Vistas (V):** `resources/views/` con **Blade** (layout `x-app-layout`, directivas `@if`, `@forelse`, `@csrf`, `@method` e interpolación `{{ }}`).
- **Controladores (C):** `app/Http/Controllers/EquipoController.php` (paneles, CRUD, préstamos y devoluciones).
- **Enrutamiento** en `routes/web.php`: rutas con nombre, agrupadas por rol y con `Route::resource('equipos', EquipoController::class)` para el CRUD.
- **Arquitectura y herramientas del framework:** Artisan (migraciones, seeders, rutas, tests), componentes de Blade, helpers de Laravel y registro de middleware en `bootstrap/app.php`.

---

## 5. Funcionalidades

**Implementadas actualmente:**

- **Autenticación por sesión** (login/logout) con mensajes en español; registro público desactivado.
- **Dos roles con paneles distintos**, cada uno mostrando el nombre y el rol del usuario.
- **CRUD de equipos** (Encargado): registrar (vista propia `create`), listar, editar y eliminar.
- **Préstamo y devolución** (Estudiante): solicitar un equipo disponible (pasa a `Prestado` a su nombre) y devolverlo (vuelve a `Disponible`).
- **Protección por rol** en todas las rutas privadas mediante el middleware `CheckRole`.
- **Validación de formularios** con mensajes en español, CSRF y consultas seguras con Eloquent.
- **Manejo de errores de base de datos** con una vista amigable.
- **Pruebas automatizadas** (autenticación, roles, CRUD y flujo de préstamos).

**En desarrollo / próximas mejoras:**

- Historial de préstamos (registro de quién prestó y devolvió, con fechas).
- Búsqueda y filtros de equipos por código, nombre o categoría.
- Paginación del inventario.
- Reportes exportables (PDF/Excel) de equipos y préstamos activos.
- Notificaciones por correo al solicitar y devolver un equipo.

---

## 6. Estructura del Proyecto

### Modelos

- **`User`** (`app/Models/User.php`)
  - Atributos rellenables: `name`, `email`, `password`, `rol`.
  - Atributos ocultos: `password`, `remember_token`.
  - Casts: `email_verified_at => datetime`, `password => hashed`.
  - Relación: `equiposPrestados()` → `hasMany(Equipo::class, 'estudiante_id')`.

- **`Equipo`** (`app/Models/Equipo.php`)
  - Atributos rellenables: `codigo`, `nombre`, `categoria`, `estado`, `estudiante_id`.
  - Relación: `estudiante()` → `belongsTo(User::class, 'estudiante_id')`.

### Base de datos

- **`users`**: columna `rol` (`enum('encargado','estudiante')`, por defecto `estudiante`).
- **`equipos`**:
  - `codigo` (único), `nombre`, `categoria`.
  - `estado` (`enum('Disponible','Prestado')`, por defecto `Disponible`).
  - `estudiante_id` (FK nullable a `users`, `nullOnDelete`).

### Controladores y middleware

- `app/Http/Controllers/EquipoController.php` → paneles, CRUD de equipos y préstamos/devoluciones.
- `app/Http/Middleware/CheckRole.php` → restringe el acceso según el rol (alias `rol`).
- `bootstrap/app.php` → registra el alias `rol` y el respaldo global ante errores de base de datos.

### Seeders

- `database/seeders/DatabaseSeeder.php` → ejecuta `UserSeeder` y `EquipoSeeder`.
- `database/seeders/UserSeeder.php` → usuarios de prueba.
- `database/seeders/EquipoSeeder.php` → equipos de ejemplo.

### Vistas

- `resources/views/encargado/dashboard.blade.php` → panel del Encargado (inventario + acceso a registrar, editar y eliminar).
- `resources/views/encargado/equipos/create.blade.php` → formulario de registro de equipos.
- `resources/views/encargado/equipos/edit.blade.php` → formulario de edición de equipos.
- `resources/views/estudiante/dashboard.blade.php` → panel del Estudiante (disponibles para solicitar + sus préstamos para devolver).
- `resources/views/errors/base-datos.blade.php` → vista amigable cuando falla la base de datos.

---

## 7. Rutas del Sistema

| Método | Ruta | Nombre | Acceso |
| --- | --- | --- | --- |
| GET | `/` | — | Redirige al login |
| GET | `/dashboard` | `dashboard` | Autenticado (redirige a su panel) |
| GET | `/encargado/dashboard` | `encargado.dashboard` | Encargado |
| GET | `/encargado/equipos/create` | `equipos.create` | Encargado |
| POST | `/encargado/equipos` | `equipos.store` | Encargado |
| GET | `/encargado/equipos/{equipo}/edit` | `equipos.edit` | Encargado |
| PUT/PATCH | `/encargado/equipos/{equipo}` | `equipos.update` | Encargado |
| DELETE | `/encargado/equipos/{equipo}` | `equipos.destroy` | Encargado |
| GET | `/estudiante/dashboard` | `estudiante.dashboard` | Estudiante |
| POST | `/estudiante/equipos/{equipo}/prestar` | `equipos.prestar` | Estudiante |
| POST | `/estudiante/equipos/{equipo}/devolver` | `equipos.devolver` | Estudiante |
| GET | `/profile` | `profile.edit` | Autenticado |

Las rutas de autenticación de Breeze (login, logout, recuperación de contraseña) viven en `routes/auth.php`.
El registro público (`/register`) está comentado.

---

## 8. Manejo de Errores de Base de Datos

Si MySQL/MariaDB no está disponible, la aplicación **no** muestra el error técnico. En su lugar:

- El controlador captura las `QueryException`, las registra con `report()` y devuelve un mensaje entendible.
- Existe un respaldo global en `bootstrap/app.php` (`withExceptions`) que muestra `resources/views/errors/base-datos.blade.php`, una vista simple e **independiente de la sesión** (importante porque la sesión también usa la base de datos).

---

## 9. Pruebas Automatizadas

```bash
php artisan test
```

Las pruebas (PHPUnit) cubren:

- Autenticación (login/logout) y registro desactivado.
- Redirección según el rol y protección del middleware `CheckRole`.
- CRUD de equipos: ver el formulario de creación, registrar (disponible/prestado) y validación de código duplicado.
- Flujo de préstamos: solicitar un equipo disponible, devolver el propio, y bloqueos (no devolver equipos ajenos, no solicitar uno ya prestado, y el encargado no puede solicitar).

---

## Tecnologías

| Componente | Versión / Detalle |
| --- | --- |
| PHP | `^8.3` |
| Laravel Framework | `^13.17` |
| Laravel Breeze | `^2.4` (autenticación) |
| Base de datos | MySQL / MariaDB |
| Frontend | Vite + Tailwind CSS + Alpine.js |
| Tests | PHPUnit `^12.5` |
| Formato de código | Laravel Pint |

---

## Autor

Proyecto desarrollado por **gerargram2006** para el examen final del curso.

Repositorio: <https://github.com/gerargram2006/Sistema-prestamo-equipos>
