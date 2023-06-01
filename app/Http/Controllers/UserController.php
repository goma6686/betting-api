<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function updateBalance(Request $request)
    {
        $request->validate([
            'balance' => 'numeric|between:0.0,5000.0',
        ]);

        $this->userRepository->manualUserBalance(Auth::id(), $request->balance);

        return redirect()->back();
    }
}
