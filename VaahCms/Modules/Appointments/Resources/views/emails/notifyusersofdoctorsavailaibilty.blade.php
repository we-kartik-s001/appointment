<h1>Appointment schedulde changed</h1>

Hello<br><br>
This is to notify that the {{$doctor['name'].'\'s'}} appointment has been rescheduled. <br>
New Time Slot - <br>
<p>Start Time : {{$doctor['start_time']}} </p>
<p>End Time: {{$doctor['end_time']}}</p>
Kindly go to the dashboard to reschedule your appointment.<br><br>
Thanks,<br>
{{ config('app.name') }}
