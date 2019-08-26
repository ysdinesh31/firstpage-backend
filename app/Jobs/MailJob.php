<?php

namespace App\Jobs;

use App\Mail\ForgotPassword as ResetMail;
use Illuminate\Support\Facades\Mail;

class MailJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $emailid;
    protected $token;

    public function __construct($emailid, $token)
    {
        $this->emailid = $emailid;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Mail::send(new ResetMail($this->emailid, $this->token));
    }
}
