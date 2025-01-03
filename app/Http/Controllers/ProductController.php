<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->get('per_page', 10);

        $products = Product::paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): ProductResource
    {
        $product = Product::create($request->validated() +
        [
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = Str::slug($request->name, '-') . '-' . $product->id . '.png';
            $product->update([
                'image' => $file->storeAs('products_images', $imageName, 'public')
            ]);
        }

        return new ProductResource($product);
    }
}
