<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Elastic\ScoutDriverPlus\Searchable;

class Product extends Model
{
    use Searchable;
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'price', 'stock', 'sku'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->sku = self::generateUniqueSku();
        });

        static::created(function ($product) {
            self::saveToRedis($product);
        });
    }

    private static function generateUniqueSku(): string
    {
        do {
            $sku = Str::upper(Str::random(8));
        } while (self::skuExists($sku));

        return $sku;
    }

    private static function skuExists($sku)
    {
        return self::where('sku', $sku)->exists();
    }

    public static function saveToRedis($product)
    {
        $id = $product->id ?? $product['id'];
        $name = $product->name ?? $product['name'];
        $category = !is_array($product) ? $product->category->toArray() : $product['category'];
        $price = $product->price ?? $product['price'];
        $stock = $product->price ?? $product['stock'];
        $sku = $product->sku ?? $product['sku'];

        Redis::hmset("product:$id", [
            'name' => $name,
            'category' => json_encode($category),
            'price' => $price,
            'stock' => $stock,
            'sku' => $sku
        ]);

        Redis::expire("product:$id", 3600);

        Redis::sadd('product_ids', $id);
    }

    public function toSearchableArray()
    {
        // Convert the model instance to an array
        $array = $this->toArray();

        // Include the category relation
        $array['category'] = $this->category ? $this->category->toArray() : null;
        // Return the customized array
        return $array;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
