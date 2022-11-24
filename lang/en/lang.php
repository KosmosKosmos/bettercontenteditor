<?php

return [
    'plugin' => [
        'name'    => 'Content Editor',
        'description' => 'Front-end content editor'
    ],
    'settings' => [
        'main_settings'           => 'Main settings',
        'style_palettes'          => 'Style palettes',
        'style_palettes_desc'     => 'Set your style palettes and reuse them on any content. In tab Additional styles you can define CSS/LESS for style palettes.',
        'buttons'                 => 'Buttons',
        'permissions'             => 'Permissions',
        'permissions_comment'     => 'Show Content Editor only for',
        'image_folder'            => 'Images folder name',
        'image_folder_comment'    => 'Upload images in MediaLibrary folder',
        'name'                    => 'Name',
        'class'                   => 'Class',
        'allowed_tags'            => 'Allowed tags',
        'additional_styles'       => 'Additional CSS',
        'additional_styles_label' => 'Addional styles that are included in website. Possibility to use CSS or LESS.'
    ],
    'styles' => [
        'bold'           => 'Bold (b)',
        'italic'         => 'Italic (i)',
        'link'           => 'Link (a)',

        'align-left'     => 'Align left',
        'align-center'   => 'Align center',
        'align-right'    => 'Align right',

        'heading'        => 'Heading (h1)',
        'subheading'     => 'Subheading (h2)',

        'subheading3'    => 'Subheading3 (h3)',
        'subheading4'    => 'Subheading4 (h4)',
        'subheading5'    => 'Subheading5 (h5)',

        'paragraph'      => 'Paragraph (p)',
        'unordered-list' => 'Unordered list (ul)',
        'ordered-list'   => 'Ordered list (ol)',

        'table'          => 'Table',
        'indent'         => 'Indent',
        'unindent'       => 'Unindent',
        'line-break'     => 'Line-break (br)',

        'image'          => 'Image upload',
        'video'          => 'Video',
        'preformatted'   => 'Preformatted (pre)',
    ],
    'translations' => [
        'addContent' => 'add content here',
        'changesSaved' => 'Your changes have been saved',
        'changesLost' => 'Your changes will be lost. Are you sure?',
        'yes' => 'Yes',
        'no' => 'No',
        'current' => 'Current'
    ],
    'imageuploader' => [
        'upload_first' => 'Drag a new picture here or',
        'upload_second' => 'select one',
        'success' => 'Image was saved'
    ]
];
