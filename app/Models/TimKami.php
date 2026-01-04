<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TimKami extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'name',
        'position',
        'bio',
        'photo',
        'image',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Auto-delete images when deleted
     */
    protected static function booted(): void
    {
        static::deleting(function ($tim) {
            if ($tim->photo && Storage::disk('public')->exists($tim->photo)) {
                Storage::disk('public')->delete($tim->photo);
            }
            if ($tim->image && Storage::disk('public')->exists($tim->image)) {
                Storage::disk('public')->delete($tim->image);
            }
        });
    }
}
