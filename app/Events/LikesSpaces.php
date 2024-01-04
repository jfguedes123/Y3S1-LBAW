<?php

namespace App\Events;

use App\Models\LikeSpace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Pusher\Pusher;
use App\Models\Space;

class LikesSpaces implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $space_id; 
    public $message; 

    public function __construct($space_id)
    {
        $this->space_id = $space_id; 
        $this->message = 'You liked the post';  
    }
    public function broadcastOn()
    {
        $owner = Space::findOrFail($this->space_id)->user_id;
        $id = Auth::user()->id;
        return [
            new PrivateChannel('lbaw2372.' .$owner),
            new PrivateChannel('lbaw2372.' .$id)
        ];
    }

    public function broadcastAs() 
    {
        return 'notification-spaceLike';
    }

    public function broadcastWith()
    {
    return [
        'space_id' => $this->space_id,
        'message' => $this->message,
    ];
    }
}
