<?php  namespace VaahCms\Modules\Appointments\Mails;

use Carbon\Carbon;
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
            ->subject('Dr. '. $this->doctor['name'].'\'s'. ' availaibility changed')
            -> with([
                'patient' => $this->patients,
                'doctor' => $this->doctor['name'],
                'start_time' => Carbon::parse($this->doctor['start_time'])->format('h:i:s A'),
                'end_time' => Carbon::parse($this->doctor['end_time'])->format('h:i:s A'),
            ]);
    }

}
