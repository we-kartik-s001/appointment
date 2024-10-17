<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use VaahCms\Modules\Appointments\Models\Appointment;

class AppointmentsExport implements FromCollection
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
                'Patient Name' => $appointment->patient->name,
                'Doctor Name' => $appointment->doctor->name,
                'Time' => $appointment->date_time,
            ];
        });
        return $appointments;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Patient Name',
            'Doctor Name',
            'Appointment Date'
        ];
    }
}
