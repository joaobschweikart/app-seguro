<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email', 'max:254'],
            'password' => ['required', 'string', 'min:1'],
        ];
    }

    public function passedValidation(): void
    {
        $this->checkThrottle();
    }

    /**
     * Verifica se este IP+email atingiu o limite de tentativas.
     *
     * Configuração:
     *   - Máximo: 5 tentativas
     *   - Janela: 60 segundos (1 minuto)
     *
     * Quando bloqueado:
     *   - Dispara evento Lockout (pode enviar e-mail de alerta)
     *   - Lança ValidationException com mensagem genérica + tempo de espera
     *   - Não informa se o e-mail existe (anti-enumeração)
     *
     * @throws ValidationException
     */
    public function checkThrottle(): void
    {
        $key = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($key, maxAttempts: 5)) {
            event(new Lockout($this));

            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => __('Muitas tentativas de login. Tente novamente em :seconds segundo(s).', [
                    'seconds' => $seconds,
                ]),
            ]);
        }

        RateLimiter::hit($key, decaySeconds: 60);
    }

    public function clearThrottleAttempts(): void
    {
        RateLimiter::clear($this->throttleKey());
    }

    public function incrementThrottleAttempts(): void
    {
        RateLimiter::hit($this->throttleKey(), decaySeconds: 60);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('email')) . '|' . $this->ip()
        );
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->email ? strtolower(trim($this->email)) : null,
        ]);
    }
}
