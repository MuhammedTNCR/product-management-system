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
        return $this->product->create($data);
    }

    public function index(int $per_page)
    {
        return $this->product->paginate($per_page);
    }
}
