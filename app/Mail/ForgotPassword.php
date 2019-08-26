<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string the address to send the email */
    protected $to_address;

    /** @var string the winnings they won */
    protected $link;

    /**
     * Create a new message instance.
     *
     * @param string $to_address the address to send the email
     * @param float $winnings   the winnings they won
     * 
     * @return void
     */
    public function __construct($to_address, $link)
    {
        $this->to_address = $to_address;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->to_address)
            ->subject('Reset Password')->html("<html><a href='http://localhost:3000/reset/$this->link'>Click here to reset Password</a><html>");
    }
}
