# 📚 API CRUD en Laravel

## 🚀 Descripción

Este proyecto es una implementación sencilla de una API RESTful para operaciones CRUD (Crear, Leer, Actualizar, Eliminar) utilizando Laravel 11. No se requiere autenticación para acceder a los endpoints de la API. El proyecto también incluye pruebas unitarias para asegurar la funcionalidad de la API.

## 📋 Características

- **CRUD Completo**: Implementación completa de operaciones CRUD.
- **Laravel 11**: Utiliza la última versión de Laravel.
- **Pruebas Unitarias**: Pruebas unitarias para asegurar la fiabilidad de la API.
- **Sin Autenticación**: Acceso libre a los endpoints de la API.
- **Base de Datos MySQL/SQLite**: Utiliza MySQL o SQLite para la persistencia de datos.

## 🫡 Explicación de la Implementación

- **Manejo de Excepciones y Trait Personalizado**
Para manejar casos en los que no se encuentran registros, se utiliza una excepción personalizada NotFoundException, que se conecta a un trait FindsModelOrFail. Este trait, utilizado en el modelo, proporciona una función findOrFailCustom que lanza la excepción cuando no se encuentra el modelo en la base de datos.

- **Lógica de Manejo de Imágenes**
La clase ImageService se encarga de manejar la lógica relacionada con las imágenes. Tiene dos métodos principales:

- **storeImage**: Almacena una imagen en el directorio products_images y la asocia con el producto.
- **deleteImage**: Elimina la imagen asociada a un producto del almacenamiento público.
- **Validaciones Personalizadas**
El StoreProductRequest se utiliza para validar los datos cuando se crea o actualiza un producto. Incluye reglas de validación para los campos como name, description, price, stock, e image, asegurando que cumplan con los requisitos establecidos antes de ser procesados.


## 📂 Estructura del Proyecto

```plaintext

├── app
│   ├── Exceptions          # Manejo de excepciones personalizadas
│   ├── Http                # Componentes relacionados con HTTP
│   │   ├── Controllers     # Controladores de la API
│   │   ├── Requests        # Validaciones de solicitudes
│   │   ├── Resources       # Transformadores de datos (formato JSON)
│   ├── Models              # Modelos Eloquent para la base de datos
│   ├── Services            # Lógica de negocio independiente de los controladores
│   ├── Traits              # Reutilización de código a través de Traits
│   └── ...
├── config                  # Archivos de configuración del proyecto
├── database                # Configuración de la base de datos
│   ├── factories           # Generadores de datos para pruebas
│   ├── migrations          # Esquema y estructura de la base de datos
│   └── seeders             # Población inicial de datos en tablas
├── routes
│   └── api.php             # Rutas específicas para la API
├── tests                   # Pruebas automatizadas
│   ├── Feature             # Pruebas de alto nivel (endpoints y casos de uso)
│   └── Unit                # Pruebas unitarias para funciones específicas
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

- **GET** `/api/products` - Listar todos los productos con paginación
  - Parámetros opcionales:
    - `include_id=true` - Incluir el ID del producto en la respuesta
    - `include_timestamps=true` - Incluir las marcas de tiempo en la respuesta
    - `per_page=10` - Número de productos por página (por defecto es 10)
    - `page=1` - Número de la página a obtener (por defecto es 1)
  - Ejemplos:
    - `/api/products?include_id=true&include_timestamps=true`
    - `/api/products?per_page=20&page=2`

- **POST** `/api/products` - Crear un nuevo producto

- **GET** `/api/products/{product}` - Obtener un producto por ID

- **PUT/PATCH** `/api/products/{product}` - Actualizar un producto por ID
  - Nota: Para usar el método PUT o PATCH, debes agregar un campo `_method` con el valor `PUT` o `PATCH` en la petición POST. Esto es necesario cuando se envían datos como `form-data`.
  - Ejemplo:
    ```bash
    curl -X POST -F "_method=PATCH" -F "name=New Product Name" -F "price=99.99" http://localhost:8000/api/products/{product}
    ```

- **DELETE** `/api/products/{product}` - Eliminar un producto por ID

- **GET** `/api/products/search` - Buscar productos por término de búsqueda
  - Parámetros opcionales:
    - `q` - Término de búsqueda (por defecto es una cadena vacía)
    - `per_page=10` - Número de productos por página (por defecto es 10)
  - Ejemplos:
    - `/api/products/search?q=Test`
    - `/api/products/search?q=Product&per_page=5`

- **GET** `/api/products/autocomplete` - Autocompletar productos por término de búsqueda
  - Parámetros opcionales:
    - `q` - Término de búsqueda (por defecto es una cadena vacía)
  - Ejemplos:
    - `/api/products/autocomplete?q=Test`
## ✅ Pruebas

Ejecuta las pruebas unitarias con PHPUnit:

```bash
php artisan test
```
