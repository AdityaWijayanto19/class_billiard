<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;

    public $fileName;

    public $reportType;

    /**
     * Create a new message instance.
     */
    public function __construct($filePath, $fileName, $reportType = 'Harian')
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->reportType = $reportType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laporan '.$this->reportType.' - Billiard Class',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.report',
            text: 'emails.report-text',
            with: [
                'reportType' => $this->reportType,
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
