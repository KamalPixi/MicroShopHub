<?php

namespace App\Events;

use App\Models\LiveChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveChatMessageCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public string $sessionToken;
    public array $message;

    public function __construct(LiveChatMessage $message)
    {
        $message->loadMissing('session');

        $this->sessionToken = (string) $message->session?->session_token;
        $this->message = [
            'id' => $message->id,
            'sender' => $message->sender,
            'message' => $message->message,
            'meta' => $message->meta,
            'created_at' => $message->created_at?->format('H:i'),
        ];
    }

    public function broadcastOn(): Channel
    {
        return new Channel('live-chat.'.$this->sessionToken);
    }

    public function broadcastAs(): string
    {
        return 'live-chat.message';
    }
}
