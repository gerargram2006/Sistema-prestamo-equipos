# Sistema de Préstamo de Equipos de Laboratorio

Aplicación web desarrollada con **Laravel** para gestionar el préstamo de equipos de laboratorio (laptops, proyectores, kits de electrónica, multímetros, etc.) entre un **Encargado** y los **Estudiantes**.

El proyecto parte de una instalación de **Laravel Breeze** (autenticación por sesión) y sobre ella se agregaron los roles de usuario, la tabla de equipos, los seeders de datos y todo el módulo de préstamos con sus paneles por rol.

---

## Tecnologías

| Componente | Versión / Detalle |
| --- | --- |
| PHP | `^8.3` |
| Laravel Framework | `13.x` |
| Laravel Breeze | `^2.4` (autenticación) |
| Laravel Tinker | `^3.0` |
| Base de datos | MySQL / MariaDB |
| Frontend | Vite + Tailwind CSS + Alpine.js |
| Tests | PHPUnit `^12.x` |
| Formato de código | Laravel Pint |

---

## Funcionalidades

- **Ingreso con correo y contraseña.** Solo usuarios registrados. Si las credenciales son incorrectas se muestra un mensaje claro en español.
- **Ninguna página interna es accesible sin sesión.** Todo redirige al login. Opción de cerrar sesión.
- **Dos roles con paneles distintos**, cada uno mostrando el nombre y el rol del usuario:
  - **Encargado:** registra, lista, edita, elimina equipos y gestiona préstamos y devoluciones.
  - **Estudiante:** solo consulta los equipos **disponibles** (modo lectura, sin acciones de modificación).
- **Gestión de equipos** con código, nombre, categoría y estado (`Disponible` / `Prestado`).
- **Préstamos y devoluciones:** prestar un equipo a un estudiante lo deja en `Prestado`; devolverlo lo vuelve a `Disponible`. No se permite eliminar un equipo que está prestado.
- **Formularios seguros:** validación, protección CSRF y consultas mediante Eloquent (sin SQL concatenado).
- **Manejo de errores de base de datos:** si la BD falla, el usuario ve una vista amigable y nunca un error técnico.

---

## Roles y permisos

| Acción | Encargado | Estudiante |
| --- | :---: | :---: |
| Ver todos los equipos | Sí | No |
| Ver solo equipos disponibles | Sí | Sí |
| Registrar equipo | Sí | No |
| Editar equipo | Sí | No |
| Eliminar equipo (si no está prestado) | Sí | No |
| Prestar / devolver equipo | Sí | No |

La protección se aplica con el middleware `CheckRole` (alias `rol`), registrado en `bootstrap/app.php`. Si un usuario intenta entrar a un panel que no le corresponde, se le redirige automáticamente a su propio panel.

---

## Requisitos previos

- PHP 8.3 o superior
- Composer
- Node.js y npm
- MySQL o MariaDB

---

## Instalación

