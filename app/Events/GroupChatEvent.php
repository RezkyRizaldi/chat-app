<?php

namespace App\Events;

use App\Models\GroupMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public GroupMessage $groupMessage)
    {
        $this->groupMessage = $groupMessage;
        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|Channel[]|string[]|string
     */
    public function broadcastOn(): Channel|array|string
    {
        return new Channel('group-chat');
    }
}
