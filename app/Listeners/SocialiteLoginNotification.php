<?php

namespace App\Listeners;

use App\Events\SocialiteLogin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SocialiteLoginNotification
{
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SocialiteLogin $event): void
    {
        //
    }
}
