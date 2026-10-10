<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'subtotal', 'shipping_fee', 'total',
        'payment_method', 'shipping_option', 'eta_from', 'eta_to', 'status',
        'delivery_name', 'delivery_phone', 'delivery_address',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2', 'shipping_fee' => 'decimal:2', 'total' => 'decimal:2',
            'eta_from' => 'datetime', 'eta_to' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->orderBy('occurred_at');
    }
}