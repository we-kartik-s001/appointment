<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class NotifyDoctorsOfUpdatedAppointment extends Mailable
{
    use Queueable, SerializesModels;
    protected $patient_details, $time;
    /**
     * Create a new message instance.
     */
    public function __construct($patient_details,$time)
    {
        $this->patient_details = $patient_details;
        $this->time = $time;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment rescheduled by '.$this->patient_details['name'] ,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notifyDoctorsOfUpdatedAppointment',
            with: [
                'patient' => $this->patient_details,
                'time' => Carbon::parse($this->time)->format('Y-m-d H:i:s')
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
        return [];
    }
}
