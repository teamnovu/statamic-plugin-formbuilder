@component('mail::message')

# {{ __('formbuilder::form.email.new_submission', ['form' => $form->value()?->title()]) }}

{{-- loop through all the fields in the form blueprint --}}
@foreach($fields as $handle => $field)
@php
    /** @var \Statamic\Fields\Value $value */
    $value = $field['value'];
    $rawValue = $value->value();
@endphp

{{-- if field handle starts with form_title show fields display and instructions --}}
@if(str_contains($field['handle'], 'form_title_'))
@if(isset($field['config']['display']))
***{{ $field['config']['display'] }}***<br />
@endif
@if(isset($field['config']['instructions']))
*{{ $field['config']['instructions'] }}*<br />
@endif
@endif

{{-- only show field display when value of that field exists (for text, checkbox, radio button values) --}}
@if(
    !str_contains($field['handle'], 'mail_text')
    && !str_contains($field['handle'], 'form_title_')
    && !isset($field['config']['default'])
    && (
        ($rawValue instanceof \Statamic\Fields\LabeledValue && $rawValue->value())
        || (gettype($rawValue) === 'string' && $rawValue !== 'undefined' && $rawValue !== '')
        || gettype($rawValue) === 'integer'
        || (is_array($rawValue) && count($rawValue) > 0)
        || $rawValue instanceof \Statamic\Assets\Asset
        || ($rawValue instanceof \Statamic\Contracts\Query\Builder && $rawValue->count() > 0))
)

**{{ $field['display'] }}**<br />

{{-- ASSETS --}}
@if($field['fieldtype'] === 'assets')

@if($rawValue instanceof \Statamic\Assets\Asset)
- <a href="{{ $rawValue->absoluteUrl() }}" target="_blank">{{ $rawValue->basename() }}</a>
@else
@foreach($rawValue->get() as $item)
- <a href="{{ $item->absoluteUrl() }}" target="_blank">{{ $item->basename() }}</a>
@endforeach
@endif

{{-- Select --}}
@elseif(is_array($rawValue) && isset($field['config']['options']))

{{-- Select Options --}}
@foreach($rawValue as $item)
- {{ ($item['label'] ?? '') !== 'undefined' ? ($item['label'] ?? '') : '' }}
@endforeach

@elseif(is_array($rawValue) && count($rawValue) === 1 && isset($rawValue[0]['value']))
{{ $rawValue[0]['value'] ? '✅': '❌' }}
@elseif(is_array($rawValue) && count($rawValue) === 0)
{{ '' }}
@elseif(is_array($rawValue))
@foreach($rawValue as $item)
@php $itemStr = is_array($item) ? ($item['label'] ?? $item['value'] ?? implode(', ', $item)) : $item; @endphp
- {{ $itemStr !== 'undefined' ? $itemStr : '' }}
@endforeach
@elseif($rawValue instanceof \Statamic\Fields\LabeledValue)
{{ $rawValue->label() != 'undefined' ? $rawValue->label() : '' }}
@else

{{-- Values --}}
@php
    $inputType = $field['config']['input_type'] ?? null;
@endphp

@if($inputType === 'date' && !empty($field['value']))
{!! nl2br(\Carbon\Carbon::parse($field['value'])->format('d.m.Y')) !!}
@else
{{ $field['value'] }}
@endif

@endif
@endif
@endforeach

@endcomponent
