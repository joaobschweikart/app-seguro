<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        // Auth::user() garante que só o usuário da sessão acessa seus próprios dados
        return view('profile.edit', ['user' => Auth::user()]);
    }

    /**
     * Atualiza o perfil do usuário autenticado.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user      = Auth::user();
        $validated = $request->validated();
        $changes   = [];

        if (isset($validated['name']) && $validated['name'] !== $user->name) {
            $changes[]  = 'name';
            $user->name = $validated['name'];
        }

        if (isset($validated['phone'])) {
            $changes[]   = 'phone';
            $user->phone = $validated['phone'];
        }

        if (! empty($validated['new_password'])) {
            if (! Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors([
                    'current_password' => 'A senha atual informada está incorreta.',
                ]);
            }

            $user->password = Hash::make($validated['new_password']);
            $changes[]      = 'password';

            // Log específico para troca de senha (evento crítico)
            AuditLog::record(
                action: AuditLog::ACTION_PASSWORD_CHANGED,
                userId: $user->id,
            );
        }

        $user->save();

        if (! empty($changes)) {
            AuditLog::record(
                action:   AuditLog::ACTION_PROFILE_UPDATED,
                userId:   $user->id,
                metadata: ['fields_changed' => $changes],
            );
        }

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }
}
