<?php

namespace App\Listeners;

use App\Events\TaskCreateEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;
use App\Jobs\CreateTaskMailJob;


class TaskCreateListener
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
     * Handle the event.
     *
     * @param  TaskEvent  $event
     * @return void
     */
    public function handle(TaskCreateEvent $event)
    {
        Queue::later(5, new CreateTaskMailJob($event->task));
        return $event;
    }
}
