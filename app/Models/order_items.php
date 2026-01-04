<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class order_items extends Model
{
    protected $fillable = [
        'order_id',
        'menu_name',
        'price',
        'quantity',
        'image',
    ];

    public function order()
    {
        return $this->belongsTo(orders::class, 'order_id');
    }

    protected static function booted(): void
    {
        static::deleting(function ($model) {
            if ($model->image && Storage::disk('public')->exists($model->image)) {
                Storage::disk('public')->delete($model->image);
            }
        });
    }
}
