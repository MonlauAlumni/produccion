<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SidebarUpdated implements ShouldBroadcast
{
    public function __construct(public array $data) {}

    public function broadcastOn(): Channel
    {
        return new Channel('sidebar');
    }

    public function broadcastAs(): string
    {
        return 'SidebarUpdated';
    }
}
