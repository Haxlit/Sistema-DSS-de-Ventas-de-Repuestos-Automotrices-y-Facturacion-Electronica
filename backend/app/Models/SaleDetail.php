<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * STUB TEMPORAL — referenciado por Product::saleDetails() (HU-04/05/06).
*/
class SaleDetail extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'unit_cost',
        'subtotal',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
