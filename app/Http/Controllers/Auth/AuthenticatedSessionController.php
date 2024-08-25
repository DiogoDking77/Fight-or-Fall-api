<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        // Se precisar usar sessões na web mas não na API, descomente a linha abaixo apenas em rotas web
        // $request->session()->regenerate();

        // Retorne uma resposta JSON para a API
        return response()->json([
            'message' => 'Login successful',
            'user' => Auth::user(),
            // Se estiver usando tokens para autenticação, você pode gerar e retornar aqui:
            // 'token' => $request->user()->createToken('API Token')->plainTextToken
        ]);
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
