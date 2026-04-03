<?php

/**
 * bootstrap/app.php — Ponto de inicialização da aplicação (Laravel 11+)
 *
 * SEGURANÇA:
 * Este arquivo registra os middlewares globais e de grupo.
 * A ordem dos middlewares importa:
 *   1. SecurityHeaders deve ser um dos primeiros — injeta headers em todas as respostas
 *   2. VerifyCsrfToken é registrado automaticamente no grupo 'web'
 *   3. SessionTimeout é aplicado apenas nas rotas autenticadas (via grupo nas rotas)
 */

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SessionTimeout;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ─────────────────────────────────────────────────────────────
        // MIDDLEWARE GLOBAL — Executado em TODA requisição HTTP
        // SecurityHeaders injeta os cabeçalhos de segurança em todas
        // as respostas, incluindo erros 404, 500, etc.
        // ─────────────────────────────────────────────────────────────
        $middleware->append(SecurityHeaders::class);

        // ─────────────────────────────────────────────────────────────
        // ALIASES DE MIDDLEWARE
        // Nomes curtos usados nas rotas (ex.: Route::middleware('session.timeout'))
        // ─────────────────────────────────────────────────────────────
        $middleware->alias([
            'session.timeout' => SessionTimeout::class,
        ]);

        // ─────────────────────────────────────────────────────────────
        // CONFIGURAÇÃO DO GRUPO 'web'
        // O grupo 'web' já inclui por padrão:
        //   - EncryptCookies          → criptografa cookies
        //   - AddQueuedCookiesToResponse
        //   - StartSession            → inicializa a sessão
        //   - ShareErrorsFromSession  → compartilha erros de validação com as views
        //   - VerifyCsrfToken         → CSRF Protection (valida @csrf em formulários)
        //   - SubstituteBindings      → route model binding
        // ─────────────────────────────────────────────────────────────

        // ─────────────────────────────────────────────────────────────
        // CONFIGURAÇÃO DO COOKIE DE SESSÃO (reforço de segurança)
        // Estas configurações complementam as do .env e config/session.php
        // ─────────────────────────────────────────────────────────────
        $middleware->encryptCookies(except: []); // Criptografa TODOS os cookies

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ─────────────────────────────────────────────────────────────
        // TRATAMENTO SEGURO DE EXCEÇÕES
        //
        // PROTEÇÃO CONTRA VAZAMENTO DE INFORMAÇÕES:
        // Em produção (APP_DEBUG=false), o Laravel retorna páginas genéricas
        // de erro sem stack trace, variáveis de ambiente ou detalhes internos.
        //
        // Aqui, customizamos respostas de erro para não vazar informações.
        // ─────────────────────────────────────────────────────────────

        // Personaliza resposta de autenticação (401) — sem revelar estrutura
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Não autenticado.'], 401);
            }
            return redirect()->route('login');
        });

        // Personaliza resposta de autorização (403)
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Acesso não autorizado.'], 403);
            }
            abort(403, 'Acesso não autorizado.');
        });

        // Throttle (429) — muitas requisições
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Muitas requisições. Aguarde antes de tentar novamente.',
                ], 429);
            }
            return back()->withErrors(['email' => 'Muitas tentativas. Aguarde antes de tentar novamente.']);
        });

    })
    ->create();
