<?php

return [
    'plugin' => [
        'name'    => 'Content Editor',
        'description' => 'Front-end content editor'
    ],
    'settings' => [
        'main_settings'           => 'Haupteinstellungen',
        'options'                 => 'Auswahlmöglichkeiten',
        'options_comment'         => 'Lege fest, welche Möglichkeiten der Nutzer im Frontend hat.',
        'style_palettes'          => 'Stil-Palette',
        'style_palettes_desc'     => 'Lege  Stilpaletten fest und verwende sie für beliebige Inhalte wieder. Im Tab Zusätzliche Stile kannst du CSS/LESS für Stilpaletten definieren.',
        'buttons'                 => 'Buttons',
        'permissions'             => 'Berechtigungen',
        'permissions_comment'     => 'Zeige Content-Editor nur für',
        'image_folder'            => 'Name für Bilder-Ordner',
        'image_folder_comment'    => 'Lade Bilder in die Medien-Bibliothek',
        'default_image'           => 'Standardbild',
        'default_image_commment'  => 'Lade ein anderes Bild als Standard fest',
        'name'                    => 'Name',
        'class'                   => 'Klasse',
        'allowed_tags'            => 'Erlaube Tags',
        'additional_styles'       => 'Zusätzliche CSS',
        'additional_styles_label' => 'Zusätzliche Stile, die in der Website enthalten sind. Möglichkeit, CSS oder LESS zu verwenden.'
    ],
    'styles' => [
        'bold'           => 'Fett (b)',
        'italic'         => 'Kursiv (i)',
        'link'           => 'Link (a)',

        'align-left'     => 'Links ausrichten',
        'align-center'   => 'Zentriert ausrichten',
        'align-right'    => 'Rechts ausrichten',

        'heading'        => 'Überschrift (h1)',
        'subheading'     => 'Unterüberschrift (h2)',

        'subheading3'    => 'Unterüberschrift3 (h3)',
        'subheading4'    => 'Unterüberschrift4 (h4)',
        'subheading5'    => 'Unterüberschrift5 (h5)',

        'paragraph'      => 'Abschnitt (p)',
        'unordered-list' => 'Liste (ul)',
        'ordered-list'   => 'Aufzählung (ol)',

        'table'          => 'Tabelle',
        'indent'         => 'Einrücken',
        'unindent'       => 'Zurück rücken',
        'line-break'     => 'Zeilenumbruch (br)',

        'image'          => 'Bild',
        'video'          => 'Video',
        'preformatted'   => 'Unformatiert (pre)',
    ],
    'translations' => [
        'addContent' => 'hier Inhalt eingeben',
        'changesSaved' => 'Ihre Änderungen wurden gespeichert',
        'changesLost' => 'Ihre Ändernugen gehen verloren. Sind Sie sicher?',
        'yes' => 'Ja',
        'no' => 'Nein',
        'current' => 'Aktuell'
    ],
    'imageuploader' => [
        'upload_first' => 'Hier ein neues Bild hinziehen oder',
        'upload_second' => 'eines auswählen',
        'success' => 'Bild wurde gespeichert'
    ]
];
