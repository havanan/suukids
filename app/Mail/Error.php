<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Error extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var string
     */
    private $message;
    /**
     * @var string
     */
    private $trace;

    /**
     * Create a new message instance.
     *
     * @param string $message
     * @param string $trace
     */
    public function __construct(string $message, string $trace)
    {
        $this->message = $message;
        $this->trace = $trace;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.error')
            ->subject(config('app.name') . ': ' . $this->message)
            ->with([
            'message' => $this->message,
            'trace' => $this->trace,
        ]);
    }
}
