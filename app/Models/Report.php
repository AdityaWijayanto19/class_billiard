<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'report_date',
        'start_date',
        'end_date',
        'total_orders',
        'total_revenue',
        'cash_revenue',
        'qris_revenue',
        'transfer_revenue',
        'order_summary',
        'created_by',
        'shift_id',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    protected $casts = [
        'report_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_revenue' => 'decimal:2',
        'cash_revenue' => 'decimal:2',
        'qris_revenue' => 'decimal:2',
        'transfer_revenue' => 'decimal:2',
        'order_summary' => 'array',
    ];
}
