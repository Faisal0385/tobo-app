<?php

namespace App\Observers;

use App\Jobs\SendMail;
use App\Models\Client;

class RegisterObserver
{
    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
        SendMail::dispatch();
    }

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        //
    }
}
