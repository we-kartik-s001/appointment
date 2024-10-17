<?php namespace VaahCms\Modules\Appointments\Models;

use App\Exports\AppointmentsExport;
use App\Mail\NotifyDoctorsOfCancelledAppointments;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Faker\Factory;
use Maatwebsite\Excel\Facades\Excel;
use VaahCms\Modules\Appointments\Mails\NotifyDoctorssOfAppointmentCancellationMail;
use WebReinvent\VaahCms\Libraries\VaahMail;
use WebReinvent\VaahCms\Models\VaahModel;
use WebReinvent\VaahCms\Models\User;
use WebReinvent\VaahCms\Libraries\VaahSeeder;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use VaahCms\Modules\Appointments\Mails\NotifyDoctorsOfNewAppointmentsMail;
use VaahCms\Modules\Appointments\Mails\NotifyDoctorsOfUpdatedAppointmentsMail;
use function Doctrine\DBAL\Query\select;

class Appointment extends VaahModel
{

    use SoftDeletes;

    //-------------------------------------------------
    protected $table = 'ap_appointments';
    //-------------------------------------------------
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    //-------------------------------------------------
    protected $fillable = [
        'id',
        'patient_id',
        'doctor_id',
        'date_time',
    ];
    //-------------------------------------------------
    protected $fill_except = [

    ];

    //-------------------------------------------------
    protected $appends = [
    ];

    //-------------------------------------------------
    protected function serializeDate(DateTimeInterface $date)
    {
        $date_time_format = config('settings.global.datetime_format');
        return $date->format($date_time_format);
    }

