<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Form Fieldtype Translations
    |--------------------------------------------------------------------------
    */

    'title' => [
        'checkboxes' => 'Champ Cases à cocher',
        'date' => 'Champ Date',
        'daterange' => 'Champ Plage de dates',
        'email' => 'Champ E-mail',
        'file_upload' => 'Champ Téléversement de fichier',
        'number' => 'Champ Nombre',
        'phone' => 'Champ Téléphone',
        'select' => 'Champ Sélection',
        'slider' => 'Champ Curseur',
        'switch' => 'Champ Interrupteur',
        'text' => 'Champ Texte',
        'textarea' => 'Champ Zone de texte',
        'translatable_bard' => 'Bard traduisible',
        'translatable_input' => 'Champ traduisible',
        'radio_buttons' => 'Champ Boutons radio',
        'display_text' => 'Texte d’affichage',
    ],

    'display_title' => [
        'display' => 'Titre',
        'instruction' => 'Le titre affiché à l’utilisateur dans le formulaire.',
    ],

    'display_text' => [
        'display' => 'Texte',
        'instruction' => 'Le texte affiché à l’utilisateur dans le formulaire.',
    ],

    'label' => [
        'display' => 'Libellé',
        'instruction' => 'Libellé principal affiché au-dessus du champ de saisie.',
    ],

    'hint' => [
        'display' => 'Indice',
        'instruction' => 'Message d’indication affiché en haut à droite du champ de saisie.',
    ],

    'help' => [
        'display' => 'Texte d’aide',
        'instruction' => 'Message d’aide affiché en bas du champ de saisie.',
    ],

    'placeholder' => [
        'display' => 'Texte indicatif',
        'instruction' => 'Texte affiché dans le champ si aucune valeur n’est sélectionnée.',
    ],

    'floating_label' => [
        'display' => 'Libellé flottant',
        'instruction' => 'Si activé, le libellé s’affiche dans le champ puis remonte quand le champ est focalisé ou rempli. Remplace le texte indicatif.',
    ],

    'character_limit' => [
        'display' => 'Limite de caractères',
        'instruction' => 'Limite de caractères du champ de saisie.',
    ],

    'orientation' => [
        'display' => 'Orientation',
        'instruction' => 'Orientation des cases à cocher.',
        'horizontal' => 'Horizontale',
        'vertical' => 'Verticale',
    ],

    'variant' => [
        'display' => 'Variante',
        'instruction' => 'Style visuel des options (cases à cocher / boutons radio).',
        'card' => 'Carte',
        'list' => 'Liste',
        'table' => 'Tableau',
    ],

    'indicator' => [
        'display' => 'Position de l’indicateur',
        'instruction' => 'Position de l’indicateur des cases à cocher / boutons radio.',
        'start' => 'Début',
        'end' => 'Fin',
        'hidden' => 'Masqué',
    ],

    'multiple' => [
        'display' => 'Multiple',
        'instruction' => 'Détermine si le champ permet une sélection multiple.',
    ],

    'options' => [
        'display' => 'Options',
        'instruction' => 'Options disponibles pour ce champ.',
    ],

    'show_search' => [
        'display' => 'Afficher la recherche',
        'instruction' => 'Détermine si un champ de recherche est affiché dans la liste de sélection.',
    ],

    'show_country_code' => [
        'display' => 'Afficher l’indicatif pays',
        'instruction' => 'Détermine si l’indicatif pays est affiché dans le champ.',
    ],

    'default_country_code' => [
        'display' => 'Indicatif pays par défaut',
        'instruction' => 'Indicatif pays utilisé par défaut s’il n’y a pas de sélection.',
    ],

    'show_country_code_selector' => [
        'display' => 'Afficher le sélecteur d’indicatif pays',
        'instruction' => 'Détermine si le sélecteur d’indicatif pays est affiché dans le champ.',
    ],

    'country_code_selector' => [
        'display' => 'Sélecteur d’indicatif pays',
        'instruction' => 'Sélecteur d’indicatif pays affiché dans le champ lorsque cette option est activée.',
    ],

    'earliest_date' => [
        'display' => 'Date la plus ancienne',
        'instruction' => 'Date la plus ancienne autorisée pour ce champ.',
    ],

    'latest_date' => [
        'display' => 'Date la plus récente',
        'instruction' => 'Date la plus récente autorisée pour ce champ.',
    ],

    'min' => [
        'display' => 'Minimum',
        'instruction' => 'Valeur minimale autorisée pour ce champ.',
    ],

    'max' => [
        'display' => 'Maximum',
        'instruction' => 'Valeur maximale autorisée pour ce champ.',
    ],

    'step' => [
        'display' => 'Pas',
        'instruction' => 'Incrément (pas) du champ.',
    ],

    'default' => [
        'display' => 'Valeur par défaut',
        'instruction' => 'Valeur préremplie dans le champ.',
    ],

    'max_files' => [
        'display' => 'Nombre maximal de fichiers',
        'instruction' => 'Nombre maximum de fichiers autorisés pour ce champ de téléversement.',
    ],

    'max_filesize' => [
        'display' => 'Taille maximale de fichier',
        'instruction' => 'Taille maximale par fichier en kilo-octets.',
    ],

    'allowed_mimes' => [
        'display' => 'Types MIME autorisés',
        'instruction' => 'Types MIME ou extensions autorisés (ex. application/pdf, image/png, .pdf, .png).',
    ],

    'upload_container' => [
        'display' => 'Conteneur de téléversement',
        'instruction' => 'Identifiant du conteneur d’assets où seront stockés les fichiers téléversés.',
    ],

    'upload_folder' => [
        'display' => 'Dossier de téléversement',
        'instruction' => 'Dossier relatif dans le conteneur pour les fichiers téléversés.',
    ],

    'icon_label' => [
        'display' => 'Libellé de l’icône',
        'instruction' => 'Libellé affiché sous l’icône.',
    ],

    'label_deactivated' => [
        'display' => 'Libellé (désactivé)',
        'instruction' => 'Libellé affiché lorsque l’interrupteur est désactivé.',
    ],

    'label_activated' => [
        'display' => 'Libellé (activé)',
        'instruction' => 'Libellé affiché lorsque l’interrupteur est activé.',
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

  
    'no_text' => 'Aucun texte saisi par l’utilisateur',
    'open' => 'Ouvrir',
    'open_in_cp' => 'Ouvrir dans le CP',
    'view_folder' => 'Voir le dossier',
    'no_files' => 'Aucun fichier téléversé',

    'attributes' => [],

];