<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $branch_name;

    public function __construct($branch_name)
    {
        $this->branch_name = $branch_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $branch_name = $this->branch_name;
        return $this->view('email-templates.customer-email-welcome', ['branch_name' => $branch_name]);
    }
}
