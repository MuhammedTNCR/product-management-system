<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'price', 'stock', 'sku'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->sku = self::generateUniqueSku();
        });

        static::created(function ($product) {
            $product->saveToRedis();
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

    private function saveToRedis()
    {
        Redis::hmset("product:$this->id", [
            'name' => $this->name,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'stock' => $this->stock,
            'sku' => $this->sku
        ]);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
