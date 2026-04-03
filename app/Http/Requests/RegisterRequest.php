<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação.
     *
     * POLÍTICA DE SENHA FORTE (NIST SP 800-63B / OWASP):
     * Password::min(8)        → mínimo 8 caracteres
     *   ->mixedCase()         → pelo menos 1 maiúscula E 1 minúscula
     *   ->numbers()           → pelo menos 1 número
     *   ->symbols()           → pelo menos 1 caractere especial (!@#$%...)
     *   ->uncompromised()     → verifica contra o banco HaveIBeenPwned
     *                           (senhas vazadas em breaches conhecidos)
     *
     * Isso implementa a recomendação de não usar apenas comprimento mínimo,
     * exigindo complexidade e verificando senhas comprometidas.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/\S/',
            ],

            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:254',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],

            'phone' => [
                'nullable',
                'string',
                'regex:/^\(?\d{2}\)?[\s\-]?9?\d{4}[\s\-]?\d{4}$/',
                'max:20',
            ],

            'cpf' => [
                'nullable',
                'string',
                'regex:/^\d{3}\.?\d{3}\.?\d{3}\-?\d{2}$/',
                'max:14',
            ],
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     * Didáticas mas sem revelar detalhes que ajudem ataques.
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'O nome é obrigatório.',
            'name.min'          => 'O nome deve ter no mínimo 3 caracteres.',
            'name.max'          => 'O nome pode ter no máximo 100 caracteres.',
            'name.regex'        => 'O nome não pode conter apenas espaços.',
            'email.required'    => 'O e-mail é obrigatório.',
            'email.email'       => 'Informe um e-mail válido.',
            'email.unique'      => 'Este e-mail já está cadastrado.',
            'email.max'         => 'O e-mail é muito longo.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed'=> 'A confirmação de senha não confere.',
            'phone.regex'       => 'Informe um telefone válido no formato (XX) XXXXX-XXXX.',
            'cpf.regex'         => 'Informe um CPF válido no formato XXX.XXX.XXX-XX.',
        ];
    }

    /**
     * Preparação/sanitização dos dados ANTES da validação.
     *
     * PROTEÇÃO CONTRA:
     * - Injeção de HTML/scripts no nome do usuário
     * - Espaços desnecessários que podem causar problemas de lookup
     * - E-mail com variação de caixa que burlaria unicidade em DBs case-sensitive
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // strip_tags remove qualquer tag HTML do nome
            // trim remove espaços nas extremidades
            'name'  => $this->name  ? strip_tags(trim($this->name))  : null,
            // E-mail normalizado para minúsculo (caso o DB seja case-sensitive)
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            // Remove formatação do CPF para armazenar apenas dígitos + padrão
            'cpf'   => $this->cpf   ? trim($this->cpf)               : null,
            'phone' => $this->phone ? trim($this->phone)              : null,
        ]);
    }
}
