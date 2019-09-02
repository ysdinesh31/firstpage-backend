<?php

namespace App\Jobs;

use App\Mail\CreateTaskMail;
use Illuminate\Support\Facades\Mail;

class CreateTaskMailJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Mail::send(new CreateTaskMail($this->task));
    }
}
