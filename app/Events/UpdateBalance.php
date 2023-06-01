<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateBalance implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $balance;

    public function __construct($balance)
    {
        $this->balance = $balance;
    }

    public function broadcastOn()
    {
        return 'Balance';
    }

    public function broadcastWith()
    {
        return [
            'balance' => $this->balance
        ];
    }
}
