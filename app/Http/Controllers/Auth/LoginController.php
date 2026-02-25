<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
     function show()
    {
        return view('auth.login', ['usuario' => (object)['id' => '']]);
    }

     function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
        return redirect()->route('venta')->with('success', 'Bienvenido de nuevo!');
    }
        return back()->withErrors([
            'email' => 'Contraseña incorrecta.',
        ])->onlyInput('email');
    }

     function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Sesión cerrada correctamente.');
    }
}
