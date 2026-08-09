<?php

return [

    'show_warnings' => env('DOMPDF_SHOW_WARNINGS', false),

    'public_path' => public_path(),

    'convert_entities' => false,

    'options' => [

        'font_dir' => storage_path('fonts'),
        'font_cache' => storage_path('fonts'),

        'temp_dir' => sys_get_temp_dir(),

        'chroot' => realpath(base_path()),

        'allowed_protocols' => [
            'data://' => ['rules' => []],
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],

        'artifactPathValidation' => null,

        'log_output_file' => storage_path('logs/dompdf.html'),

        'enable_font_subsetting' => true,

        'pdf_backend' => 'CPDF',

        'default_media_type' => 'screen',

        'default_paper_size' => 'a4',

        'default_paper_orientation' => 'portrait',

        'default_font' => 'THSarabunNew',

        'dpi' => 96,

        'enable_php' => false,

        'enable_javascript' => false,

        'enable_remote' => env('DOMPDF_ENABLE_REMOTE', false),

        'allowed_remote_hosts' => null,

        'font_height_ratio' => 1.1,

        'enable_html5_parser' => true,
    ],

];