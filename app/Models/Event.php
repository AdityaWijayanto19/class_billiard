<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'event_title',
        'event_description',
        'description',
        'category',
        'event_date',
        'image',
        'link_url',
        'order',
        'is_active',
    ];

    /**
     * Auto-delete image when deleted
     */
    protected static function booted(): void
    {
        static::deleting(function ($event) {
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }
        });
    }    protected $casts = [
        'is_active' => 'boolean',
        'event_date' => 'date',
        'order' => 'integer',
    ];
}
