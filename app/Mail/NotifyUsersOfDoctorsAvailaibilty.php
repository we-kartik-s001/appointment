<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifyUsersOfDoctorsAvailaibilty extends Mailable
{
    use Queueable, SerializesModels;
    protected $patients,$doctor;
    /**
     * Create a new message instance.
     */
    public function __construct($patients,$doctor)
    {
        $this->patients = $patients;
        $this->doctor = $doctor;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dr. '. $this->doctor->name. '\'s updated appointment schedule.' ,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notifyUsersOfDoctorsAvailaibilty',
            with: [
                'patients' => $this->patients,
                'doctor' => $this->doctor
            ]
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
