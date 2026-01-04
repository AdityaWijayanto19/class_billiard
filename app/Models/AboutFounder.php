<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AboutFounder extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'name',
        'position',
        'description',
        'quote',
        'signature',
        'photo',
        'image',
        'video_url',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Auto-delete images when deleted
     */
    protected static function booted(): void
    {
        static::deleting(function ($founder) {
            if ($founder->photo && Storage::disk('public')->exists($founder->photo)) {
                Storage::disk('public')->delete($founder->photo);
            }
            if ($founder->image && Storage::disk('public')->exists($founder->image)) {
                Storage::disk('public')->delete($founder->image);
            }
        });
    }
}
