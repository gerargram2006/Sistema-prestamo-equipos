# Sistema de préstamo de equipos

Aplicación web desarrollada con **Laravel** para el control y préstamo de equipos de laboratorio (laptops, proyectores, kits de electrónica, multímetros, etc.).

El sistema tiene **autenticación por sesión** y **paneles separados según el rol**: el **Encargado** administra el inventario (registrar, ver, editar y eliminar equipos) y el **Estudiante** consulta los equipos disponibles, los solicita y los devuelve. El proyecto parte de **Laravel Breeze** y sobre esa base se añadieron los roles, la tabla de equipos, los seeders, las vistas con Blade y todo el módulo de préstamos.

---

## 1. Descripción del Proyecto

El **Sistema de préstamo de equipos** es una aplicación web pensada para el control y préstamo del material de un laboratorio, evitando pérdidas y desorden en la entrega y devolución de equipos.

Cuenta con **dos paneles independientes**: uno para el **Encargado**, que gestiona el inventario completo (CRUD), y otro para el **Estudiante**, que solicita equipos disponibles y devuelve los que tiene a su cargo. El acceso a cada panel está protegido por autenticación y por el rol del usuario.

---

## 2. Nombres de los Integrantes

> Completa la lista con los nombres y apellidos de todos los integrantes del grupo y su rol/participación.

  - **Integrante 1:** _Gerar Rodrigo Camma Baldeon_ — _(base de datos, modelos y repositorio)_
  - **Integrante 2:** _Gabriel Quispe Huaychay_ — _(CRUD de equipos)_
  - **Integrante 3:** _Hugo Ernesto Florez Merma_ — _(roles y paneles)_
  - **Integrante 4:** _Barry Valdivia Lovon_ — _(préstamos, vista del estudiante, errores de BD y documentación)_
  - **Integrante 5:** _Samuel A. Llallacachi Salhua_ — _(autenticación y sesiones)_

**Curso:** _Nombre del curso_ · **Docente:** _Nombre del docente_ · **Fecha:** _dd/mm/aaaa_

---

## 3. Guía de Instalación Rápida

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

# 3. Instalar las dependencias del frontend y compilar los estilos
npm install && npm run build

# 4. Copiar el archivo de entorno
cp .env.example .env

# 5. Generar la clave de la aplicación
php artisan key:generate

# 6. Levantar el servidor local
php artisan serve
```

La aplicación quedará disponible en **http://localhost:8000**.

### Configurar la base de datos (`.env`)

Antes de migrar, edita el archivo `.env` con los datos de tu MySQL/MariaDB:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prestamo_equipos
DB_USERNAME=root
DB_PASSWORD=tu_password
```

> **Nota para MariaDB en Ubuntu/WSL:** si al migrar aparece
> `SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost'`, ejecuta:
>
> ```bash
> sudo mariadb -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('tu_password'); FLUSH PRIVILEGES;"
> ```

---

## 4. Base de Datos y Semillas (Seeders)

Ejecuta el siguiente comando para crear todas las tablas y cargar los datos de prueba (usuarios y equipos):

```bash
php artisan migrate:fresh --seed
```

### Credenciales de Prueba

Estas cuentas las genera el `UserSeeder` (el **registro público está desactivado**, por lo que los usuarios se cargan por seeders):

| Rol | Correo | Contraseña | ¿Qué puede hacer? |
| --- | --- | --- | --- |
| **Encargado** | `encargado@tecsup.edu.pe` | `encargado123` | Registrar, ver, editar y eliminar equipos (CRUD completo). |
| **Estudiante** | `estudiante1@tecsup.edu.pe` | `estudiante123` | Ver disponibles, solicitar equipos y devolver los suyos. |
| **Estudiante** | `estudiante2@tecsup.edu.pe` | `estudiante123` | Igual que el anterior (sirve para probar que no se devuelven equipos ajenos). |

> El `EquipoSeeder` carga además **8 equipos de ejemplo**, todos en estado `Disponible`.

---

## 5. Aplicación de los Temas del Curso (Semanas 5 a 8)

Esta sección justifica técnicamente cómo el proyecto aplica los temas vistos en clase.

### Semana 5 — Programación Orientada a Objetos (POO)

