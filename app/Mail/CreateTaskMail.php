<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreateTaskMail extends Mailable
{
    use Queueable, SerializesModels;


    protected $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function build()
    {
        return $this
            ->to($this->task->assignedto->email)
            ->subject('Task Created')->html("<html>Task Created by {$this->task->createdby->name} <html>");
    }
}
