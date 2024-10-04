<?php  namespace VaahCms\Modules\Appointments\Mails;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyDoctorssOfAppointmentCancellationMail extends Mailable {

    use Queueable, SerializesModels;
    protected $patient_details, $time;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($patient_details,$time)
    {
        $this->patient_details = $patient_details;
        $this->time = $time;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function build()
    {
        return $this->view('appointments::emails.notifydoctorssofappointmentcancellation')
            ->subject('Appointment Cancelled by '.$this->patient_details['name'])
            ->with([
                'patient' => $this->patient_details,
                'time' => Carbon::parse($this->time)->format('Y-m-d h:i:s A')
            ]);
    }

}