**Clases.** Todo el sistema está construido con clases: los modelos (`User`, `Equipo`), el controlador (`EquipoController`), el middleware (`CheckRole`) y los seeders (`DatabaseSeeder`, `UserSeeder`, `EquipoSeeder`). Cada clase agrupa sus datos (atributos) y su comportamiento (métodos).

**Herencia.** Nuestras clases **heredan funcionalidad base de Laravel** en lugar de reescribirla:

- `App\Models\User extends Illuminate\Foundation\Auth\User` (que a su vez extiende `Illuminate\Database\Eloquent\Model`).
  - `App\Models\Equipo extends Illuminate\Database\Eloquent\Model`.
  - `App\Http\Controllers\EquipoController extends Controller` (controlador base de Laravel).
  - Los seeders extienden `Illuminate\Database\Seeder` y las migraciones extienden `Illuminate\Database\Migrations\Migration`.

Gracias a la herencia obtenemos gratis métodos como `create()`, `update()`, `delete()`, `validate()` o `redirect()`, sin volver a programarlos.

**Encapsulamiento.** Ocultamos los detalles internos y exponemos solo lo necesario:

- El modelo `Equipo` usa la propiedad protegida `$fillable` para definir qué campos se pueden asignar masivamente (protección contra *mass assignment*).
  - El modelo `User` usa los atributos `#[Fillable([...])]` y `#[Hidden([...])]` para ocultar `password` y `remember_token` al serializar.
  - `casts()` encapsula la conversión de tipos (`password => hashed`, `email_verified_at => datetime`).
  - Las relaciones viven dentro del modelo: `Equipo::estudiante()` y `User::equiposPrestados()`.
  - En el controlador, `mensajes()` es un método `private` (solo se usa internamente).

**Polimorfismo.** Un mismo método o interfaz se comporta distinto según la clase:

- Los tres seeders sobrescriben el método `run()` de `Seeder` con comportamientos diferentes (uno llama a otros seeders, otro crea usuarios y otro crea equipos): **mismo método, distinto comportamiento**.
  - `Equipo` y `User` heredan de `Model`, por lo que comparten **la misma interfaz** (`->update()`, `->delete()`, `->save()`) aunque representen tablas distintas.
  - El middleware implementa el contrato `handle()` de Laravel, que el framework llama de forma uniforme para todos los middlewares.

### Semana 6 — Conexión con MySQL y CRUD (Eloquent + Migraciones)

**Conexión y esquema.** La conexión a MySQL se define en `.env` y el esquema se versiona con **migraciones** (`database/migrations/`), que describen las tablas `users` (con la columna `rol`) y `equipos` (con `codigo` único, `nombre`, `categoria`, `estado` y la clave foránea `estudiante_id` → `users`).

**CRUD completo sobre la tabla `equipos`** usando **Eloquent** (el ORM de Laravel) en lugar de PDO manual:

| Operación | Método / código usado | Acción en el sistema |
| --- | --- | --- |
| **C**rear | `Equipo::create($datos)` | Registrar un equipo nuevo |
| **L**eer | `Equipo::with('estudiante')->get()` | Listar el inventario |
| **A**ctualizar | `$equipo->update($datos)` | Editar un equipo |
| **E**liminar | `$equipo->delete()` | Eliminar un equipo (si no está prestado) |

**Seguridad contra inyección SQL.** El ORM genera **consultas preparadas con parámetros vinculados** (*bindings*), por lo que **no concatenamos SQL** en ningún punto del proyecto. A esto se suman:

- Las **migraciones** definen el esquema como código (sin escribir SQL suelto).
  - El **Route Model Binding** (`Equipo $equipo`) resuelve el registro de forma segura.
  - La **validación** (`$request->validate(...)` y `Rule::unique('equipos','codigo')`) filtra los datos antes de llegar a la base de datos.

### Semana 7 — Sesiones y Seguridad

**Sesiones.** El sistema mantiene la sesión del usuario con el driver de sesiones de Laravel y la autenticación de **Breeze** (`AuthenticatedSessionController` + `Auth::attempt()`). Todo se apoya en `auth()->user()` y `auth()->id()`; al cerrar sesión se invalida la sesión.

**Middlewares.** Protegemos las rutas privadas con dos filtros:

1. **`auth`:** exige que el usuario tenga sesión iniciada. Si no, redirige al login.
   2. **`rol` (personalizado, `App\Http\Middleware\CheckRole`):** verifica el rol del usuario y, si no corresponde, lo **redirige a su propio panel**.

