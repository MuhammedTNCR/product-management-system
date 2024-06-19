<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductRepositoryInterface $productRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = $this->productRepository->index($request->per_page ?? 15);
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productRepository->create($request->validated());
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
