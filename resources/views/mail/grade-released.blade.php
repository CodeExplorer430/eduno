@component('mail::message')
# Your Grade Has Been Released

Hello {{ $grade->submission->student->name }},

Your submission for **{{ $grade->submission->assignment->title }}** has been graded.

**Score:** {{ $grade->score }} / {{ $grade->submission->assignment->max_score }}

@if($grade->feedback)
**Feedback:** {{ $grade->feedback }}
@endif

Log in to Eduno to view your full grade details.

@component('mail::button', ['url' => url('/student/grades')])
View My Grades
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
