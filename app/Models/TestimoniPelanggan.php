<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TestimoniPelanggan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'customer_name',
        'name',
        'customer_role',
        'role',
        'testimonial',
        'rating',
        'photo',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Auto-delete images when deleted
     */
    protected static function booted(): void
    {
        static::deleting(function ($testimoni) {
            if ($testimoni->photo && Storage::disk('public')->exists($testimoni->photo)) {
                Storage::disk('public')->delete($testimoni->photo);
            }
            if ($testimoni->image && Storage::disk('public')->exists($testimoni->image)) {
                Storage::disk('public')->delete($testimoni->image);
            }
        });
    }
}
