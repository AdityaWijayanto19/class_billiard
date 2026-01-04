<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenReport extends Model
{
    protected $fillable = [
        'order_id',
        'customer_name',
        'table_number',
        'room',
        'total_price',
        'payment_method',
        'order_items',
        'order_date',
        'completed_at',
    ];

    protected $casts = [
        'order_items' => 'array',
        'order_date' => 'date',
        'completed_at' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    /**
     * Relasi ke orders
     */
    public function order()
    {
        return $this->belongsTo(orders::class, 'order_id');
    }
}
