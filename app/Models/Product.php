<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
