<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InformeBEChart extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filename = "\informerBE_";
        $file_path = 'C:\inetpub\certilapReport\public\pdf_temp' . $filename . '.pdf';
        echo "enviar correo";
        return $this->view('emails.informeBE')->attach($file_path);
    }
}
