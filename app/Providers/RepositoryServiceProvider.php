<?php

namespace App\Providers;

use App\Repositories\Contracts\TransactionRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\WalletRepository;
use App\Repositories\Eloquent\TransactionRepositoryFromEloquent;
use App\Repositories\Eloquent\UserRepositoryFromEloquent;
use App\Repositories\Eloquent\WalletRepositoryFromDoctrine;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            UserRepository::class,
            UserRepositoryFromEloquent::class
        );

        $this->app->bind(
            TransactionRepository::class,
            TransactionRepositoryFromEloquent::class
        );

        $this->app->bind(
            WalletRepository::class,
            WalletRepositoryFromDoctrine::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
