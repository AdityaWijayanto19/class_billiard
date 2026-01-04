<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_menu_id', 'name', 'slug', 'short_description',
        'description', 'price', 'image_path', 'labels',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    /**
     * Get full image URL with storage path prefix
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : '';
    }

    public function categoryMenu(): BelongsTo
    {
        return $this->belongsTo(CategoryMenu::class, 'category_menu_id');
    }

    public function inventory()
    {
        return $this->hasOne(FoodInventory::class, 'menu_id');
    }

    /**
     * Auto-delete image when menu is deleted
     */
    protected static function booted(): void
    {
        static::deleting(function ($menu) {
            if ($menu->image_path && Storage::disk('public')->exists($menu->image_path)) {
                Storage::disk('public')->delete($menu->image_path);
            }
        });
    }

    /**
     * Check if menu is in stock
     */
    public function isInStock(): bool
    {
        $inventory = $this->inventory;

        return $inventory ? $inventory->isInStock() : false;
    }
}
