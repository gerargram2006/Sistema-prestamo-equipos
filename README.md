# Sistema de Préstamo de Equipos de Laboratorio

Sistema web desarrollado en **Laravel 13** para controlar el préstamo de equipos (laptops, proyectores, kits Arduino, multímetros) con **paneles de acceso separados** para los roles de **Encargado** y **Estudiante**.

---

## 1. Descripción

El **Sistema de Préstamo de Equipos de Laboratorio** es una aplicación web cuya finalidad es llevar un control ordenado de la entrega y devolución del material de laboratorio, evitando pérdidas y registros manuales.

Cuenta con dos paneles independientes según el rol:

- **Encargado:** administra el inventario completo (registrar, listar, editar y eliminar equipos).
- **Estudiante:** consulta los equipos disponibles, los **solicita** (préstamo) y **devuelve** los que tiene a su cargo.

El acceso a cada panel está protegido por autenticación por sesión y por el rol del usuario.

---

## 2. Integrantes del Equipo

- **Gerar Rodrigo Camma Baldeon** — Base de datos, modelos y repositorio.
- **Quispe Huaychay Gabriel** — CRUD de equipos.
- **Hugo Ernesto Florez Merma** — Roles y paneles.
- **Barry Valdivia Lovon** — Préstamos, vista del estudiante, manejo de errores de BD y documentación.
- **Llallacachi Salhua Samuel A.** — Autenticación y sesiones.

---

## 3. Guía de Instalación

### Requisitos previos

- PHP **8.3** o superior
- Composer
- Node.js y npm
- MySQL o MariaDB

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/gerargram2006/Sistema-prestamo-equipos.git
cd Sistema-prestamo-equipos

# 2. Instalar las dependencias de PHP
composer install

# 3. Copiar el archivo de entorno y generar la clave
cp .env.example .env
php artisan key:generate
```

```bash
# 4. Crear una base de datos vacía llamada "prestamo_equipos" (desde MySQL/MariaDB)
mysql -u root -p -e "CREATE DATABASE prestamo_equipos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

> Recuerda configurar las credenciales de la base de datos en el archivo `.env`
> (`DB_DATABASE=prestamo_equipos`, `DB_USERNAME`, `DB_PASSWORD`).

```bash
# 5. Ejecutar migraciones y seeders
php artisan migrate:fresh --seed

# 6. Compilar los assets del frontend
npm install && npm run build

# 7. Levantar el servidor local
php artisan serve
```

La aplicación quedará disponible en **http://localhost:8000**.

---

## 4. Usuarios de Prueba (Seeders)

Estas cuentas son creadas automáticamente por el `UserSeeder` al ejecutar `php artisan migrate:fresh --seed`
(el registro público está desactivado, por lo que los usuarios se cargan mediante seeders):

| Rol | Correo | Contraseña |
| --- | --- | --- |
| **Encargado** | `encargado@tecsup.edu.pe` | `encargado123` |
| **Estudiante 1** | `estudiante1@tecsup.edu.pe` | `estudiante123` |
| **Estudiante 2** | `estudiante2@tecsup.edu.pe` | `estudiante123` |

> El `EquipoSeeder` carga además **8 equipos de ejemplo** (laptops, proyectores, kits Arduino y multímetros), todos en estado `Disponible`.

---

## 5. Aplicación de los Temas del Curso (Semanas 5 a 8)

Esta sección explica cómo el proyecto aplica, en la práctica, los temas vistos en clase.

### Semana 5 — Programación Orientada a Objetos (POO)

**Herencia.** Todas las clases del sistema reutilizan comportamiento de una clase base de Laravel en lugar de programarlo desde cero:

- `App\Models\Equipo extends Model` (Eloquent): hereda `create()`, `update()`, `delete()`, `find()`, etc.
- `App\Models\User extends Illuminate\Foundation\Auth\User` (que a su vez hereda de `Model`).
- `App\Http\Requests\Auth\LoginRequest extends FormRequest`: hereda la validación y la autorización de peticiones.
- `App\Http\Controllers\EquipoController extends Controller` y los seeders extienden `Illuminate\Database\Seeder`.

**Encapsulamiento.** Los datos internos se protegen y solo se exponen los campos necesarios:

- Los modelos usan `protected $fillable` (por ejemplo, en `Equipo`: `codigo`, `nombre`, `categoria`, `estado`, `estudiante_id`) para definir qué campos pueden asignarse de forma masiva y evitar el *mass assignment*.
- El modelo `User` oculta atributos sensibles con `#[Hidden(['password', 'remember_token'])]` y define sus campos con `#[Fillable([...])]`.
- Las relaciones se encapsulan en métodos del modelo (`Equipo::estudiante()`, `User::equiposPrestados()`).
- En el controlador, `mensajes()` es un método `private`: solo se usa internamente.

