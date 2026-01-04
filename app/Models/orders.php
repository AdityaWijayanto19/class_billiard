<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Model Order
 *
 * PENTING: Order dengan status completed, processing, atau rejected TIDAK BOLEH DIHAPUS
 * untuk mencegah kecurangan saat tutup buku akhir bulan/tahun.
 * Data penjualan harus tersimpan permanen untuk keperluan audit dan laporan keuangan.
 */
class orders extends Model
{
    protected $fillable = [
        'customer_name',
        'table_number',
        'room',
        'total_price',
        'payment_method',
        'status',
        'shift_id',
        'idempotency_key',
    ];

    public function orderItems()
    {
        return $this->hasMany(order_items::class, 'order_id')->cascadeOnDelete();
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Cek apakah order bisa dihapus
     * Hanya order dengan status pending yang bisa dihapus
     */
    public function canBeDeleted(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Auto-delete order items files when order is deleted
     * Cascade delete akan handle penghapusan order_items di database
     */
    protected static function booted(): void
    {
        static::deleting(function ($model) {
            // Clean up order items images before cascade delete
            $model->orderItems()->get()->each(function ($item) {
                if ($item->image && Storage::disk('public')->exists($item->image)) {
                    Storage::disk('public')->delete($item->image);
                }
            });
        });
    }
}
