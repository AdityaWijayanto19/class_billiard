<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendStrukHarianEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $strukData;

    public $tanggal;

    /**
     * Create a new message instance.
     */
    public function __construct($strukData, $tanggal)
    {
        $this->strukData = $strukData;
        $this->tanggal = $tanggal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $shiftInfo = '';
        if (isset($this->strukData['shift']) && $this->strukData['shift']) {
            $shiftInfo = ' - '.$this->strukData['shift']->name;
        }

        return new Envelope(
            subject: 'Struk Tutup Hari - '.$this->tanggal->format('d M Y').$shiftInfo.' - Billiard Class',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.struk-harian',
            with: [
                'strukData' => $this->strukData,
                'tanggal' => $this->tanggal,
            ],
        );
    }
}