**Polimorfismo.** Un mismo método o interfaz se comporta distinto según la clase que lo implementa:

- Los seeders sobrescriben el método `run()` de `Seeder` con comportamientos diferentes (crear usuarios, crear equipos, o llamar a otros seeders): **mismo método, distinto comportamiento**.
- `Equipo` y `User` heredan de `Model` y comparten la misma interfaz (`->save()`, `->update()`, `->delete()`) aunque representen tablas distintas.
- `LoginRequest` sobrescribe `rules()` y `authorize()` de `FormRequest`, personalizando su comportamiento.
- El middleware `CheckRole` implementa el contrato `handle()`, que Laravel invoca de forma uniforme para todos los middlewares.

### Semana 6 — PHP y MySQL con PDO

**Conexión mediante PDO.** Laravel **utiliza PDO internamente** para conectarse a MySQL/MariaDB. La conexión se configura en el archivo `.env` (`DB_CONNECTION=mysql`, `DB_HOST`, `DB_DATABASE=prestamo_equipos`, `DB_USERNAME`, `DB_PASSWORD`). Gracias a esta capa, el framework abstrae el acceso a la base de datos.

**ORM Eloquent y sentencias preparadas.** En lugar de escribir SQL concatenado, usamos el ORM **Eloquent**, que genera **sentencias preparadas** con parámetros vinculados (*bindings*). Esto **evita la inyección SQL**, porque los datos nunca se concatenan directamente en la consulta. El CRUD de la tabla `equipos` se realiza íntegramente en `EquipoController`:

| Operación | Método usado en `EquipoController` |
| --- | --- |
| **Crear** | `Equipo::create($datos)` |
| **Leer** | `Equipo::with('estudiante')->get()` / `Equipo::where('estado', 'Disponible')->get()` |
| **Actualizar** | `$equipo->update($datos)` |
| **Eliminar** | `$equipo->delete()` |

Además, las **migraciones** (`create_users_table`, `create_equipos_table`) definen el esquema como código y el **Route Model Binding** (`Equipo $equipo`) resuelve los registros de forma segura.

**Manejo de errores de base de datos.** El controlador captura las excepciones de tipo `QueryException` con `try/catch`, las registra con `report()` y muestra un mensaje amigable en lugar del error técnico. A este respaldo se suma un manejador **global** en `bootstrap/app.php` que, ante una falla de conexión, renderiza la vista `resources/views/errors/base-datos.blade.php`.

### Semana 7 — Sesiones y Seguridad

**Sesiones con Laravel Breeze.** La autenticación se apoya en **Laravel Breeze**, que gestiona la sesión del usuario **ligada a la base de datos** (`SESSION_DRIVER=database` y la tabla `sessions` creada por la migración de usuarios). Así, cada sesión iniciada queda registrada en la base de datos.

**Prevención de fijación de sesión.** Tras un inicio de sesión correcto se ejecuta `$request->session()->regenerate()` (en `AuthenticatedSessionController::store()`), lo que **regenera el ID de sesión** y evita ataques de *session fixation*. Al cerrar sesión se ejecutan `session()->invalidate()` y `session()->regenerateToken()`.

**Middlewares de protección.** Las rutas internas se protegen con dos middlewares:

1. **`auth`:** exige que el usuario tenga una sesión iniciada; si no, redirige al login.
2. **`CheckRole` (middleware personalizado, alias `rol`):** verifica el rol del usuario y, si no corresponde, lo **redirige a su propio panel**.

```php
Route::middleware(['auth', 'rol:encargado'])->prefix('encargado')->group(...);
Route::middleware(['auth', 'rol:estudiante'])->prefix('estudiante')->group(...);
```

De este modo, **un estudiante no puede acceder por URL al dashboard del encargado ni a sus acciones**, y viceversa. Se complementa con `@csrf` en los formularios (protección CSRF), el cifrado de contraseñas (`password => hashed`) y el escape de salida de Blade (`{{ }}`).

### Semana 8 — Introducción a Frameworks con Laravel

**Arquitectura MVC.** El proyecto aplica el patrón **Modelo–Vista–Controlador**, separando responsabilidades:

- **Modelos:** en `app/Models` (`User.php`, `Equipo.php`): representan las tablas y sus relaciones.
- **Vistas:** en `resources/views` con el motor **Blade**: solo presentación (layout `x-app-layout`, directivas `@if`, `@forelse`, `@csrf`, `@method` e interpolación `{{ }}`).
- **Controladores:** en `app/Http/Controllers` (`EquipoController.php`): contienen la lógica de negocio.

**Enrutamiento.** Todas las solicitudes web se gestionan en `routes/web.php`, organizado por grupos según el rol, con rutas con nombre y con `Route::resource('equipos', EquipoController::class)` para el CRUD. Las rutas de autenticación viven en `routes/auth.php`.

