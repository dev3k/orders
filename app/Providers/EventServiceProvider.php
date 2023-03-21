<?php

namespace App\Providers;

use App\Events\Order\OrderReceived;
use App\Events\Stock\LowStock;
use App\Listeners\Order\UpdateStockListener;
use App\Listeners\Stock\SendLowStockEmail;
use App\Models\Ingredient;
use App\Observers\IngredientObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderReceived::class => [
            UpdateStockListener::class,
        ],
        LowStock::class => [
            SendLowStockEmail::class,
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        Ingredient::class => [IngredientObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
