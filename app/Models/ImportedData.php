<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedData extends Model
{
    protected $fillable = [
        'import_id',
        'import_type',
        'order_date',
        'channel',
        'sku',
        'item_description',
        'origin',
        'so_number',
        'total_price',
        'cost',
        'shipping_cost',
        'profit'
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'profit' => 'decimal:2'
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }
} 