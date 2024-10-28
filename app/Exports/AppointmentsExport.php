<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use VaahCms\Modules\Appointments\Models\Appointment;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AppointmentsExport implements FromCollection, WithHeadings
{
    protected $type;

    public function __construct($type = null)
    {
        $this->type = $type;
    }

    public function collection()
    {
        $appointments = Appointment::with('doctor', 'patient');

        if ($this->type == 'sample-download') {
            return collect();
        }

        return $appointments->get()->map(function ($appointment) {
            return [
                'Patient_Name' => $appointment->patient->name,
                'Patient_Email' => $appointment->patient->email,
                'Doctor_Name' => $appointment->doctor->name,
                'Doctor_Email' => $appointment->doctor->email,
                'Doctor_Specialization' => $appointment->doctor->specialization,
                'Start_Date' => Carbon::parse($appointment->date_time)->format('Y-m-d H:i:s'),
                'End_Date' => Carbon::parse($appointment->date_time)->addMinutes(15)->format('Y-m-d H:i:s'),
                'Appointment_Status' => $appointment->status ? 'Booked' : 'Cancelled',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Patient Name',
            'Patient Email',
            'Doctor Name',
            'Doctor Email',
            'Doctor Specialization',
            'Start Date',
            'End Date',
            'Appointment Status',
        ];
    }
}
