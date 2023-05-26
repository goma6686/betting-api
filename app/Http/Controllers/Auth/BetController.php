<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Traits\TokenTrait;
use App\Repositories\Interfaces\TokenRepositoryInterface;

class BetController extends Controller
{
    use TokenTrait;

    public function __construct(
        protected TokenRepositoryInterface $tokenRepository
    ) {}

    public function index (){
        return view('betgames', ['token' => $this->issue_token(Auth::user())]);
    }
}