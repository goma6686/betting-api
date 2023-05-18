<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Repositories\Interfaces\UserRepositoryInterface;

class TransactionController extends Controller
{

    public function __construct(
        protected TransactionRepositoryInterface $transactionRepository,
        protected UserRepositoryInterface $userRepository
        ) {}

    public function store(array $transactionData){

        return $this->transactionRepository->createTransaction($transactionData);
    }
}
