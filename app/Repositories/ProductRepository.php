<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Redis;

class ProductRepository implements ProductRepositoryInterface
{

    public function __construct(protected Product $product)
    {
    }

    public function create(array $params)
    {
        return $this->product->create($params);
    }

    public function index(array $params)
    {
        $perPage = $params['per_page'] ?? 15;
        $page = $params['page'] ?? 1;

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

    public function search(array $params)
    {
        $query = $params['query'] ?? '';

        // Check Redis cache first
        $cacheKey = 'search:' . md5($query);
        if (Redis::exists($cacheKey)) {
            $cachedResults = Redis::get($cacheKey);
            return response()->json([
                'data' => json_decode($cachedResults, true)
            ]);
        }

        // Perform Elasticsearch search using Scout
        $products = Product::search($query)->paginate(15)->toArray();

        // Cache results in Redis for future queries
        Redis::set($cacheKey, json_encode($products));
        Redis::expire($cacheKey, 3600); // Cache for 1 hour

        return response()->json([
            'data' => $products
        ]);
    }
}
