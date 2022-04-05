<?php

namespace App\Listeners;

use App\Events\JwtHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class storeJwtHistory
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
     * @param  \App\Events\JwtHistory  $event
     * @return void
     */
    public function handle(JwtHistory $event)
    {
        $user = $event->user;
        $jwt = $event->token;
        $type = $event->type;

        $saveHistory = DB::table("jwt_tokens")->insert([
            "unique_id" => $jwt,
            "user_id" => $user->id,
            "token_title" => $type,
        ]);
        return $saveHistory;
    }
}
