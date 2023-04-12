<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // to access auth services
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
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

        $this->issuetoken($user);

        Auth::login($user);

        return redirect('/');
    }

    public function issuetoken (User $user){
        return $user->createToken('token')->plainTextToken;
    }
}
