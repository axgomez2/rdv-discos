<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionShipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'address_id',
        'shipping_cost',
        'status',
        'shipping_date',
        'shipping_label_url',
        'tracking_code',
    ];

    protected $casts = [
        'shipping_date' => 'datetime',
        'shipping_cost' => 'decimal:2',
    ];

    /**
     * Get the subscription that owns the shipment.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the address for this shipment.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the formatted shipping cost
     */
    public function getFormattedShippingCostAttribute(): string
    {
        return 'R$ ' . number_format($this->shipping_cost, 2, ',', '.');
    }

    /**
     * Check if the shipment has a tracking code
     */
    public function hasTrackingCode(): bool
    {
        return !empty($this->tracking_code);
    }

    /**
     * Check if the shipment has a shipping label
     */
    public function hasShippingLabel(): bool
    {
        return !empty($this->shipping_label_url);
    }

    /**
     * Check if the shipment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the shipment is in processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if the shipment is shipped
     */
    public function isShipped(): bool
    {
        return $this->status === 'shipped';
    }

    /**
     * Check if the shipment is delivered
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }
}
