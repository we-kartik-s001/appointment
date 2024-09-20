<x-mail::message>
<h1>New Appoinment</h1>
Hello,<br><br>

A new appoinment has been scehduled. Please refer to the details of patient below - <br><br>
    Name: {{$patient['name']}}<br>
    Email: {{$patient['email']}}<br>
    Phone: {{$patient['phone']}}<br>
    Time Slot: {{$time}}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
