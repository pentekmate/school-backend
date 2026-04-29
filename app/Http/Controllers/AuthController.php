<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Hibás adatok!',
            ], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'user' => Auth::user(),
            'message' => 'Sikeres bejelentkezés!',
        ]);
    }

    public function logout(Request $request)
    {
        // 1. Kijelentkeztetjük a felhasználót a Guard segítségével
        Auth::guard('web')->logout();

        // 2. Érvénytelenítjük a jelenlegi session-t
        $request->session()->invalidate();

        // 3. Újrageneráljuk a CSRF tokent a biztonság érdekében
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Sikeres kijelentkezés',
        ]);
    }
}
