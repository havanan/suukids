@component('mail::message')
    {{ json_encode($message) }}<br>

    {{ json_encode($trace) }}

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
