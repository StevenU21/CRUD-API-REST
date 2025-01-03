# 📚 Proyecto API CRUD en Laravel

## 🚀 Descripción

Este proyecto es una implementación sencilla de una API RESTful para operaciones CRUD (Crear, Leer, Actualizar, Eliminar) utilizando Laravel 11. No se requiere autenticación para acceder a los endpoints de la API. El proyecto también incluye pruebas unitarias para asegurar la funcionalidad de la API.

## 📋 Características

- **CRUD Completo**: Implementación completa de operaciones CRUD.
- **Laravel 11**: Utiliza la última versión de Laravel.
- **Pruebas Unitarias**: Pruebas unitarias para asegurar la fiabilidad de la API.
- **Sin Autenticación**: Acceso libre a los endpoints de la API.
- **Base de Datos MySQL/SQLite**: Utiliza MySQL o SQLite para la persistencia de datos.

## 📂 Estructura del Proyecto

```
├── app
│   ├── Http
│   │   ├── Controllers
│   │   └── Requests
│   ├── Models
│   └── ...
├── config
├── database
│   ├── factories
│   ├── migrations
│   └── seeders
├── routes
│   └── api.php
├── tests
│   ├── Feature
│   └── Unit
└── ...
```

## ⚙️ Instalación

1. Clona el repositorio:

    ```bash
    git clone https://github.com/StevenU21/CRUD-API-REST.git
    ```

    ```bash
    cd CRUD-API-REST && code .
    ```

2. Instala las dependencias de Composer:

    ```bash
    composer install
    ```

3. Configura el archivo `.env`:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. Configura la base de datos en el archivo `.env`:

    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5. Ejecuta las migraciones y los seeders:

    ```bash
    php artisan migrate --seed
    ```

## 🛠️ Uso

Inicia el servidor de desarrollo:

```bash
php artisan serve
```

La API estará disponible en `http://localhost:8000`.

## 🔍 Endpoints

- **GET** `/api/` - Listar todos los recursos
- **POST** `/api/` - Crear un nuevo recurso
- **GET** `/api/{id}` - Obtener un recurso por ID
- **PUT/PATCH** `/api{id}` - Actualizar un recurso por ID
- **DELETE** `/api/{id}` - Eliminar un recurso por ID

## ✅ Pruebas

Ejecuta las pruebas unitarias con PHPUnit:

```bash
php artisan test
```
