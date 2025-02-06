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

    public function test_index_returns_paginated_products()
    {
        // Crear algunos productos para probar
        Product::factory()->count(20)->create();

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

    public function test_index_returns_paginated_products_with_custom_per_page()
    {
        // Crear algunos productos para probar
        Product::factory()->count(10)->create();

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

        $response->assertStatus(201);
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


    public function test_it_can_search_products()
    {
        // Create products
        Product::factory()->create(['name' => 'Test Product 1', 'description' => 'Description 1']);
        Product::factory()->create(['name' => 'Another Product', 'description' => 'Description 2']);

        // Hacer una solicitud GET a la ruta de búsqueda de productos
        $response = $this->getJson('/api/products/search?q=Test');

        // Asegúrese de que la respuesta sea exitosa y contenga los productos correctos
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Test Product 1']);
    }

    public function test_it_returns_not_found_if_no_products_match_search()
    {
        // Create a product
        Product::factory()->create(['name' => 'Test Product 1', 'description' => 'Description 1']);

        // Hacer una solicitud GET a la ruta de búsqueda de productos
        $response = $this->getJson('/api/products/search?q=NonExistentProduct');

        // Asegúrese de que la respuesta sea 404 y contenga un mensaje de error
        $response->assertStatus(404)
            ->assertJson(['message' => 'Resource does not exist']);
    }

    public function test_it_can_autocomplete_products()
    {
        // Create products
        Product::factory()->create(['name' => 'Test Product 1']);
        Product::factory()->create(['name' => 'Test Product 2']);
        Product::factory()->create(['name' => 'Another Product']);

        // Hacer una solicitud GET a la ruta de autocompletar productos
        $response = $this->getJson('/api/products/autocomplete?q=Test');

        // Asegúrese de que la respuesta sea exitosa y contenga los productos correctos
        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Test Product 1'])
            ->assertJsonFragment(['name' => 'Test Product 2']);
    }
    public function test_it_can_delete_a_product()
    {
        // Arrange
        $product = Product::factory()->create(['image' => 'path/to/image.jpg']);

        // Act
        $response = $this->deleteJson("/api/products/{$product->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Product deleted successfully']);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_it_returns_not_found_when_deleting_non_existent_product()
    {
        // Act
        $response = $this->deleteJson('/api/products/999');

        // Assert
        $response->assertStatus(404)
            ->assertJson(['message' => 'Resource does not exist']);
    }
}

