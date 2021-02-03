<?php

namespace App\Listeners;

use App\Events\PremiumPurchased;
use Carbon\Carbon;

class SetPremiumForUser
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
     * Handle the premium purchased event.
     *
     * @param  PremiumPurchased  $event
     * @return void
     */
    public function handle(PremiumPurchased $event)
    {
        $user = $event->user;
        $period = $event->period;
        $date = Carbon::now()->addMonths($period);
        $user->premium = 1;
        $user->premium_expired = $date;
        $user->save();
    }
}
