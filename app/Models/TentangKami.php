<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TentangKami extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'visi',
        'misi',
        'arah_gerak',
        'video_url',
        'video_description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Auto-delete image when deleted
     */
    protected static function booted(): void
    {
        static::deleting(function ($tentang) {
            if ($tentang->image && Storage::disk('public')->exists($tentang->image)) {
                Storage::disk('public')->delete($tentang->image);
            }
        });
    }
}
