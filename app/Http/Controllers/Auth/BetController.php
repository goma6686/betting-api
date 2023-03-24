<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class BetController extends Controller
{
    public function issuetoken (Request $request){
        $request->validate([
            'username' => 'required|string|max:25|unique:users',
            'password' => 'required|string|min:3',
        ]);
     
        $user = User::where('username', $request->username)->first();
     
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }
     
        return dd($user->createToken($request->device_name)->plainTextToken);
    }
}
