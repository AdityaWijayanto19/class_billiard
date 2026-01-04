<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ShiftClosing extends Model
{
    protected $fillable = [
        'shift_id',
        'closing_date',
        'start_time',
        'end_time',
        'total_orders',
        'total_revenue',
        'cash_revenue',
        'qris_revenue',
        'transfer_revenue',
        'order_summary',
        'closed_by',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'closing_date' => 'date',
        'total_orders' => 'integer',
        'total_revenue' => 'decimal:2',
        'cash_revenue' => 'decimal:2',
        'qris_revenue' => 'decimal:2',
        'transfer_revenue' => 'decimal:2',
        'order_summary' => 'array',
        'closed_at' => 'datetime',
    ];

    /**
     * Get start_time as string
     */
    public function getStartTimeAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        // Return as string time format (H:i:s)
        if (is_string($value)) {
            return $value;
        }

        return Carbon::parse($value)->format('H:i:s');
    }

    /**
     * Get end_time as string
     */
    public function getEndTimeAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        // Return as string time format (H:i:s)
        if (is_string($value)) {
            return $value;
        }

        return Carbon::parse($value)->format('H:i:s');
    }

    /**
     * Set start_time - ensure it's stored as time string
     */
    public function setStartTimeAttribute($value)
    {
        if ($value instanceof Carbon) {
            $this->attributes['start_time'] = $value->format('H:i:s');
        } elseif (is_string($value)) {
            // Ensure format is H:i:s
            $timeParts = explode(':', $value);
            if (count($timeParts) == 2) {
                $value = $value.':00';
            }
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
            // Ensure format is H:i:s
            $timeParts = explode(':', $value);
            if (count($timeParts) == 2) {
                $value = $value.':00';
            }
            $this->attributes['end_time'] = $value;
        }
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