Los grupos de rutas en `routes/web.php` aplican ambos middlewares:

```php
Route::middleware(['auth', 'rol:encargado'])->prefix('encargado')->group(...);
Route::middleware(['auth', 'rol:estudiante'])->prefix('estudiante')->group(...);
```

Con esto, **un estudiante no puede acceder por URL al dashboard del encargado ni a sus acciones**, y viceversa: aunque escriba la dirección a mano, el middleware lo devuelve a su panel. Además:

- **CSRF:** todos los formularios incluyen `@csrf`, protegiendo contra peticiones falsificadas.
  - **Contraseñas cifradas** con el cast `password => hashed` (bcrypt).
  - **Escape de salida** con `{{ }}` de Blade (protección XSS).
  - **Registro público desactivado** (los usuarios se crean por seeder).

### Semana 8 — Introducción a Frameworks con Laravel

**Arquitectura MVC.** El proyecto sigue el patrón **Modelo–Vista–Controlador**:

- **Modelo (M):** `app/Models/User.php` y `app/Models/Equipo.php` (datos y relaciones).
  - **Vista (V):** `resources/views/**` con el motor **Blade** (solo presentación).
  - **Controlador (C):** `app/Http/Controllers/EquipoController.php` (lógica de negocio).

**Enrutamiento.** Todas las solicitudes web se registran en `routes/web.php`, usando rutas con nombre, agrupadas por rol y con `Route::resource('equipos', EquipoController::class)` para el CRUD. Las rutas de autenticación están en `routes/auth.php`.

**Blade (plantillas).** Las vistas usan el layout `x-app-layout` y las directivas de Blade (`@if`, `@forelse`, `@csrf`, `@method`) junto con la interpolación `{{ }}`. Así **separa la lógica de presentación** del código PHP: el controlador prepara los datos y la vista solo los muestra.

**Herramientas del framework.** Se aprovechan Artisan (migraciones, seeders, rutas, pruebas), los componentes de Blade, el sistema de configuración (`.env`) y el registro de middleware en `bootstrap/app.php`.

---

## 6. Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/EquipoController.php   # Paneles, CRUD, préstamos y devoluciones
│   └── Middleware/CheckRole.php           # Protección por rol (alias 'rol')
├── Models/
│   ├── User.php                           # Usuarios (con rol) y sus préstamos
│   └── Equipo.php                         # Equipos del laboratorio
database/
├── migrations/                            # Esquema de users y equipos
└── seeders/                               # UserSeeder, EquipoSeeder
resources/views/
├── encargado/                             # Panel y vistas del Encargado
├── estudiante/                            # Panel del Estudiante
└── errors/                                # Vista amigable de error de BD
routes/
├── web.php                                # Rutas del sistema
└── auth.php                               # Rutas de autenticación (Breeze)
bootstrap/app.php                          # Middleware y manejo de excepciones
```

### Modelos

- **`User`:** rellenable `name`, `email`, `password`, `rol`; ocultos `password`, `remember_token`; relación `equiposPrestados()` → `hasMany(Equipo)`.
  - **`Equipo`:** rellenable `codigo`, `nombre`, `categoria`, `estado`, `estudiante_id`; relación `estudiante()` → `belongsTo(User)`.

### Base de datos

- **`users`:** columna `rol` (`enum('encargado','estudiante')`, por defecto `estudiante`).
  - **`equipos`:** `codigo` (único), `nombre`, `categoria`, `estado` (`enum('Disponible','Prestado')`) y `estudiante_id` (FK nullable, `nullOnDelete`).

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

## 8. Tecnologías Utilizadas

| Componente | Versión / Detalle |
| --- | --- |
| PHP | `^8.3` |
| Laravel Framework | `^13.17` |
| Laravel Breeze | `^2.4` (autenticación) |
| Base de datos | MySQL / MariaDB |
| Frontend | Vite + Tailwind CSS + Alpine.js |
| Pruebas | PHPUnit `^12.5` |
| Formato de código | Laravel Pint |

---

## 9. Pruebas

```bash
php artisan test
```

Las pruebas cubren la autenticación, la redirección por rol, el CRUD de equipos y el flujo completo de préstamos y devoluciones.
