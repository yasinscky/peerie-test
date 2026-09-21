<?php

return [
    'version' => [
        'tiny' => '7.3.0',
        'language' => [
            'version' => '24.7.29',
            'package' => 'langs7',
        ],
        'licence_key' => env('TINY_LICENSE_KEY', 'no-api-key'),
    ],
    'provider' => 'cloud',
    'darkMode' => 'auto',
    'license_key' => 'gpl',
    'skins' => [
        'ui' => 'oxide',
        'content' => 'default',
    ],
    'profiles' => [
        'default' => [
            'plugins' => 'accordion autoresize codesample directionality advlist link image lists preview pagebreak searchreplace wordcount code fullscreen insertdatetime media table emoticons',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize fontsizeinput font_size_formats styles | bold italic underline | rtl ltr | alignjustify alignleft aligncenter alignright | numlist bullist outdent indent | forecolor backcolor | blockquote table toc hr | image link media codesample emoticons | wordcount fullscreen',
            'upload_directory' => null,
        ],
        'simple' => [
            'plugins' => 'autoresize directionality emoticons link wordcount',
            'toolbar' => 'removeformat | bold italic | rtl ltr | numlist bullist | link emoticons',
            'upload_directory' => null,
        ],
        'minimal' => [
            'plugins' => 'link wordcount',
            'toolbar' => 'bold italic link numlist bullist',
            'upload_directory' => null,
        ],
        'full' => [
            'plugins' => 'accordion autoresize codesample directionality advlist autolink link image lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media table emoticons help',
            'toolbar' => 'undo redo removeformat | fontfamily fontsize fontsizeinput font_size_formats styles | bold italic underline | rtl ltr | alignjustify alignright aligncenter alignleft | numlist bullist outdent indent accordion | forecolor backcolor | blockquote table toc hr | image link anchor media codesample emoticons | visualblocks print preview wordcount fullscreen help',
            'upload_directory' => null,
        ],
        'instruction' => [
            'plugins' => 'autoresize advlist autolink link image lists table',
            'toolbar' => 'undo redo | styles | bold italic underline strikethrough | bullist numlist | blockquote | table | link image | removeformat',
            'upload_directory' => 'task-instructions',
        ],
    ],
    'languages' => [],
    'extra' => [
        'content_style' => 'table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; } th { background-color: #f3f4f6; font-weight: 600; }',
        'toolbar' => [],
    ],
];
