<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
    public function store(LoginRequest $request): RedirectResponse
    {
        // Bejelentkezünk az API-ba
        $response = Http::api()->post('/user/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Ha sikeres választ kaptunk, akkor elmentjük az adatokat a session-be
        if ($response->successful()) {
            // elmentjük a bejelentkezési adatokat a session-be.
            $responseBody = json_decode($response->body());
            if (empty($responseBody->data)) {
                return back()->withErrors([
                    'message' => $responseBody->message ?? 'Hiba történt a bejelentkezés során.',
                ]);
            }
            // az, hogy a token és a többi milyen formában van a response-ban
            // az API programozójától függ, pl: "data" tömbön belül
            session([
                'api_token' => $responseBody->data->token,
                'user_name' => $responseBody->data->name,
                'user_email' => $responseBody->data->email,
            ]);

            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Hibás bejelentkezési adatok.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        session()->forget('api_token');
        session()->forget('user_name');
        session()->forget('user_email');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
