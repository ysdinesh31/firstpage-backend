<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class TaskCreateEvent implements ShouldBroadcast
{
    use SerializesModels, InteractsWithSockets;
    public $task;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($task)
    {
        $this->task = $task;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("TaskCreateChannel." . $this->task->assignedto->id);
    }
}
