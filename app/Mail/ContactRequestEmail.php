<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class ContactRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $form_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->form_data = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->form_data->email)
                    ->subject('New Message From '.$this->form_data->name)
                    ->markdown('emails.contactRequest');
    }
}
