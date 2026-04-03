<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'cpf',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'cpf',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'created_at'        => 'datetime',
            'updated_at'        => 'datetime',
        ];
    }

    public function setCpfAttribute(?string $value): void
    {
        $this->attributes['cpf'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getCpfAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Não expõe erro ao usuário — registra internamente
            logger()->warning('Falha ao descriptografar CPF', ['user_id' => $this->id]);
            return null;
        }
    }

    public function setPhoneAttribute(?string $value): void
    {
        $this->attributes['phone'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getPhoneAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            logger()->warning('Falha ao descriptografar telefone', ['user_id' => $this->id]);
            return null;
        }
    }

    public function auditLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
