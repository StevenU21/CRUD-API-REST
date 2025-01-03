<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que la funcion image() del modelo Product retorne la imagen correcta.
     * @return void
     */
    public function test_it_returns_image_url(): void
    {
        // Simula el almacenamiento local
        Storage::fake('public');

        // Crea y guarda un archivo de imagen simulado
        UploadedFile::fake()->image('product.jpg')->storeAs('products', 'product.jpg', 'public');

        // Crea un producto con la imagen
        $product = Product::factory()->create([
            'image' => 'product.jpg',
        ]);

        // Verifica que la URL de la imagen sea correcta
        $this->assertEquals(asset('storage/products_images/product.jpg'), $product->image());
    }

    /**
     * Verifica que la funcion image() del modelo Product retorne la imagen por defecto.
     * @return void
     */
    public function test_it_returns_default_image_url(): void
    {
        // Crea un producto sin imagen
        $product = Product::factory()->create([
            'image' => null,
        ]);

        // Verifica que la URL de la imagen por defecto sea correcta
        $this->assertEquals('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRwm0rdbOAslibv0mLIxWKZ6C6r9m8fujTIBA&s', $product->image());
    }

    public function test_index_returns_paginated_products()
    {
        // Crear algunos productos para probar
        Product::factory()->count(30)->create();

        // Hacer una solicitud GET a la ruta de índice de productos
        $response = $this->getJson('/api/products?per_page=10');

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que la respuesta tenga la estructura correcta
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'description',
                    'price',
                    'stock',
                ]
            ],
            'links',
            'meta'
        ]);

        // Verificar que se devuelvan 10 productos por página
        $this->assertCount(10, $response->json('data'));
    }

    public function test_index_returns_paginated_products_with_default_per_page()
    {
        // Crear algunos productos para probar
        Product::factory()->count(30)->create();

        // Hacer una solicitud GET a la ruta de índice de productos
        $response = $this->getJson('/api/products');

        // Verificar que se devuelvan 10 productos por página
        $this->assertCount(10, $response->json('data'));
    }

    public function test_index_returns_paginated_products_with_custom_per_page()
    {
        // Crear algunos productos para probar
        Product::factory()->count(30)->create();

        // Hacer una solicitud GET a la ruta de índice de productos
        $response = $this->getJson('/api/products?per_page=5');

        // Verificar que se devuelvan 5 productos por página
        $this->assertCount(5, $response->json('data'));
    }

    public function test_index_includes_id()
    {
        // Crear algunos productos para probar
        Product::factory()->count(10)->create();

        // Hacer una solicitud GET a la ruta de índice de productos
        $response = $this->getJson('/api/products?include_id=1');

        // Verificar que la respuesta tenga la estructura correcta
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'stock',
                ]
            ],
            'links',
            'meta'
        ]);
    }

    public function test_index_includes_timestamps()
    {
        // Crear algunos productos para probar
        Product::factory()->count(10)->create();

        // Hacer una solicitud GET a la ruta de índice de productos
        $response = $this->getJson('/api/products?include_timestamps=1');

        // Verificar que la respuesta tenga la estructura correcta
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'description',
                    'price',
                    'stock',
                    'created_at',
                ]
            ],
            'links',
            'meta'
        ]);
    }

    public function test_show_returns_single_product()
    {
        // Crear un producto para probar
        $product = Product::factory()->create();

        // Hacer una solicitud GET a la ruta de un solo producto
        $response = $this->getJson("/api/products/{$product->id}");

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que la respuesta tenga la estructura correcta
        $response->assertJsonStructure([
            'data' => [
                'name',
                'description',
                'price',
                'stock',
            ]
        ]);

        // Verificar que la respuesta tenga los datos correctos
        $response->assertJsonFragment([
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
        ]);
    }

    public function test_store_creates_new_product()
    {
        // Simular el almacenamiento de archivos
        Storage::fake('public');

        // Crear un producto simulado
        $productData = Product::factory()->make()->toArray();

        // Agregar un archivo de imagen simulado
        $productData['image'] = UploadedFile::fake()->image('product.jpg');

        // Hacer una solicitud POST a la ruta de almacenamiento de productos
        $response = $this->postJson('/api/products', $productData);

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(201);

        // Verificar que el producto se haya almacenado en la base de datos
        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'description' => $productData['description'],
            'price' => $productData['price'],
            'stock' => $productData['stock'],
        ]);

        // Verificar que la imagen se haya almacenado
        Storage::disk('public')->assertExists('products_images/' . Str::slug($productData['name'], '-') . '-1.png');
    }

    public function test_store_creates_new_product_without_image()
    {
        // Crear un producto simulado sin imagen
        $productData = Product::factory()->make()->toArray();
        $productData['image'] = null;

        // Hacer una solicitud POST a la ruta de almacenamiento de productos
        $response = $this->postJson('/api/products', $productData);

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(201);
    }

    public function test_store_returns_validation_error()
    {
        // Hacer una solicitud POST a la ruta de almacenamiento de productos con datos inválidos
        $response = $this->postJson('/api/products', []);

        // Verificar que la respuesta tenga un error de validación
        $response->assertStatus(422);

        // Verificar que la respuesta tenga la estructura correcta
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'description',
                'price',
                'stock',
            ]
        ]);
    }

    public function test_update_product_name()
    {
        // Crear un producto existente
        $product = Product::factory()->create([
            'name' => 'Old Name',
        ]);

        // Crear datos de actualización
        $updateData = [
            'name' => 'New Name',
            'description' => 'Updated Description',
            'price' => 200,
            'stock' => 20,
        ];

        // Hacer una solicitud PATCH a la ruta de actualización de productos
        $response = $this->patchJson("/api/products/{$product->id}", $updateData);

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que el producto se haya actualizado en la base de datos
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
            'description' => 'Updated Description',
            'price' => 200,
            'stock' => 20,
        ]);
    }

    public function test_update_product_with_image()
    {
        // Simular el almacenamiento de archivos
        Storage::fake('public');

        // Crear un producto existente
        $product = Product::factory()->create([
            'name' => 'Old Name',
            'image' => 'products_images/old-image.png',
        ]);

        // Crear datos de actualización
        $updateData = [
            'name' => 'New Name',
            'description' => 'Updated Description',
            'price' => 200,
            'stock' => 20,
            'image' => UploadedFile::fake()->image('new-product.jpg'),
        ];

        // Hacer una solicitud PATCH a la ruta de actualización de productos
        $response = $this->patchJson("/api/products/{$product->id}", $updateData);

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que el producto se haya actualizado en la base de datos
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
            'description' => 'Updated Description',
            'price' => 200,
            'stock' => 20,
        ]);

        // Verificar que la nueva imagen se haya almacenado y la antigua se haya eliminado
        Storage::disk('public')->assertExists('products_images/new-name-' . $product->id . '.png');
        Storage::disk('public')->assertMissing('products_images/old-image.png');
    }

    public function test_update_product_without_image()
    {
        // Crear un producto existente
        $product = Product::factory()->create([
            'name' => 'Old Name',
            'image' => 'products_images/old-image.png',
        ]);

        // Crear datos de actualización sin imagen
        $updateData = [
            'name' => 'New Name',
            'description' => 'Updated Description',
            'price' => 200,
            'stock' => 20,
            'image' => null,
        ];

        // Hacer una solicitud PATCH a la ruta de actualización de productos
        $response = $this->patchJson("/api/products/{$product->id}", $updateData);

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);
    }
}
