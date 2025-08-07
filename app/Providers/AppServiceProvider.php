<?php

namespace App\Providers;

use App\Events\MoneyDepositedEvent;
use App\Events\MoneyTransferredEvent;
use App\Events\UserUpdatedEvent;
use App\Listeners\LogTransactionListener;
use App\Listeners\SendNotificationListener;
use App\Repositories\AccountRepositoryInterface;
use App\Repositories\Eloquent\AccountRepository;
use App\Repositories\Eloquent\TransactionRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\TransactionRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register event listeners
        Event::listen(UserUpdatedEvent::class, [LogTransactionListener::class, 'handleUserUpdated']);
        Event::listen(UserUpdatedEvent::class, [SendNotificationListener::class, 'handleUserUpdated']);

        Event::listen(MoneyDepositedEvent::class, [LogTransactionListener::class, 'handleMoneyDeposited']);
        Event::listen(MoneyDepositedEvent::class, [SendNotificationListener::class, 'handleMoneyDeposited']);

        Event::listen(MoneyTransferredEvent::class, [LogTransactionListener::class, 'handleMoneyTransferred']);
        Event::listen(MoneyTransferredEvent::class, [SendNotificationListener::class, 'handleMoneyTransferred']);
    }
}
