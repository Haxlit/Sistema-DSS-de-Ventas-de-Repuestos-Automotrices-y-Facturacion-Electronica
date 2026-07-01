<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * HU-04: Registro de productos con costo y precio
 *
 * Entidad Brand — Catálogo de marcas de repuestos.
 */
class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }
}
