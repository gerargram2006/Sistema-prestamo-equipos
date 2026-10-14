    # Sistema de Préstamo de Equipos de Laboratorio

Aplicación web desarrollada con **Laravel** para gestionar el préstamo de equipos de laboratorio (laptops, proyectores, kits de electrónica, multímetros, etc.) entre un **encargado** y los **estudiantes**.

El proyecto parte de una instalación de **Laravel Breeze** (autenticación por sesión) y sobre ella se fueron agregando el rol de usuario, la tabla de equipos y los seeders de datos.

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

- **`users`**: se agregó la columna `rol`
  (`enum('encargado','estudiante')`, por defecto `estudiante`).
- **`equipos`**:
  - `codigo` (único), `nombre`, `categoria`.
  - `estado` (`enum('Disponible','Prestado')`, por defecto `Disponible`).
  - `estudiante_id` (FK nullable a `users`, `nullOnDelete`).

### Seeders

- `database/seeders/DatabaseSeeder.php` → ejecuta `UserSeeder` y `EquipoSeeder`.
- `database/seeders/UserSeeder.php` → usuarios de prueba.
- `database/seeders/EquipoSeeder.php` → equipos de ejemplo.

### Rutas

| Método | Ruta | Descripción |
| --- | --- | --- |
| GET | `/` | Página de bienvenida |
| GET | `/dashboard` | Panel principal (requiere autenticación) |
| GET | `/login`, `/register` | Autenticación (Breeze) |
| GET/PATCH/DELETE | `/profile` | Gestión del perfil del usuario |

---

## En desarrollo

- CRUD de equipos: `EquipoController` está creado como esqueleto, pero sus
  métodos y rutas aún no están implementados.
- Lógica de préstamos y devoluciones de equipos.
