<?php  namespace VaahCms\Modules\Appointments\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyUsersOfCancelledAppointmentsMail extends Mailable {

    use Queueable, SerializesModels;
    protected $doctor;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($doctor)
    {
        $this->doctor = $doctor;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function build()
    {
        return $this->view('appointments::emails.notifyusersofcancelledappointments')
            ->subject('Appointment Cancelled')
            ->with(['doctor' => $this->doctor]);
    }

}
