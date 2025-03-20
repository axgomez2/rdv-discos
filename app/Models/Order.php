<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'total',
        'shipping_cost',
        'tax',
        'status',
        'payment_method',
        'payment_status',
        'transaction_code',
        'payment_details',
        'shipping_address_id',
        'billing_address_id',
        'notes',
        'shipping_label',
        'tracking_code',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'payment_details' => 'array',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the shipping address for the order.
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /**
     * Get the billing address for the order.
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /**
     * Get the payment method for the order.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Retorna uma descrição formatada do status do pedido
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pendente',
            'processing' => 'Em processamento',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
        ];

        return $labels[$this->status] ?? 'Desconhecido';
    }

    /**
     * Retorna uma descrição formatada do status do pagamento
     */
    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Aguardando pagamento',
            'analyzing' => 'Em análise',
            'paid' => 'Pago',
            'available' => 'Disponível',
            'disputed' => 'Em disputa',
            'refunded' => 'Devolvido',
            'cancelled' => 'Cancelado',
            'debited' => 'Debitado',
            'withheld' => 'Retido temporariamente',
        ];

        return $labels[$this->payment_status] ?? 'Desconhecido';
    }

    /**
     * Retorna uma descrição formatada do método de pagamento
     */
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'credit_card' => 'Cartão de Crédito',
            'boleto' => 'Boleto Bancário',
            'pix' => 'PIX',
        ];

        return $labels[$this->payment_method] ?? 'Desconhecido';
    }
}
