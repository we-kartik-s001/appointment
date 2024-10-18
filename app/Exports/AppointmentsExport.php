<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use VaahCms\Modules\Appointments\Models\Appointment;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AppointmentsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $appointments_list = Appointment::with('doctor','patient')->get();
        $appointments = $appointments_list->map(function ($appointment) {
            return [
                'ID' => $appointment->id,
                'Patient_Name' => $appointment->patient->name,
                'Patient_Email' => $appointment->patient->email,
                'Doctor_Name' => $appointment->doctor->name,
                'Doctor_Email' => $appointment->doctor->email,
                'Doctor_Specialization' => $appointment->doctor->specialization,
                'Start_Date' => Carbon::parse($appointment->date_time)->format('Y-m-d H:i:s'),
                'End_Date' => Carbon::parse($appointment->date_time)->addMinutes(15)->format('Y-m-d H:i:s'),
                'Appointment_Status' => $appointment->status ? 'Booked' : 'Cancelled'
            ];
        });
        return $appointments;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Patient_Name',
            'Patient_Email',
            'Doctor_Name',
            'Doctor_Email',
            'Doctor_Specialization',
            'Start_Date',
            'End_Date',
            'Appointment_Status'
        ];
    }
}
