<?php

namespace App\Http\Controllers\api;

use App\Helpers\StorageLocation;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductPostRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Storage;

class ProductController extends Controller
{
    public const storage_loc = StorageLocation::PRODUCT_PHOTOS;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function menu()
    {
        return ProductResource::collection(Product::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $newProduct = $request->validated();
        if ($request->type == null)
            return response(['message' => 'A product type is required'], 422);

        if (!$request->hasFile('photo'))
            return response(['message' => 'You must provide a photo for your new product'], 422);

        $newProduct['photo_url'] = basename($request->file('photo')->store($this->storage_loc));
        unset($newProduct['photo']);

        return response(["message" => "Product created", "product" => new ProductResource(Product::create($newProduct))]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $newProduct = $request->validated();

        if ($request->hasFile('photo')) {
            $newProduct['photo_url'] = basename($request->file('photo')->store($this->storage_loc));
            unset($newProduct['photo']);

            //Delete previous photo
            $product->photo_url ? Storage::delete($this->storage_loc . '/' . $product->photo_url) : null;
        }

        //prevent updating type
        unset($newProduct['type']);

        $product->update($newProduct);
        return response(['message' => 'Product updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response(['message' => 'Product removed']);
    }
}
