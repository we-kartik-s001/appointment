<x-mail::message>
<h1>Appoinment Rescheduled</h1>
Hello,<br><br>

Appoinment has been rescehduled. Please refer to the new timings of the patient below - <br><br>
Name: {{$patient['name']}}<br>
Email: {{$patient['email']}}<br>
Phone: {{$patient['phone']}}<br>
Appointment Slot: {{$time}}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
