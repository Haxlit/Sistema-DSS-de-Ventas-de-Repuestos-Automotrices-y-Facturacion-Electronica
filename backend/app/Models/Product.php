<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'brand_id',
        'category_id',
        'compatibility',
        'price',
        'cost',
        'stock',
        'stock_min',
        'estado'
    ];

    // Para asegurarte de que maneje el JSON correctamente
    protected $casts = [
        'compatibility' => 'array',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}