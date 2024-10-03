<?php  namespace VaahCms\Modules\Appointments\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyUsersOfDoctorsAvailaibiltyMail extends Mailable {

    use Queueable, SerializesModels;
    protected $patients,$doctor;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($patients,$doctor)
    {
        $this->patients = $patients;
        $this->doctor = $doctor;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function build()
    {
        return $this->view('appointments::emails.notifyusersofdoctorsavailaibilty')
            -> with([
                'patient' => $this->patients,
                'doctor' => $this->doctor
            ]);
    }

}
