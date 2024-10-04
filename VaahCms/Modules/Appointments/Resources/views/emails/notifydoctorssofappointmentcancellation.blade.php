<h1>Appointment Cancelled</h1>

Hello, <br><br>
This is to notify that the appointment scheduled for <br>
{{$time}} has been cancelled.<br><br>Below are the appointment details of the person - <br><br>
Name: {{$patient['name']}}<br>
Email: {{$patient['email']}}<br>
Phone: {{$patient['phone']}}<br><br>

Thanks,<br>
{{ config('app.name') }}
