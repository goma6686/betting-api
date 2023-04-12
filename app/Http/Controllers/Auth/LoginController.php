<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // to access auth services
use Illuminate\Http\RedirectResponse;
use App\Models\User;

class LoginController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string|max:25',
            'password' => 'required|string|min:3',
        ]);
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user() instanceof \App\Models\User) {
                $this->issuetoken(Auth::user());
            }

            return redirect('/');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function issuetoken (User $user){
        return $user->createToken('token')->plainTextToken;
    }
}