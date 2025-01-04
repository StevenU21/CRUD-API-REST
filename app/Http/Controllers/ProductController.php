<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Exceptions\NotFoundException;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->get('per_page', 10);

        $products = Product::paginate($perPage);

        return ProductResource::collection($products);
    }
    public function search(Request $request): AnonymousResourceCollection
    {
        $searchTerm = $request->get('q', '');
        $perPage = $request->get('per_page', 10);

        $products = Product::where('name', 'LIKE', "%{$searchTerm}%")
            ->paginate($perPage);

        if ($products->isEmpty()) {
            throw new NotFoundException();
        }

        return ProductResource::collection($products);
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $searchTerm = $request->get('q', '');

        $products = Product::where('name', 'LIKE', "%{$searchTerm}%")
            ->limit(5)
            ->get(['id', 'name']);

        if ($products->isEmpty()) {
            throw new NotFoundException();
        }

        return response()->json($products);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): ProductResource
    {
        $product = Product::findOrFailCustom($id);

        return new ProductResource($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        if ($request->hasFile('image')) {
            $path = $this->imageService->storeImage($request->file('image'), $request->name, $product->id);
            $product->update(['image' => $path]);
        }

        return response()->json([
            'message' => 'Product created successfully',
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id): JsonResponse
    {
        $product = Product::findOrFailCustom($id);

        $product->fill($request->validated());
        $product->save();

        if ($request->hasFile('image')) {
            if ($product->image) {
                $this->imageService->deleteImage($product->image);
            }

            $path = $this->imageService->storeImage($request->file('image'), $request->name, $product->id);
            $product->update(['image' => $path]);
        }

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFailCustom($id);

        if ($product->image) {
            $this->imageService->deleteImage($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
