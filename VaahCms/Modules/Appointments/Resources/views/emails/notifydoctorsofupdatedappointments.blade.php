<h1>Appoinment Rescheduled</h1>
Hello,<br><br>

Appoinment has been rescehduled. Please refer to the new timings of the appointment below - <br><br>
Name: {{$patient['name']}}<br>
Email: {{$patient['email']}}<br>
Phone: {{$patient['phone']}}<br>
New Appointment Slot: {{$time['start_time']}} - {{$time['end_time']}}<br><br>

Thanks,<br>
{{ config('app.name') }}
