<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'package_id',
        'external_reference',
        'status',
        'frequency',
        'start_date',
        'end_date',
        'cancelled_at',
        'last_charged_at',
        'next_charge_date',
        'price',
        'payment_method',
        'mercadopago_subscription_id',
        'mercadopago_preapproval_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_charged_at' => 'datetime',
        'next_charge_date' => 'datetime',
        'price' => 'decimal:2'
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package for the subscription.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPackage::class, 'package_id');
    }

    /**
     * Get the shipments for the subscription.
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(SubscriptionShipment::class);
    }

    /**
     * Verifica se a assinatura está ativa
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verifica se a assinatura está suspensa
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Verifica se a assinatura está cancelada
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled' || $this->cancelled_at !== null;
    }

    /**
     * Verifica se a assinatura falhou
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Cancela a assinatura no sistema local
     */
    public function cancel(): bool
    {
        try {
            $this->status = 'cancelled';
            $this->cancelled_at = now();
            return $this->save();
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar assinatura localmente', [
                'subscription_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Reativa uma assinatura cancelada
     */
    public function reactivate(): bool
    {
        try {
            if (!$this->isCancelled()) {
                throw new \Exception('Apenas assinaturas canceladas podem ser reativadas');
            }

            $this->status = 'active';
            $this->cancelled_at = null;
            return $this->save();
        } catch (\Exception $e) {
            Log::error('Erro ao reativar assinatura', [
                'subscription_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Retorna a data da próxima cobrança
     */
    public function getNextBillingDateAttribute()
    {
        if ($this->last_charged_at) {
            return $this->last_charged_at->addDays(30); // Assumindo assinatura mensal
        } else if ($this->start_date) {
            return $this->start_date->addDays(30);
        }
        
        return null;
    }

    /**
     * Retorna o status formatado para exibição
     */
    public function getFormattedStatusAttribute(): string
    {
        $statusMap = [
            'active' => 'Ativa',
            'pending' => 'Pendente',
            'suspended' => 'Suspensa',
            'cancelled' => 'Cancelada',
            'failed' => 'Falhou'
        ];
        
        return $statusMap[$this->status] ?? ucfirst($this->status);
    }
}
