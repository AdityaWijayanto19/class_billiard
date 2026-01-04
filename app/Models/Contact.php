<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'location_name',
        'address',
        'phone',
        'email',
        'whatsapp',
        'navbar_label',
        'navbar_link',
        'google_maps_url',
        'map_url',
        'opening_hours',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
