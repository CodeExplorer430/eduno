@component('mail::message')
# Assignment Deadline Reminder

Hello,

This is a reminder that the assignment **{{ $assignment->title }}** is due soon.

- **Due:** {{ $assignment->due_at }}
- **Max Score:** {{ $assignment->max_score }}

@component('mail::button', ['url' => url('/student/assignments/'.$assignment->id)])
View Assignment
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
