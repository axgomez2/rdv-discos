<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'description',
        'is_encrypted'
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * Acessor para obter o valor descriptografado se a configuração estiver marcada como criptografada
     */
    public function getValueAttribute($value)
    {
        if ($this->is_encrypted && !empty($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                // Em caso de erro na descriptografia, retornar o valor original
                return $value;
            }
        }
        
        return $value;
    }

    /**
     * Mutator para criptografar o valor automaticamente se a configuração estiver marcada como criptografada
     */
    public function setValueAttribute($value)
    {
        if ($this->is_encrypted && !empty($value)) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Buscar uma configuração por grupo e chave
     */
    public static function getByKey($group, $key, $default = null)
    {
        $setting = self::where('group', $group)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Atualizar ou criar uma configuração
     */
    public static function updateOrCreateSetting($group, $key, $value, $description = null, $isEncrypted = false)
    {
        return self::updateOrCreate(
            ['group' => $group, 'key' => $key],
            [
                'value' => $value,
                'description' => $description,
                'is_encrypted' => $isEncrypted
            ]
        );
    }

    /**
     * Obter todas as configurações de um grupo
     */
    public static function getGroupSettings($group)
    {
        return self::where('group', $group)->get()->pluck('value', 'key')->toArray();
    }
}
