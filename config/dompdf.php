<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DomPDF Options
    |--------------------------------------------------------------------------
    */

    'show_warnings' => false,

    'orientation' => 'portrait',

    'defines' => [

        "font_dir" => storage_path('fonts'),
        "font_cache" => storage_path('fonts'),

        "temp_dir" => sys_get_temp_dir(),

        "chroot" => realpath(base_path()),

        "allowed_protocols" => [
            "file://" => ["rules" => []],
            "http://" => ["rules" => []],
            "https://" => ["rules" => []],
        ],

        "log_output_file" => null,

        "enable_font_subsetting" => true,

        "pdf_backend" => "CPDF",

        "default_media_type" => "screen",

        "default_paper_size" => "a4",

        "default_font" => "NotoSansTamil",

        "dpi" => 96,

        "enable_php" => false,

        "enable_javascript" => true,

        "enable_remote" => true,

        "font_height_ratio" => 1.1,
    ],
];
