<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserPaymentMethod extends Pivot
{
    protected $table = 'user_payment_methods';

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'provider',
        'account_number',
        'expiration_date',
        'pix_key_type',
        'pix_key',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function setAsDefault()
    {
        $this->user->paymentMethods()->updateExistingPivot($this->payment_method_id, ['is_default' => false]);
        $this->update(['is_default' => true]);
    }
}

