<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_code',
        'customer_id',
        'customer_name',
        'vendor_name',
        'delivery_location',
        'shipping_address',
        'delivery_date',
        'time_slot',
        'delivery_method',
        'items',
        'subtotal',
        'delivery_fee',
        'total',
        'payment_method',
        'payment_status',
        'notes',
        'vendor_note',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'items' => 'array',
        'delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the display name for the customer (handles walk-in orders).
     */
    public function getCustomerDisplayNameAttribute(): string
    {
        if ($this->customer) {
            return $this->customer->name;
        }
        return $this->customer_name ?? 'Walk-in';
    }

    /**
     * Scope to filter orders for a specific vendor.
     */
    public function scopeForVendor(Builder $query, string $vendorName): Builder
    {
        return $query->where('vendor_name', $vendorName);
    }

    /**
     * Generate a unique order code.
     */
    public static function generateOrderCode(): string
    {
        $latest = static::orderBy('id', 'desc')->first();
        $nextNumber = $latest ? intval(substr($latest->order_code, 4)) + 1 : 2401;
        return 'BOM-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
