<?php

namespace App\Listeners\Stock;

use App\Events\Stock\LowStock;
use App\Mail\LowStockMail;
use Illuminate\Support\Facades\Mail;

class SendLowStockEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LowStock $event): void
    {
        Mail::to('notifications@exampl.com')
            ->send(
                new LowStockMail($event->ingredient)
            );
    }
}
