{{-- Plain-text counterpart of user-submission, using the same parsed email body. --}}
@php
    $mailText = $email_config['mail_text'] ?? '';
    $mailText = preg_replace('/<br\s*\/?\s*>/i', "\n", $mailText);
    $mailText = preg_replace('/<\/(?:p|div|li|h[1-6])\s*>/i', "\n\n", $mailText);
    $mailText = html_entity_decode(strip_tags($mailText), ENT_QUOTES | ENT_HTML5, 'UTF-8');
@endphp
{!! trim($mailText) !!}
