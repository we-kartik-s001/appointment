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
        return Doctor::all()->map(function ($doctor) {
            return [
                'ID' => $doctor->id,
                'Name' => $doctor->name,
                'Email' => $doctor->email,
                'Price' => $doctor->price,
                'Phone' => $doctor->phone,
                'Specialization' => $doctor->specialization,
                'Start Time' =>  Carbon::parse($doctor->start_time)->format('Y-m-d h:i:s A'),
                'End Time' =>  Carbon::parse($doctor->end_time)->format('Y-m-d h:i:s A'),
                'Created At' => Carbon::parse($doctor->created_at)->format('Y-m-d H:i:s'),
                'Updated At' => Carbon::parse($doctor->updated_at)->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Price',
            'Phone',
            'Specialization',
            'Start Time',
            'End Time',
            'Created At',
            'Updated At',
        ];
    }
}
