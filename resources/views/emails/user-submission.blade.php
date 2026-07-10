{{-- Default localized email body for a form submission. --}}
@component('mail::message')

@php
    $mailText = $email_config['mail_text'] ?? null;
@endphp

@if ($mailText)
{!! $mailText !!}
@endif

@endcomponent
