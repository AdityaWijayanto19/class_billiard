<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryMenu extends Model
{
    // Memberitahu Laravel nama tabel secara eksplisit (opsional tapi aman)
    protected $table = 'category_menus';

    protected $fillable = ['name', 'slug', 'order_priority'];

    public function menus(): HasMany
    {
        // Mendefinisikan relasi ke model Menu dengan foreign key category_menu_id
        return $this->hasMany(Menu::class, 'category_menu_id');
    }
}
