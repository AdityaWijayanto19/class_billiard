<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendRecapEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;

    public $fileName;

    public $reportPeriod;

    public $report;

    /**
     * Create a new message instance.
     */
    public function __construct($filePath, $fileName, $reportPeriod, $report = null)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->reportPeriod = $reportPeriod;
        $this->report = $report;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tutup Hari - '.$this->reportPeriod.' - Billiard Class',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.recap',
            text: 'emails.recap-text',
            with: [
                'reportPeriod' => $this->reportPeriod,
                'report' => $this->report,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->filePath)
                ->as($this->fileName)
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
