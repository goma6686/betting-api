<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\TransactionRepository;
use App\Repositories\Interfaces\PersonalAccessTokenRepositoryInterface;
use App\Repositories\PersonalAccessTokenRepostory;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->registerTransactionRepository();
        $this->registerTokenRepository();
        $this->registerUserRepository();
    }

    public function registerTransactionRepository()
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
    }

    public function registerTokenRepository()
    {
        $this->app->bind(PersonalAccessTokenRepositoryInterface::class, PersonalAccessTokenRepostory::class);
    }

    public function registerUserRepository()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
