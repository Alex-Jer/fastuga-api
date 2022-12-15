<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductPostRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Storage;

class ProductController extends Controller
{
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
            return response(['message' => 'A product type is required'], 400);

        if ($request->hasFile('photo')) {
            $newProduct['photo_url'] = basename($request->file('photo')->store('public/products'));
            unset($newProduct['photo']);
        }

        return response(["message" => "Product created", "product" => Product::create($newProduct)]);
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
            $newProduct['photo_url'] = basename($request->file('photo')->store('public/products'));
            unset($newProduct['photo']);

            //Delete previous photo
            $product->photo_url ? Storage::delete('public/products/' . $product->photo_url) : null;
        }

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
