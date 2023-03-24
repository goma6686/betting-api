<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // to access auth services
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class RegisterLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');//can be accessible after logged IN to the application
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:25|unique:users',
            'password' => 'required|string|min:3',

        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/');
    }

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
 
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            /*if (Auth::user() instanceof \App\Models\User) {
                return dd(Auth::user()->createToken($request->username));
            }*/

            return redirect('/');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        if ($request->user()->tokens()){
            $request->user()->tokens()->delete();
        }
        
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }    
}
