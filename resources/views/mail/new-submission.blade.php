@component('mail::message')
# New Submission Received

Hello {{ $submission->assignment->courseSection->instructor->name }},

**{{ $submission->student->name }}** has submitted **{{ $submission->assignment->title }}**.

- **Submitted at:** {{ $submission->submitted_at }}
- **Attempt:** #{{ $submission->attempt_no }}
@if($submission->is_late)
- ⚠️ This submission was submitted **late**.
@endif

@component('mail::button', ['url' => url('/instructor/submissions/'.$submission->id)])
Review Submission
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
