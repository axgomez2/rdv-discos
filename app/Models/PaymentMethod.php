<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_payment_methods')
                    ->withPivot('provider', 'account_number', 'expiration_date', 'pix_key_type', 'pix_key', 'is_default')
                    ->withTimestamps();
    }
}
