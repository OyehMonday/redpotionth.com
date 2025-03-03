<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationToken;

    // Accept verificationToken when creating the email
    public function __construct($verificationToken)
    {
        $this->verificationToken = $verificationToken;
    }

    // Build the email with subject and view
    public function build()
    {
        return $this->subject('Admin Account Verification')
                    ->view('emails.admin_verification');  // Specify the email view
    }
}