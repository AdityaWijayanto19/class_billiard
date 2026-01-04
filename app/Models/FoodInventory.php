<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodInventory extends Model
{
    protected $fillable = [
        'menu_id',
        'quantity',
        'reorder_level',
        'last_restocked_at',
    ];

    protected $casts = [
        'last_restocked_at' => 'datetime',
    ];

    /**
     * Relationship to Menu
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Check if item is in stock
     */
    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Check if below reorder level
     */
    public function isBelowReorderLevel(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }
}
