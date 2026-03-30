<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Form Fieldtype Translations
    |--------------------------------------------------------------------------
    */

    'title' => [
        'checkboxes' => 'Input Checkboxen',
        'date' => 'Input Datum',
        'daterange' => 'Input Datumsbereich',
        'email' => 'Input E-Mail',
        'file_upload' => 'Input Datei-Upload',
        'number' => 'Input Nummer',
        'telephone' => 'Input Telefon',
        'select' => 'Input Auswahl',
        'slider' => 'Input Schieberegler',
        'switch' => 'Input Schalter',
        'text' => 'Input Text',
        'textarea' => 'Input Textfeld',
        'translatable_bard' => 'Übersetzbare Bard',
        'translatable_input' => 'Übersetzbares Eingabefeld',
        'radio_buttons' => 'Input Radiobuttons',
    ],

    'label' => [
        'display' => 'Label',
        'instruction' => 'Dies ist der Haupt-Label und wird oben über dem Eingabefeld angezeigt.',
    ],

    'hint' => [
        'display' => 'Hinweistext',
        'instruction' => 'Dies ist der Hinweis-Text und wird oben rechts neben dem Eingabefeld angezeigt.',
    ],

    'help' => [
        'display' => 'Hilfetext',
        'instruction' => 'Dies ist der Hilfe-Text und wird unten neben dem Eingabefeld angezeigt.',
    ],

    'placeholder' => [
        'display' => 'Platzhalter',
        'instruction' => 'Dies ist der Platzhaltertext, der im Eingabefeld angezeigt wird, wenn kein Wert ausgewählt ist.',
    ],

    'floating_label' => [
        'display' => 'Schwebendes Label',
        'instruction' => 'Dieser Schalter bestimmt, ob das Label innerhalb des Eingabefeldes angezeigt wird oder nicht.',
    ],

    'character_limit' => [
        'display' => 'Zeichenlimit',
        'instruction' => 'Dies ist das Zeichenlimit des Eingabefeldes.',
    ],

    'orientation' => [
        'display' => 'Ausrichtung',
        'instruction' => 'Die Ausrichtung der Checkboxen.',
        'horizontal' => 'Horizontal',
        'vertical' => 'Vertikal',
    ],

    'variant' => [
        'display' => 'Variante',
        'instruction' => 'Der visuelle Stil der Checkbox/Radio-Optionen.',
        'card' => 'Karte',
        'list' => 'Liste',
        'table' => 'Tabelle',
    ],

    'indicator' => [
        'display' => 'Indikator-Platzierung',
        'instruction' => 'Die Platzierung des Checkbox/Radio-Indikators.',
        'start' => 'Anfang',
        'end' => 'Ende',
        'hidden' => 'Versteckt',
    ],

    'multiple' => [
        'display' => 'Mehrfachauswahl',
        'instruction' => 'Dieser Schalter bestimmt, ob es sich um ein Mehrfachauswahlfeld handelt oder nicht.',
    ],

    'options' => [
        'display' => 'Optionen',
        'instruction' => 'Dies sind die Auswahloptionen, die für das Eingabefeld verfügbar sind.',
    ],

    'show_search' => [
        'display' => 'Suche anzeigen',
        'instruction' => 'Dieser Schalter bestimmt, ob im Auswahlfeld ein Suchfeld angezeigt wird oder nicht.',
    ],

    'show_country_code' => [
        'display' => 'Ländercode anzeigen',
        'instruction' => 'Dieser Schalter bestimmt, ob der Ländercode im Eingabefeld angezeigt wird oder nicht.',
    ],

    'default_country_code' => [
        'display' => 'Standard-Ländercode',
        'instruction' => 'Dies ist der Standard-Ländercode, der im Eingabefeld angezeigt wird, wenn kein Ländercode ausgewählt ist.',
    ],

    'show_country_code_selector' => [
        'display' => 'Ländercode-Auswahl anzeigen',
        'instruction' => 'Dieser Schalter bestimmt, ob die Ländercode-Auswahl im Eingabefeld angezeigt wird oder nicht.',
    ],

    'country_code_selector' => [
        'display' => 'Ländercode-Auswahl',
        'instruction' => 'Dies ist die Ländercode-Auswahl, die im Eingabefeld angezeigt wird.',
    ],

    'earliest_date' => [
        'display' => 'Frühestes Datum',
        'instruction' => 'Dies ist das früheste erlaubte Datum für das Eingabefeld.',
    ],

    'latest_date' => [
        'display' => 'Spätestes Datum',
        'instruction' => 'Dies ist das späteste erlaubte Datum für das Eingabefeld.',
    ],

    'min' => [
        'display' => 'Minimum',
        'instruction' => 'Dies ist der Minimalwert für das Eingabefeld.',
    ],

    'max' => [
        'display' => 'Maximum',
        'instruction' => 'Dies ist der Maximalwert für das Eingabefeld.',
    ],

    'step' => [
        'display' => 'Schrittweite',
        'instruction' => 'Dies ist die Schrittweite für das Eingabefeld.',
    ],

    'default' => [
        'display' => 'Standardwert',
        'instruction' => 'Der Standardwert, der im Eingabefeld vorausgefüllt wird.',
    ],

    'max_files' => [
        'display' => 'Maximale Dateien',
        'instruction' => 'Maximale Anzahl Dateien, die hochgeladen werden dürfen.',
    ],

    'max_filesize' => [
        'display' => 'Maximale Dateigrösse',
        'instruction' => 'Maximale Dateigrösse pro Datei in Kilobytes.',
    ],

    'allowed_mimes' => [
        'display' => 'Erlaubte MIME-Types',
        'instruction' => 'Erlaubte MIME-Types (z.B. application/pdf, image/png).',
    ],

    'upload_container' => [
        'display' => 'Upload-Container',
        'instruction' => 'Asset-Container Handle, in dem Dateien abgelegt werden.',
    ],

    'upload_folder' => [
        'display' => 'Upload-Ordner',
        'instruction' => 'Relativer Ordner im Container für hochgeladene Dateien.',
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
    'no_files' => 'Keine Dateien hochgeladen',
    'view_folder' => 'Ordner ansehen',
    'open' => 'Öffnen',
    'open_in_cp' => 'In CP öffnen',

    'attributes' => [],

];
