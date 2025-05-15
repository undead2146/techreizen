<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $resetUrl;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  string|null  $password
     * @param  string|null  $resetUrl
     * @return void
     */
    public function __construct($user, $password = null, $resetUrl = null)
    {
        $this->user = $user;
        $this->password = $password;
        $this->resetUrl = $resetUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('TechReizen - Wachtwoord Herstel')
                    ->view('emails.password-reset');
    }
}
