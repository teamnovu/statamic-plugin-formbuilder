<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Form Fieldtype Translations
    |--------------------------------------------------------------------------
    */

    'title' => [
        'checkboxes' => 'Form Input Checkboxes',
        'date' => 'Form Input Date',
        'daterange' => 'Form Input Date Range',
        'email' => 'Form Input Email',
        'file_upload' => 'Form Input File Upload',
        'number' => 'Form Input Number',
        'phone' => 'Form Input Phone',
        'select' => 'Form Input Select',
        'slider' => 'Form Input Slider',
        'switch' => 'Form Input Switch',
        'text' => 'Form Input Text',
        'textarea' => 'Form Input Textarea',
        'translatable_bard' => 'Translatable Bard',
        'translatable_input' => 'Translatable Input',
        'radio_buttons' => 'Form Input Radio Buttons',
        'display_text' => 'Form Display Text',
    ],

    'display_title' => [
        'display' => 'Title',
        'instruction' => 'The title displayed to the user in the form.',
    ],

    'display_text' => [
        'display' => 'Text',
        'instruction' => 'The text displayed to the user in the form.',
    ],

    'label' => [
        'display' => 'Label',
        'instruction' => 'This is the main label and will be displayed above the input field.',
    ],

    'hint' => [
        'display' => 'Hint text',
        'instruction' => 'This is the hint message and will be displayed on the top right of the input field.',
    ],

    'help' => [
        'display' => 'Helper Text',
        'instruction' => 'This is the help message and will be displayed on the bottom of the input field.',
    ],

    'placeholder' => [
        'display' => 'Placeholder',
        'instruction' => 'This is the placeholder text that will be displayed in the input field if no value is selected.',
    ],

    'floating_label' => [
        'display' => 'Floating Label',
        'instruction' => 'When enabled, the label is displayed inside the input field and moves upward when the field is focused or filled. Replaces the placeholder text.',
    ],

    'character_limit' => [
        'display' => 'Character Limit',
        'instruction' => 'This is the character limit of the input field.',
    ],

    'orientation' => [
        'display' => 'Orientation',
        'instruction' => 'The orientation of the checkboxes.',
        'horizontal' => 'Horizontal',
        'vertical' => 'Vertical',
    ],

    'variant' => [
        'display' => 'Variant',
        'instruction' => 'The visual style of the checkbox/radio options.',
        'card' => 'Card',
        'list' => 'List',
        'table' => 'Table',
    ],

    'indicator' => [
        'display' => 'Indicator Placement',
        'instruction' => 'The placement of the checkbox/radio indicator.',
        'start' => 'Start',
        'end' => 'End',
        'hidden' => 'Hidden',
    ],

    'multiple' => [
        'display' => 'Multiple',
        'instruction' => 'This toggle will decide if it is a multiple select field or not.',
    ],

    'options' => [
        'display' => 'Options',
        'instruction' => 'This is the select options that are available for the input field.',
    ],

    'show_search' => [
        'display' => 'Show Search',
        'instruction' => 'This toggle will decide if in the select field there is a search input or not.',
    ],

    'show_country_code' => [
        'display' => 'Show Country Code',
        'instruction' => 'This toggle will decide if the country code is displayed in the input field or not.',
    ],

    'default_country_code' => [
        'display' => 'Default Country Code',
        'instruction' => 'This is the default country code and will be displayed in the input field if no country code is selected.',
    ],

    'show_country_code_selector' => [
        'display' => 'Show Country Code Selector',
        'instruction' => 'This toggle will decide if the country code selector is displayed in the input field or not.',
    ],

    'country_code_selector' => [
        'display' => 'Country Code Selector',
        'instruction' => 'This is the country code selector and will be displayed in the input field if the country code selector is displayed.',
    ],

    'earliest_date' => [
        'display' => 'Earliest Date',
        'instruction' => 'This is the earliest date and will be displayed in the input field if the earliest date is displayed.',
    ],

    'latest_date' => [
        'display' => 'Latest Date',
        'instruction' => 'This is the latest date and will be displayed in the input field if the latest date is displayed.',
    ],

    'min' => [
        'display' => 'Minimum',
        'instruction' => 'This is the minimum value allowed for the input field.',
    ],

    'max' => [
        'display' => 'Maximum',
        'instruction' => 'This is the maximum value allowed for the input field.',
    ],

    'step' => [
        'display' => 'Step',
        'instruction' => 'This is the step size for the input field.',
    ],

    'default' => [
        'display' => 'Default Value',
        'instruction' => 'The default value that will be pre-filled in the input field.',
    ],

    'max_files' => [
        'display' => 'Maximum Files',
        'instruction' => 'Maximum number of files allowed for this upload field.',
    ],

    'max_filesize' => [
        'display' => 'Maximum File Size',
        'instruction' => 'Maximum file size per file in kilobytes.',
    ],

    'allowed_mimes' => [
        'display' => 'Allowed MIME Types',
        'instruction' => 'Allowed MIME types or file extensions (e.g. application/pdf, image/png, .pdf, .png).',
    ],

    'upload_container' => [
        'display' => 'Upload Container',
        'instruction' => 'Asset container handle where uploaded files will be stored.',
    ],

    'upload_folder' => [
        'display' => 'Upload Folder',
        'instruction' => 'Relative folder inside the container for uploaded files.',
    ],

    'icon_label' => [
        'display' => 'Icon Label',
        'instruction' => 'This label will be displayed below the icon.',
    ],

    'label_deactivated' => [
        'display' => 'Label when Deactivated',
        'instruction' => 'This label will be displayed when the switch is deactivated.',
    ],

    'label_activated' => [
        'display' => 'Label when Activated',
        'instruction' => 'This label will be displayed when the switch is activated.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'attributes' => [],

];