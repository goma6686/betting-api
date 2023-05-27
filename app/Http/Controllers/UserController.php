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

    public function get_balance() {

        return $this->userRepository->getUserBalance(Auth::user());
    }

    public function update_balance(Request $request)
    {
        $request->validate([
            'balance' => 'numeric|between:0.0,50000.99',
        ]);
        $this->userRepository->manualUserBalance(Auth::id(), $request->balance);

        return redirect()->back();
    }
}
