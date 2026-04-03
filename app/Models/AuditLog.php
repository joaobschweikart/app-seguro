<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',      // null para tentativas de login com e-mail inexistente
        'action',       // ex.: 'login_success', 'login_failed', 'profile_updated'
        'ip_address',   // IPv4 ou IPv6 do cliente
        'user_agent',   // navegador/cliente HTTP
        'metadata',     // dados adicionais em JSON (ex.: e-mail tentado)
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata'   => 'array',
            'created_at' => 'datetime',
        ];
    }

    public const ACTION_LOGIN_SUCCESS      = 'login_success';
    public const ACTION_LOGIN_FAILED       = 'login_failed';
    public const ACTION_LOGOUT             = 'logout';
    public const ACTION_REGISTER           = 'register';
    public const ACTION_PROFILE_UPDATED    = 'profile_updated';
    public const ACTION_PASSWORD_CHANGED   = 'password_changed';
    public const ACTION_SESSION_EXPIRED    = 'session_expired';

    public static function record(
        string  $action,
        ?int    $userId = null,
        array   $metadata = [],
        ?string $ip = null,
        ?string $userAgent = null,
    ): self {
        return self::create([
            'action'     => $action,
            'user_id'    => $userId,
            'ip_address' => $ip ?? request()->ip(),
            'user_agent' => mb_substr($userAgent ?? request()->userAgent() ?? '', 0, 500),
            'metadata'   => $metadata,
        ]);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
