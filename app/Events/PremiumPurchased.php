<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PremiumPurchased
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $period;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param $period
     */
    public function __construct(User $user, int $period)
    {
        $this->user = $user;
        $this->period = $period;
    }
}
