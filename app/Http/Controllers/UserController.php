<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\PersonalAccessTokenRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\UserRepositoryInterface;
class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected PersonalAccessTokenRepositoryInterface $personalAccessTokenRepository
    ) {}

    public function update_balance(Request $request)
    {
        $request->validate([
            'balance' => 'numeric|between:0.0,50000.99',
        ]);

        $this->userRepository->updateBalance(Auth::id(), $request->balance);
        
        return redirect()->back();
    }

    public function issue_token($id){
        return $this->personalAccessTokenRepository->issueToken($this->userRepository->getUserById($id));
    }
}
