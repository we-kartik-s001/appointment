<h1>Appointment schedulde changed</h1>

Hello<br><br>
This is to notify that Dr. {{$doctor.'\'s'}} availiability has been rescheduled. <br>
New Time Slot - <br>
<p>Start Time : {{$start_time}} </p>
<p>End Time: {{$end_time}}</p>
Kindly go to the <a href="{{$redirect_url}}">appointment's section</a> to reschedule your appointment.<br><br>
Thanks,<br>
{{ config('app.name') }}
