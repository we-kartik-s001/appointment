<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VaahCms\Modules\Appointments\Models\Doctor;
use VaahCms\Modules\Appointments\Models\Patient;

class ProcessBulkRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $records, $type;
    /**
     * Create a new job instance.
     */
    public function __construct($records, $type)
    {
        $this->records = $records;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $i = 0;
        while ($i<$this->records){
            if($this->type == 'Doctor'){
                $inputs = Doctor::fillItem(false);
                $item =  new Doctor();
            }else if($this->type == 'Patient'){
                $inputs = Patient::fillItem(false);
                $item =  new Patient();
            }
            $item->fill($inputs);
            $item->save();

            $i++;
        }
    }
}
