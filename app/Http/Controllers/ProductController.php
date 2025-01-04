<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use App\Services\ImageService;
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
    public function store(StoreProductRequest $request): ProductResource
    {
        $product = Product::create($request->validated());

        if ($request->hasFile('image')) {
            $path = $this->imageService->storeImage($request->file('image'), $request->name, $product->id);
            $product->update(['image' => $path]);
        }

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id): ProductResource
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

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): ProductResource
    {
        $product = Product::findOrFailCustom($id);

        if ($product->image) {
            $this->imageService->deleteImage($product->image);
        }

        $product->delete();

        return new ProductResource($product);
    }
}
