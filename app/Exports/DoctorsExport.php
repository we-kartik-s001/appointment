<?php

namespace App\Exports;

use Carbon\Carbon;
use VaahCms\Modules\Appointment\Models\Doctor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DoctorsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $doctors = Doctor::all()->map(function ($doctor) {
            return [
                'Name' => $doctor->name,
                'Email' => $doctor->email,
                'Price' => $doctor->price,
                'Phone' => $doctor->phone,
                'Specialization' => $doctor->specialization,
                'Start_Time' =>  $doctor->start_time,
                'End_Time' =>  $doctor->end_time,
                'Created_At' => $doctor->created_at,
                'Updated_At' => $doctor->updated_at
            ];
        });
        return $doctors;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Price',
            'Phone',
            'Specialization',
            'Start_Time',
            'End_Time',
            'Created_At',
            'Updated_At',
        ];
    }
}
