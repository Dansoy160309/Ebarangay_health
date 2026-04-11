<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DefaulterRecallMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public string $mailSubject,
        public string $body,
        public string $recipientEmail
    ) {
    }

    public function build()
    {
        return $this->subject($this->mailSubject)
                    ->view('emails.defaulter-recall')
                    ->with([
                        'body' => $this->body,
                    ]);
    }
}
