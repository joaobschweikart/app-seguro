<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'name' => [
                'sometimes', 'required',
                'string', 'min:3', 'max:100',
                'regex:/\S/',
            ],

            'email' => [
                'sometimes', 'required',
                'string', 'email:rfc,dns', 'max:254',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'phone' => [
                'nullable', 'string',
                'regex:/^\(?\d{2}\)?[\s\-]?9?\d{4}[\s\-]?\d{4}$/',
                'max:20',
            ],

            'current_password' => [
                'required_with:new_password',
                'string',
            ],

            'new_password' => [
                'nullable', 'string',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required_with' => 'Informe sua senha atual para trocar a senha.',
            'new_password.confirmed'          => 'A confirmação da nova senha não confere.',
            'email.unique'                    => 'Este e-mail já está em uso por outra conta.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => $this->name  ? strip_tags(trim($this->name))  : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'phone' => $this->phone ? trim($this->phone)              : null,
        ]);
    }
}