> **Atajo:** existe un script `composer run setup` que hace la instalación completa
> (dependencias, `.env`, clave, migraciones y build). Si lo usas, salta al paso de
> [configurar la base de datos](#configurar-la-base-de-datos) primero para ajustar el `.env`.

```bash
# 1. Clonar el repositorio
git clone <url-del-repositorio>
cd prestamo-equipos

# 2. Instalar dependencias de PHP
composer install

# 3. Instalar dependencias de frontend
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

### Idioma

El proyecto está configurado en español. Verifica en tu `.env`:

```env
APP_LOCALE=es
APP_FALLBACK_LOCALE=en
```

> Los mensajes de autenticación en español están en `lang/es/auth.php`.

> **Nota para MariaDB en Ubuntu/WSL:** si al migrar ves
> `SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost'`, es porque el
> usuario `root` usa el plugin `unix_socket` y no acepta contraseña por TCP.
> Soluciónalo con:
>
> ```bash
> sudo mariadb -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('tu_password'); FLUSH PRIVILEGES;"
> ```

### Migrar y poblar la base de datos

```bash
php artisan migrate:fresh --seed
```

Esto crea las tablas y carga los datos de prueba (usuarios y equipos).

### Levantar el proyecto

```bash
# Opción 1: todo junto (servidor + Vite + queue + logs)
composer run dev

# Opción 2: en dos terminales
php artisan serve
npm run dev
```

La aplicación quedará disponible en `http://localhost:8000`.

---

## Usuarios de prueba

Creados por `UserSeeder`:

| Nombre | Email | Contraseña | Rol |
| --- | --- | --- | --- |
| Encargado Lab | `encargado@tecsup.edu.pe` | `encargado123` | `encargado` |
| Estudiante Uno | `estudiante1@tecsup.edu.pe` | `estudiante123` | `estudiante` |
| Estudiante Dos | `estudiante2@tecsup.edu.pe` | `estudiante123` | `estudiante` |

> La contraseña se cifra automáticamente gracias al cast `password => hashed` del modelo `User`.
>
> El **registro público está desactivado**: los usuarios se cargan por seeder.

---

## Datos de ejemplo

`EquipoSeeder` carga 8 equipos, todos en estado `Disponible`:

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

```bash
# Recargar solo los seeders
php artisan db:seed --force

# Recargar un seeder específico
php artisan db:seed --class=UserSeeder
```

---

## Estructura del proyecto

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
- `bootstrap/app.php` → registra el alias y el respaldo global ante errores de base de datos.

### Seeders

- `database/seeders/DatabaseSeeder.php` → ejecuta `UserSeeder` y `EquipoSeeder`.
- `database/seeders/UserSeeder.php` → usuarios de prueba.
- `database/seeders/EquipoSeeder.php` → equipos de ejemplo.

### Rutas

| Método | Ruta | Nombre | Acceso |
| --- | --- | --- | --- |
| GET | `/` | — | Redirige al login |
| GET | `/dashboard` | `dashboard` | Autenticado (redirige a su panel) |
| GET | `/encargado/dashboard` | `encargado.dashboard` | Encargado |
| POST | `/encargado/equipos` | `equipos.store` | Encargado |
| GET | `/encargado/equipos/{equipo}/edit` | `equipos.edit` | Encargado |
| PUT | `/encargado/equipos/{equipo}` | `equipos.update` | Encargado |
| DELETE | `/encargado/equipos/{equipo}` | `equipos.destroy` | Encargado |
| POST | `/encargado/equipos/{equipo}/prestar` | `equipos.prestar` | Encargado |
| POST | `/encargado/equipos/{equipo}/devolver` | `equipos.devolver` | Encargado |
| GET | `/estudiante/dashboard` | `estudiante.dashboard` | Estudiante |
| GET | `/profile` | `profile.edit` | Autenticado |

Las rutas de autenticación de Breeze (login, logout, recuperación de contraseña) viven en `routes/auth.php`.
El registro público (`/register`) está comentado.

### Vistas

- `resources/views/encargado/dashboard.blade.php` → panel del Encargado (inventario + registrar + prestar/devolver).
- `resources/views/encargado/equipos/edit.blade.php` → formulario de edición de equipos.
- `resources/views/estudiante/dashboard.blade.php` → panel del Estudiante (solo equipos disponibles).
- `resources/views/errors/base-datos.blade.php` → vista amigable cuando falla la base de datos.

---

## Manejo de errores de base de datos

Si MySQL/MariaDB no está disponible, la aplicación no muestra el error técnico. En su lugar:

- El controlador captura las `QueryException`, las registra con `report()` y devuelve un mensaje entendible.
- Existe un respaldo global en `bootstrap/app.php` (`withExceptions`) que muestra
  `resources/views/errors/base-datos.blade.php`, una vista simple e independiente de la sesión
  (importante porque la sesión también usa la base de datos).

---

## Pruebas

```bash
php artisan test
```

Cubren autenticación, redirección por rol, registro desactivado y el flujo general.
