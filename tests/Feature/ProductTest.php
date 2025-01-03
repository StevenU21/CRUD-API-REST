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

    
}
