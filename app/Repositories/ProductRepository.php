<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Redis;

class ProductRepository implements ProductRepositoryInterface
{

    public function __construct(protected Product $product)
    {
    }

    public function create(array $data)
    {
        return $this->product->create($data);
    }

    public function index(array $data)
    {
        $perPage = $data['per_page'] ?? 15;
        $page = $data['page'] ?? 1;
        $search = $data['search'] ?? '';

        // Calculate the start and end indices for pagination
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage - 1;

        // Fetch product IDs from the set
        $productIds = Redis::smembers('product_ids');
        $total = count($productIds);
        $productIds = array_slice($productIds, $start, $perPage);

        $products = [];

        if (!empty($productIds)) {
            // Fetch product details from Redis
            foreach ($productIds as $productId) {
                $productKey = "product:$productId";
                $product = Redis::hgetall($productKey);
                if ($product) {
                    $product['category'] = json_decode($product['category'], true); // Decode JSON string to array
                    $products[] = $product;
                }
            }
        }

        if (empty($products)) {
            // Fetch products from database
            $query = Product::query()->with('category');

            if ($search) {
                $query->where('products.id', 'like', '%' . $search . '%')
                    ->orWhere('products.name', 'like', '%' . $search . '%')
                    ->orWhereRelation('category', 'name','like', '%' . $search . '%');
            }

            $products = $query->paginate($perPage)->toArray();

            // Save products to Redis for future requests
            foreach ($products['data'] as $product) {
                Product::saveToRedis($product);
            }
        }

        return response()->json([
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'data' => $products
        ]);
    }
}
