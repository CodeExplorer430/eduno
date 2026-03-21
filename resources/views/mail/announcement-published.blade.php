@component('mail::message')
# {{ $announcement->title }}

{{ $announcement->body }}

@component('mail::button', ['url' => url('/student/courses')])
View Course
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
