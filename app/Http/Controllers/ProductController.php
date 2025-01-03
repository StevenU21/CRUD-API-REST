<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
    public function store(StoreProductRequest $request): ProductResource
    {
        $product = Product::create($request->validated());

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = Str::slug($request->name, '-') . '-' . $product->id . '.' . 'png';
            $path = $file->storeAs('products_images', $imageName, 'public');
            $product->update(['image' => $path]);
        }

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id): ProductResource
    {
        $product = Product::findOrFail($id);

        $product->update($request->validated());


        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image); // Corrección aquí
            }

            $file = $request->file('image');
            $imageName = Str::slug($request->name, '-') . '-' . $product->id . '.' . 'png';
            $path = $file->storeAs('products_images', $imageName, 'public');
            $product->update(['image' => $path]);
        }

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): ProductResource
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return new ProductResource($product);
    }
}
