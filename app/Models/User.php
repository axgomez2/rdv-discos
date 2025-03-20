<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'cpf',
        'role',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'integer',
        ];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Itens na wishlist do usuário (favoritos)
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Itens na wantlist do usuário (itens desejados que não estão em estoque)
     */
    public function wantlist()
    {
        return $this->hasMany(Wantlist::class);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
    public function paymentMethods()
    {
        return $this->belongsToMany(PaymentMethod::class, 'user_payment_methods')
                    ->withPivot('provider', 'account_number', 'expiration_date', 'pix_key_type', 'pix_key', 'is_default')
                    ->withTimestamps();
    }

    public function defaultPaymentMethod()
    {
        return $this->paymentMethods()->wherePivot('is_default', true)->first();
    }

    /**
     * Pedidos do usuário
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
