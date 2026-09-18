<?php

// Form Builder control panel translations.

return [

    'email' => [
        'new_submission' => 'Neuer Formulareintrag: :form',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Fieldtype Translations
    |--------------------------------------------------------------------------
    */

    'one_click_translation' => [
        'translate' => 'Übersetzen',
        'translating' => 'Wird übersetzt…',
        'missing_source' => 'Zuerst einen Wert in der Standardsprache eingeben.',
        'failed' => 'Übersetzung fehlgeschlagen. Ist das One-click Content Translation Addon konfiguriert?',
    ],

    'email_config' => [
        'mail_text_display' => 'E-Mail Text',
        'mail_text_instruction' => 'HTML-Inhalt, der in die E-Mail-Vorlage eingefügt wird. Für Einreichungswerte Antlers-Platzhalter mit doppelten geschweiften Klammern verwenden, z. B. {{ vorname }}. Feld-Handles müssen mit dem Formular-Blueprint übereinstimmen.',
        'to_instruction' => 'Empfänger-Adresse(n), kommagetrennt. Für dynamische Werte Antlers verwenden, z. B. {{ email }}.',
        'from_instruction' => 'Absender-Adresse. RFC-Format: Anzeigename &lt;email@beispiel.ch&gt; oder nur email@beispiel.ch. Nur ein Anzeigename ist ungültig.',
        'reply_to_instruction' => 'Antwort-an-Adresse(n), kommagetrennt. RFC-Format: Anzeigename &lt;email@beispiel.ch&gt; oder nur email@beispiel.ch.',
        'subject_instruction' => 'Betreffzeile. Antlers-Platzhalter mit doppelten geschweiften Klammern verwenden, z. B. {{ vorname }}.',
        'html_instruction' => 'Blade-Vorlage für diese E-Mail. Wählen Sie vendor/formbuilder/emails/user-submission (Bestätigung an Absender/in; nutzt den E-Mail Text oben) oder vendor/formbuilder/emails/submission (Benachrichtigung ans Team; listet alle Felder).',
        'markdown_instruction' => 'Standardmässig aktiviert für Formbuilder-Vorlagen. Nur deaktivieren, wenn eine einfache Blade-Ansicht ohne mail::message verwendet wird.',
        'mail_preview_display' => 'Vorschau',
        'mail_preview_instruction' => 'In der Vorschau werden nur gespeicherte Werte angezeigt.',
        'mail_preview_button' => 'E-Mail Vorschau',
        'mail_preview_modal_title' => 'E-Mail Vorschau',
    ],

    'title' => [
        'checkboxes' => 'Formular Input Checkboxen',
        'date' => 'Formular Input Datum',
        'daterange' => 'Formular Input Datumsbereich',
        'email' => 'Formular Input E-Mail',
        'file_upload' => 'Formular Input Datei-Upload',
        'number' => 'Formular Input Nummer',
        'phone' => 'Formular Input Telefon',
        'select' => 'Formular Input Auswahl',
        'slider' => 'Formular Input Schieberegler',
        'switch' => 'Formular Input Schalter',
        'text' => 'Formular Input Text',
        'textarea' => 'Formular Input Textfeld',
        'translatable_bard' => 'Übersetzbare Bard',
        'translatable_input' => 'Übersetzbares Eingabefeld',
        'radio_buttons' => 'Formular Input Radiobuttons',
        'display_text' => 'Formular Anzeigetext',
    ],

    'display_title' => [
        'display' => 'Titel',
        'instruction' => 'Der Titel, der dem Benutzer im Formular angezeigt wird.',
    ],

    'display_text' => [
        'display' => 'Text',
        'instruction' => 'Der Text, der dem Benutzer im Formular angezeigt wird.',
    ],

    'label' => [
        'display' => 'Label',
        'instruction' => 'Dies ist der Haupt-Label und wird oben über dem Eingabefeld angezeigt.',
    ],

    'hide_label' => [
        'display' => 'Label ausblenden',
        'instruction' => 'Wenn aktiviert, wird das Label nicht über dem Eingabefeld angezeigt.',
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
        'instruction' => 'Wenn aktiviert, wird das Label innerhalb des Eingabefeldes angezeigt und bewegt sich nach oben, wenn das Feld fokussiert oder ausgefüllt ist. Ersetzt den Platzhaltertext.',
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
        'instruction' => 'Erlaubte MIME-Types oder Dateiendungen (z.B. application/pdf, image/png, .pdf, .png).',
    ],

    'upload_container' => [
        'display' => 'Upload-Container',
        'instruction' => 'Asset-Container Handle, in dem Dateien abgelegt werden.',
    ],

    'upload_folder' => [
        'display' => 'Upload-Ordner',
        'instruction' => 'Relativer Ordner im Container für hochgeladene Dateien.',
    ],

    'icon_label' => [
        'display' => 'Icon Beschriftung',
        'instruction' => 'Diese Beschriftung wird unterhalb des Icons angezeigt.',
    ],

    'label_deactivated' => [
        'display' => 'Beschriftung wenn Deaktiviert',
        'instruction' => 'Diese Beschriftung wird angezeigt, wenn der Schalter deaktiviert ist.',
    ],

    'label_activated' => [
        'display' => 'Beschriftung wenn Aktiviert',
        'instruction' => 'Diese Beschriftung wird angezeigt, wenn der Schalter aktiviert ist.',
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