    //-------------------------------------------------
    public static function getUnFillableColumns()
    {
        return [
            'uuid',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }
    //-------------------------------------------------
    public static function getFillableColumns()
    {
        $model = new self();
        $except = $model->fill_except;
        $fillable_columns = $model->getFillable();
        $fillable_columns = array_diff(
            $fillable_columns, $except
        );
        return $fillable_columns;
    }
    //-------------------------------------------------
    public static function getEmptyItem()
    {
        $model = new self();
        $fillable = $model->getFillable();
        $empty_item = [];
        foreach ($fillable as $column)
        {
            $empty_item[$column] = null;
        }

        $empty_item['is_active'] = 1;

        return $empty_item;
    }

    //-------------------------------------------------

    public function createdByUser()
    {
        return $this->belongsTo(User::class,
            'created_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function updatedByUser()
    {
        return $this->belongsTo(User::class,
            'updated_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function deletedByUser()
    {
        return $this->belongsTo(User::class,
            'deleted_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }

    //-------------------------------------------------
    public function scopeExclude($query, $columns)
    {
        return $query->select(array_diff($this->getTableColumns(), $columns));
    }

    //-------------------------------------------------
    public function scopeBetweenDates($query, $from, $to)
    {

        if ($from) {
            $from = \Carbon::parse($from)
                ->startOfDay()
                ->toDateTimeString();
        }

        if ($to) {
            $to = \Carbon::parse($to)
                ->endOfDay()
                ->toDateTimeString();
        }

        $query->whereBetween('updated_at', [$from, $to]);
    }

    //-------------------------------------------------
    public static function createItem($request)
    {
        $inputs = $request->all();
        $check_status = self::checkAppointmentTime(self::formatTimeZone($inputs['date_time']),$inputs['doctor_id']);
        if(count($check_status) > 0){
            if(array_key_exists('message',$check_status)){
                $response['errors'][] = $check_status['message'];
            }
            else{
                $response['errors'][] = 'Slot not available. Please select another slot.';
            }
            return $response;
        }

        $validation = self::validation($inputs);
        if (!$validation['success']) {
            return $validation;
        }

        $item = new self();
        $item->status = 1; //keeping the status booked by default upon creation
        $item->fill($inputs);
        $item->date_time = Self::formatTimeZone($inputs['date_time']);
        $doctor_email = Doctor::where('id',$inputs['doctor_id'])->pluck('email');
        $patient_details = Patient::where('id',$inputs['patient_id'])->first();
        if($doctor_email){
            VaahMail::dispatch(new NotifyDoctorsOfNewAppointmentsMail($patient_details,Self::formatTimeZone($inputs['date_time'])),$doctor_email);
            $item->save();
        }

        $response = self::getItem($item->id);
        $response['messages'][] = trans("vaahcms-general.saved_successfully");
        return $response;

    }

    //-------------------------------------------------
    public function scopeGetSorted($query, $filter)
    {

        if(!isset($filter['sort']))
        {
            return $query->orderBy('id', 'desc');
        }

        $sort = $filter['sort'];


        $direction = Str::contains($sort, ':');

        if(!$direction)
        {
            return $query->orderBy($sort, 'asc');
        }

        $sort = explode(':', $sort);

        return $query->orderBy($sort[0], $sort[1]);
    }
    //-------------------------------------------------
    public function scopeIsActiveFilter($query, $filter)
    {

        if(!isset($filter['is_active'])
            || is_null($filter['is_active'])
            || $filter['is_active'] === 'null'
        )
        {
            return $query;
        }
        $is_active = $filter['is_active'];

        if((int)$is_active === 1 || $is_active === true)
        {
            return $query->where('status', 1);
        } else{
            return $query->where(function ($q){
                $q->whereNull('status')
                    ->orWhere('status', 0);
            });
        }

    }
    //-------------------------------------------------
    public function scopeTrashedFilter($query, $filter)
    {

        if(!isset($filter['trashed']))
        {
            return $query;
        }
        $trashed = $filter['trashed'];

        if($trashed === 'include')
        {
            return $query->withTrashed();
        } else if($trashed === 'only'){
            return $query->onlyTrashed();
        }

    }
    //-------------------------------------------------
        public function scopeSearchFilter($query, $filter)
    {
        if(!isset($filter['q']))
        {
            return $query;
        }
        $search_array = explode(' ',$filter['q']);
        foreach ($search_array as $search_item){
            $query->where(function ($q1) use ($search_item) {
                $q1->whereHas('doctor', function ($query) use ($search_item) {
                    $query->where('name', 'LIKE', '%' . $search_item . '%')
                          ->orWhere('specialization', 'LIKE', '%' . $search_item . '%');
                })
                ->orWhereHas('patient', function ($query) use ($search_item) {
                    $query->where('name', 'LIKE', '%' . $search_item . '%');
                });
            });
        }
    }
    //-------------------------------------------------
    public static function getList($request)
    {
        $list = self::getSorted($request->filter);
        $list->isActiveFilter($request->filter);
        $list->trashedFilter($request->filter);
        $list = $list->with('doctor','patient');
        $list->searchFilter($request->filter);

        $rows = config('vaahcms.per_page');

        if($request->has('rows'))
        {
            $rows = $request->rows;
        }

        $list = $list->select(['id','doctor_id','patient_id','date_time','status','created_at']);
        $list = $list->paginate($rows);

        $response['success'] = true;
        $response['data'] = $list;

        return $response;


    }

    //-------------------------------------------------
    public static function updateList($request)
    {
        $inputs = $request->all();

        $rules = array(
            'type' => 'required',
        );

        $messages = array(
            'type.required' => trans("vaahcms-general.action_type_is_required"),
        );


        $validator = \Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {

            $errors = errorsToArray($validator->errors());
            $response['success'] = false;
            $response['errors'] = $errors;
            return $response;
        }

        if(isset($inputs['items']))
        {
            $items_id = collect($inputs['items'])
                ->pluck('id')
                ->toArray();
        }

        $items = self::whereIn('id', $items_id);

        switch ($inputs['type']) {
            case 'deactivate':
                $items->withTrashed()->where(['is_active' => 1])
                    ->update(['is_active' => null]);
                break;
            case 'activate':
                $items->withTrashed()->where(function ($q){
                    $q->where('is_active', 0)->orWhereNull('is_active');
                })->update(['is_active' => 1]);
                break;
            case 'trash':
                self::whereIn('id', $items_id)
                    ->get()->each->delete();
                break;
            case 'restore':
                self::whereIn('id', $items_id)->onlyTrashed()
                    ->get()->each->restore();
                break;
        }

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }

    //-------------------------------------------------
    public static function deleteList($request): array
    {
        $inputs = $request->all();

        $rules = array(
            'type' => 'required',
            'items' => 'required',
        );

        $messages = array(
            'type.required' => trans("vaahcms-general.action_type_is_required"),
            'items.required' => trans("vaahcms-general.select_items"),
        );

        $validator = \Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {

            $errors = errorsToArray($validator->errors());
            $response['success'] = false;
            $response['errors'] = $errors;
            return $response;
        }

        $items_id = collect($inputs['items'])->pluck('id')->toArray();
        self::whereIn('id', $items_id)->forceDelete();

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }
    //-------------------------------------------------
     public static function listAction($request, $type): array
    {

        $list = self::query();

        if($request->has('filter')){
            $list->getSorted($request->filter);
            $list->isActiveFilter($request->filter);
            $list->trashedFilter($request->filter);
            $list->searchFilter($request->filter);
        }

        switch ($type) {
            case 'activate-all':
                $list->withTrashed()->where(function ($q){
                    $q->where('is_active', 0)->orWhereNull('is_active');
                })->update(['is_active' => 1]);
                break;
            case 'deactivate-all':
                $list->withTrashed()->where(['is_active' => 1])
                    ->update(['is_active' => null]);
                break;
            case 'trash-all':
                $list->get()->each->delete();
                break;
            case 'restore-all':
                $list->onlyTrashed()->get()
                    ->each->restore();
                break;
            case 'delete-all':
                $list->forceDelete();
                break;
            case 'create-100-records':
            case 'create-1000-records':
            case 'create-5000-records':
            case 'create-10000-records':

            if(!config('appointments.is_dev')){
                $response['success'] = false;
                $response['errors'][] = 'User is not in the development environment.';

                return $response;
            }

            preg_match('/-(.*?)-/', $type, $matches);

            if(count($matches) !== 2){
                break;
            }

            self::seedSampleItems($matches[1]);
            break;
        }

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }
    //-------------------------------------------------
    public static function getItem($id)
    {

        $item = self::where('id', $id)
            ->with('doctor','patient')
            ->withTrashed()
            ->first();

        if(!$item)
        {
            $response['success'] = false;
            $response['errors'][] = 'Record not found with ID: '.$id;
            return $response;
        }
        $response['success'] = true;
        $response['data'] = $item;

        return $response;

    }
    //-------------------------------------------------
    public static function updateItem($request, $id)
    {
        $inputs = $request->all();

        $check_status = self::checkAppointmentTime(self::formatTimeZone($inputs['date_time']),$inputs['doctor_id'], $inputs['patient_id']);
        if(count($check_status) > 0){
            if(array_key_exists('message',$check_status)){
                $response['errors'][] = $check_status['message'];
            }
            else{
                $response['errors'][] = 'Slot not available. Please select another slot.';
            }
            return $response;
        }

        $validation = self::validation($inputs);
        if (!$validation['success']) {
            return $validation;
        }

        $item = self::where('id', $id)->withTrashed()->first();
        $item->fill($inputs);
        $item->date_time = Carbon::parse($inputs['date_time'])->setTimeZone('Asia/Kolkata');
        $doctor_email = Doctor::where('id',$inputs['doctor_id'])->pluck('email');
        $patient_details = Patient::where('id',$inputs['patient_id'])->first();
        if($doctor_email){
            Vaahmail::dispatch((new NotifyDoctorsOfUpdatedAppointmentsMail($patient_details,Carbon::parse($inputs['date_time'])->setTimeZone('Asia/Kolkata'))),$doctor_email);
            $item->save();
        }

        $response = self::getItem($item->id);
        $response['messages'][] = trans("vaahcms-general.saved_successfully");
        return $response;

    }
    //-------------------------------------------------
    public static function deleteItem($request, $id): array
    {
        $item = self::where('id', $id)->withTrashed()->first();
        if (!$item) {
            $response['success'] = false;
            $response['errors'][] = trans("vaahcms-general.record_does_not_exist");
            return $response;
        }
        $item->forceDelete();

        $response['success'] = true;
        $response['data'] = [];
        $response['messages'][] = trans("vaahcms-general.record_has_been_deleted");

        return $response;
    }
    //-------------------------------------------------
    public static function itemAction($request, $id, $type): array
    {
        $users = Appointment::select('doctor_id','patient_id','date_time')->where('id',$id)->get();
        $time = $users[0]['date_time'];
        $doctor_email = Doctor::where('id',$users[0]['doctor_id'])->pluck('email');
        $patient_details = Patient::where('id',$users[0]['patient_id'])->first();
        switch($type)
        {
            case 'activate':
                self::where('id', $id)
                    ->withTrashed()
                    ->update(['is_active' => 1]);
                break;
            case 'deactivate':
                self::where('id', $id)
                    ->withTrashed()
                    ->update(['is_active' => null]);
                break;
            case 'trash':
                self::find($id)
                    ->delete();
                break;
            case 'restore':
                self::where('id', $id)
                    ->onlyTrashed()
                    ->first()->restore();
                break;
            case 'cancel':
                self::where('id',$id)
                    ->update(['status' => 0]);
                VaahMail::dispatch((new NotifyDoctorssOfAppointmentCancellationMail($patient_details,$time)),$doctor_email);
                break;
        }

        return self::getItem($id);
    }
    //-------------------------------------------------

    public static function validation($inputs)
    {
        $rules = array(
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'date_time' => ['required','date',Rule::unique('ap_appointments')->ignore($inputs['id'])]
        );

        $validator = \Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $messages = $validator->errors();
            $response['success'] = false;
            $response['errors'] = $messages->all();
            return $response;
        }

        $response['success'] = true;
        return $response;

    }

    //-------------------------------------------------
    public static function getActiveItems()
    {
        $item = self::where('is_active', 1)
            ->withTrashed()
            ->first();
        return $item;
    }

    //-------------------------------------------------
    //-------------------------------------------------
    public static function seedSampleItems($records=100)
    {

        $i = 0;

        while($i < $records)
        {
            $inputs = self::fillItem(false);

            $item =  new self();
            $item->fill($inputs);
            $item->save();

            $i++;

        }

    }


    //-------------------------------------------------
    public static function fillItem($is_response_return = true)
    {
        $request = new Request([
            'model_namespace' => self::class,
            'except' => self::getUnFillableColumns()
        ]);
        $fillable = VaahSeeder::fill($request);
        if(!$fillable['success']){
            return $fillable;
        }
        $inputs = $fillable['data']['fill'];

        $faker = Factory::create();

        /*
         * You can override the filled variables below this line.
         * You should also return relationship from here
         */

        if(!$is_response_return){
            return $inputs;
        }

        $response['success'] = true;
        $response['data']['fill'] = $inputs;
        return $response;
    }

    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------

    public function patient(){
        return $this->belongsTo(Patient::class)
            ->select(['id','name','email','phone']);
    }

    public function doctor(){
        return $this->belongsTo(Doctor::class)
            ->select(['id','name','email','phone','specialization','start_time','end_time']);
    }

    public static function checkAppointmentTime($date_time,$doctor_id,$patient_id = null)
    {
        if(!$date_time || !$doctor_id){
            $appointments['message'] = 'Insufficient parameters passed';
            return $appointments;
        }
        //This code will help us to keep track that no appointment corresponding to a doctor should be created which falls within 15 min of other person's slot
        $date_time = Carbon::parse(Carbon::createFromFormat('Y-m-d H:i:s',$date_time));
        $start_time = json_decode(Doctor::where('id',$doctor_id)->pluck('start_time'));
        $end_time = json_decode(Doctor::where('id',$doctor_id)->pluck('end_time'));

        if($date_time->lt($start_time[0]) || $date_time->gt($end_time[0])){
            $appointments['message'] = 'You are attempting to book the slot when doctor will be away. Please select a valid slot.';
            return $appointments;
        }

        $appointments = Appointment::where('doctor_id',$doctor_id)
        ->where('patient_id','!=',$patient_id)
        ->where(function ($query) use ($date_time) {
            $query->whereBetween('date_time', [$date_time->copy()
                ->subMinutes(15), $date_time->copy()->addMinutes(15)])
                ->where('status',1);
        })->get();

        if(!$appointments){
            return $appointments->isEmpty(); // Returns true if no overlapping appointments
        }
        return $appointments->toArray();
    }

    public static function formatTimeZone($time){
        return Carbon::parse($time)->timezone('Asia/Kolkata');
    }

    public static function exportAppointments(){
        return Excel::download(new AppointmentsExport,'appointments.csv');
    }

    public static function importAppointments($file_contents){
        $failed_records = 0;
        $file_contents = self::normalizeCsvData($file_contents);
        $validationErrors = [];

        foreach ($file_contents as $index => $content) {
            $check_doctor = Doctor::where('email', $content['Doctor_Email'])->pluck('id')->first();
            $check_patient = Patient::where('email',$content['Patient_Email'])->pluck('id')->first();
            if(!$check_doctor && strlen($content['Doctor_Email'])){
                $validationErrors[] = 'Doctor with '. $content['Doctor_Email'].' doesn\'t exists';
                $failed_records++;
            }
            if(!$check_patient && strlen($content['Patient_Email'])){
                $validationErrors[] = 'Patient with '. $content['Patient_Email'].' doesn\'t exists';
                $failed_records++;
            }

            foreach ($content as $field => $value) {
                switch ($field) {
                    case 'Patient_Email':
                        if (empty($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $validationErrors[] = 'Patient email is required and must be a string.';
                            $failed_records++;
                        }
                        break;

                    case 'Doctor_Email':
                        if (empty($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $validationErrors[] = 'Doctor email is required and must be a valid email address.';
                            $failed_records++;
                        }
                        break;

                    case 'Start_Date':
                        if (!empty($value) && !strtotime($value)) {
                            $validationErrors[] = 'Start_Time must be a valid date.';
                            $failed_records++;
                        }else if(empty($value)){
                            $validationErrors[] = 'Start Time is required.';
                            $failed_records++;
                        }
                        break;

                    default:
                        break;
                }
            }

            if (!empty($validationErrors)) {
                continue;
            }

            $dataToInsert = [
                'doctor_id' => $check_doctor,
                'patient_id' => $check_patient,
                'status' => 1,
                'date_time' => !empty($content['Start_Date']) ? Carbon::parse($content['Start_Date'])->format('Y-m-d H:i:s') : null,
            ];

            if(!$content['End_Date']){
                $app = new Appointment();
                $app->doctor_id = $check_doctor;
                $app->patient_id = $check_patient;
                $app->date_time = !empty($content['Start_Date']) ? Carbon::parse($content['Start_Date'])->format('Y-m-d H:i:s') : null;
                $app->status = 1;
                $app->save();
            }
        }
        return response()->json([
            'total_records' => count($file_contents),
            'failed_records' => $failed_records,
            'successful_records' => count($file_contents) - $failed_records,
            'reporting_errors' => $validationErrors
        ]);
    }

    public static function normalizeCsvData($content){
        $reformatted_data = array_map(function($record) {
            return array_combine(
                array_map(function($key) {
                    return trim($key, '"');
                }, array_keys($record)),
                array_map(function($value) {
                    return trim($value, '"');
                }, $record)
            );
        }, $content);

        return $reformatted_data;
    }
}
