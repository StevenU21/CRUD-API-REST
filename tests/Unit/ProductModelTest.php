<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use App\Models\Product;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes(): void
    {
        $product = new Product();

        $this->assertEquals([
            'name',
            'description',
            'price',
            'stock',
            'image',
        ], $product->getFillable());
    }

    public function test_it_casts_price_to_decimal(): void
    {
        $product = new Product();

        $this->assertEquals('decimal:2', $product->getCasts()['price']);
    }

    public function test_it_casts_stock_to_integer(): void
    {
        $product = new Product();

        $this->assertEquals('integer', $product->getCasts()['stock']);
    }
}
