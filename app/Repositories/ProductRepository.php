<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{

    public function __construct(protected Product $product)
    {
    }

    public function create(array $data)
    {
        $this->product->create($data);
    }
}
