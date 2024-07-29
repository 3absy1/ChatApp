<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PusherBroadcast implements  ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public User $receiver;

    public string $message;

    public $attachment;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct(User $receiver, $message, $attachment = null)
    {
        $this->receiver = $receiver;
        $this->message = $message;
        $this->attachment = $attachment;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): array
    {
        return  [
            new Channel('chat'.$this->receiver->id),
        ];
    }

    public function broadcastAs(){
        return 'chatMessage';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'attachment' => $this->attachment,
        ];
    }
}
