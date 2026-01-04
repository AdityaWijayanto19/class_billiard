<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'background_image',
        'logo_image',
        'title',
        'subtitle',
        'tagline',
        'cta_text_1',
        'cta_link_1',
        'cta_text_2',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get background image URL with storage path
     */
    public function getBackgroundImageUrlAttribute(): string
    {
        return $this->background_image ? asset('storage/' . $this->background_image) : '';
    }

    /**
     * Get logo image URL with storage path
     */
    public function getLogoImageUrlAttribute(): string
    {
        return $this->logo_image ? asset('storage/' . $this->logo_image) : '';
    }

    /**
     * Auto-delete images when hero section is deleted
     */
    protected static function booted(): void
    {
        static::deleting(function ($hero) {
            if ($hero->background_image && Storage::disk('public')->exists($hero->background_image)) {
                Storage::disk('public')->delete($hero->background_image);
            }
            if ($hero->logo_image && Storage::disk('public')->exists($hero->logo_image)) {
                Storage::disk('public')->delete($hero->logo_image);
            }
        });
    }
}
