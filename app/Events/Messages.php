<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Pusher\Pusher;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class Messages implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public Message $message;

    public function __construct($message)
    {
        $this->message = $message;

    }

    public function broadcastWith() 
    {
        return 
        [
            'message_id' => $this->message->id,
            'content' => $this->message->content,
            'received_id' => $this->message->received_id,
            'emits_id' => $this->message->emits_id
        ];
    }

    public function broadcastOn()
    {
        $firstMessage = Message::where(function($query) {
            $query->where('emits_id', $this->message->emits_id)
                ->where('received_id', $this->message->received_id);
        })->orWhere(function($query) {
            $query->where('emits_id', $this->message->received_id)
                ->where('received_id', $this->message->emits_id);
        })->orderBy('id')->first();
    return [    new PrivateChannel('user.' .$firstMessage->emits_id.'-'.$firstMessage->received_id)];    }

}