<?php

namespace App\Providers;

use App\Providers\JwtHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreJwtHistory
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\JwtHistory  $event
     * @return void
     */
    public function handle(JwtHistory $event)
    {
        //
    }
}
