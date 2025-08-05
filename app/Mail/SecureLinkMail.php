<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecureLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $user;
    public $secureUrl;
    public $email;

    public function __construct($user, $secureUrl, $email)
    {
        $this->user = $user;
        $this->secureUrl = $secureUrl;
        $this->email = $email;
    }

    public function build()
    {
        return $this->subject('Your Secure Access Link')
                    ->view('emails.secure-link') // ✅ points to correct email view
                    ->with([
                        'user' => $this->user,
                        'secureUrl' => $this->secureUrl,
                        'email' => $this->email,
                    ]);
    }


}
