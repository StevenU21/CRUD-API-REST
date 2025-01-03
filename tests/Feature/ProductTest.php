<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
        $this->assertEquals(asset('storage/products/product.jpg'), $product->image());
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
}