**Herramientas del framework.** Se aprovechan Artisan (migraciones, seeders, rutas y pruebas), los componentes de Blade, la configuración por `.env` y el registro de middleware y excepciones en `bootstrap/app.php`.

---

## 6. Estructura del Proyecto: ¿Para qué sirve cada archivo?

> Sección pensada para la **sustentación**: explica en palabras sencillas qué hace cada archivo creado para el proyecto.

### Base de datos

| Archivo | ¿Para qué sirve? |
| --- | --- |
| `database/migrations/0001_01_01_000000_create_users_table.php` | Crea las tablas `users` (con la columna `rol`), `password_reset_tokens` y `sessions`. |
| `database/migrations/2026_10_10_235534_create_equipos_table.php` | Crea la tabla `equipos` (código único, nombre, categoría, estado y la clave foránea `estudiante_id`). |
| `database/seeders/UserSeeder.php` | Inserta los usuarios de prueba (1 encargado y 2 estudiantes) con la contraseña cifrada. |
| `database/seeders/EquipoSeeder.php` | Inserta los 8 equipos de ejemplo. |
| `database/seeders/DatabaseSeeder.php` | Orquesta los seeders: llama a `UserSeeder` y `EquipoSeeder`. |

### Modelos

| Archivo | ¿Para qué sirve? |
| --- | --- |
| `app/Models/User.php` | Representa a los usuarios; guarda el `rol`, oculta la contraseña y define la relación "equipos prestados". |
| `app/Models/Equipo.php` | Representa a los equipos; define los campos rellenables y la relación con el estudiante que lo tiene. |

### Controladores, peticiones y middleware

| Archivo | ¿Para qué sirve? |
| --- | --- |
| `app/Http/Controllers/EquipoController.php` | Es el corazón del sistema: muestra los paneles, hace el CRUD de equipos y gestiona prestar/devolver. |
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Maneja el inicio y cierre de sesión; regenera la sesión y redirige al panel según el rol. |
| `app/Http/Requests/Auth/LoginRequest.php` | Valida el correo y la contraseña del login y limita los intentos (protección contra fuerza bruta). |
| `app/Http/Middleware/CheckRole.php` | Filtra el acceso por rol: si el usuario no tiene el rol correcto, lo devuelve a su propio panel. |

### Rutas

| Archivo | ¿Para qué sirve? |
| --- | --- |
| `routes/web.php` | Define las rutas del sistema agrupadas por rol (encargado y estudiante). |
| `routes/auth.php` | Rutas de autenticación de Breeze; el registro público (`/register`) está comentado. |

### Vistas (Blade)

| Archivo | ¿Para qué sirve? |
| --- | --- |
| `resources/views/encargado/dashboard.blade.php` | Panel del Encargado: muestra el inventario y los botones para registrar, editar y eliminar. |
| `resources/views/encargado/equipos/create.blade.php` | Formulario para registrar un equipo nuevo. |
| `resources/views/encargado/equipos/edit.blade.php` | Formulario para editar un equipo existente. |
| `resources/views/estudiante/dashboard.blade.php` | Panel del Estudiante: equipos disponibles para solicitar y sus préstamos para devolver. |
| `resources/views/errors/base-datos.blade.php` | Página amigable que se muestra si la base de datos falla. |

### Configuración y documentación

| Archivo | ¿Para qué sirve? |
| --- | --- |
| `bootstrap/app.php` | Registra las rutas, el alias del middleware `rol` y el manejo global de errores de base de datos. |
| `lang/es/auth.php` | Traduce al español los mensajes de autenticación (credenciales incorrectas, etc.). |
| `.env.example` | Plantilla de configuración (base de datos, idioma `es`, sesiones en base de datos). |
| `README.md` | Documentación del proyecto (este archivo). |

### Pruebas

| Archivo | ¿Para qué sirve? |
| --- | --- |
| `tests/Feature/EquipoCrudTest.php` | Verifica el registro de equipos y que un estudiante no pueda crear. |
| `tests/Feature/PrestamoEquipoTest.php` | Verifica solicitar y devolver equipos, y los bloqueos de seguridad. |

---

## 7. Rutas Principales

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

---

## 8. Tecnologías

| Componente | Versión / Detalle |
| --- | --- |
| PHP | `^8.3` |
| Laravel Framework | `^13.17` |
| Laravel Breeze | `^2.4` (autenticación) |
| Base de datos | MySQL / MariaDB (vía PDO) |
| Frontend | Vite + Tailwind CSS + Alpine.js |
| Pruebas | PHPUnit `^12.5` |
| Formato de código | Laravel Pint |

---

## 9. Pruebas

```bash
php artisan test
```

Las pruebas cubren la autenticación, la redirección por rol, el CRUD de equipos y el flujo completo de préstamos y devoluciones.
