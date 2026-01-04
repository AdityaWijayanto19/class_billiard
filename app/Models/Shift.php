<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get start_time as Carbon instance
     */
    public function getStartTimeAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        // If already a Carbon instance, return as is
        if ($value instanceof Carbon) {
            return $value;
        }
        // Parse string time format (H:i:s or H:i)
        // Handle both formats
        if (is_string($value)) {
            // Ensure we have seconds
            $timeParts = explode(':', $value);
            if (count($timeParts) == 2) {
                $value = $value.':00';
            }

            return Carbon::createFromTimeString($value);
        }

        return null;
    }

    /**
     * Get end_time as Carbon instance
     */
    public function getEndTimeAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        // If already a Carbon instance, return as is
        if ($value instanceof Carbon) {
            return $value;
        }
        // Parse string time format (H:i:s or H:i)
        // Handle both formats
        if (is_string($value)) {
            // Ensure we have seconds
            $timeParts = explode(':', $value);
            if (count($timeParts) == 2) {
                $value = $value.':00';
            }

            return Carbon::createFromTimeString($value);
        }

        return null;
    }

    /**
     * Set start_time - ensure it's stored as time string
     */
    public function setStartTimeAttribute($value)
    {
        if ($value instanceof Carbon) {
            $this->attributes['start_time'] = $value->format('H:i:s');
        } elseif (is_string($value)) {
            $this->attributes['start_time'] = $value;
        }
    }

    /**
     * Set end_time - ensure it's stored as time string
     */
    public function setEndTimeAttribute($value)
    {
        if ($value instanceof Carbon) {
            $this->attributes['end_time'] = $value->format('H:i:s');
        } elseif (is_string($value)) {
            $this->attributes['end_time'] = $value;
        }
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(orders::class);
    }

    /**
     * Get the active shift based on current time
     */
    public static function getActiveShift()
    {
        $now = Carbon::now('Asia/Jakarta');
        $currentHour = $now->hour;
        $currentMinute = $now->minute;
        $currentTimeInMinutes = ($currentHour * 60) + $currentMinute;

        // Shift 1: 10:00 - 18:00
        // Shift 2: 18:00 - 00:00 (next day)

        $shift1 = self::where('name', 'Shift 1')->where('is_active', true)->first();
        $shift2 = self::where('name', 'Shift 2')->where('is_active', true)->first();

        if ($shift1 && $shift2) {
            // Get time values - accessors will return Carbon instances
            $shift1Start = $shift1->start_time;
            $shift1End = $shift1->end_time;
            $shift2Start = $shift2->start_time;
            $shift2End = $shift2->end_time;

            // Calculate minutes from midnight
            $shift1StartMinutes = ($shift1Start->hour * 60) + $shift1Start->minute;
            $shift1EndMinutes = ($shift1End->hour * 60) + $shift1End->minute;
            $shift2StartMinutes = ($shift2Start->hour * 60) + $shift2Start->minute;
            $shift2EndMinutes = ($shift2End->hour * 60) + $shift2End->minute;

            // Check if current time is in Shift 1 (10:00 - 18:00)
            if ($currentTimeInMinutes >= $shift1StartMinutes && $currentTimeInMinutes < $shift1EndMinutes) {
                return $shift1;
            }

            // Check if current time is in Shift 2 (18:00 - 00:00)
            // Shift 2 spans midnight, so check if time >= 17:00 or < 00:00
            if ($currentTimeInMinutes >= $shift2StartMinutes || $currentTimeInMinutes < $shift2EndMinutes) {
                return $shift2;
            }
        }

        return null;
    }

    /**
     * Get the next shift after the current active shift
     */
    public static function getNextShift()
    {
        $activeShift = self::getActiveShift();

        if (! $activeShift) {
            // If no active shift, return the first active shift
            return self::where('is_active', true)->orderBy('start_time')->first();
        }

        // Get all active shifts
        $shifts = self::where('is_active', true)->orderBy('start_time')->get();

        if ($shifts->count() < 2) {
            // If only one shift, return it (circular)
            return $activeShift;
        }

        // Find the next shift
        $currentIndex = -1;
        foreach ($shifts as $index => $shift) {
            if ($shift->id === $activeShift->id) {
                $currentIndex = $index;
                break;
            }
        }

        if ($currentIndex === -1) {
            return $shifts->first();
        }

        // Get next shift (circular)
        $nextIndex = ($currentIndex + 1) % $shifts->count();

        return $shifts[$nextIndex];
    }
}
