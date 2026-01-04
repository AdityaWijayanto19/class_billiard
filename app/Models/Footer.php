<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    use HasFactory;

    protected $fillable = [
        'about_text',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'whatsapp',
        'address',
        'location_name',
        'phone',
        'email',
        'google_maps_url',
        'map_url',
        'monday_friday_hours',
        'saturday_sunday_hours',
        'opening_hours',
        'copyright',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
