<?php

return [
    'asset_url' => null,
    'app_url' => null,
    'path' => 'livewire',
    'asset_base_url' => null,
    'middleware_group' => ['web'],
    'layout' => 'layouts.app',
    'temporary_file_upload' => [
        'disk' => null,        
        'rules' => null,
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [   
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5, 
    ],
    'manifest_path' => null,
    'back_button_cache' => false,
    'render_on_redirect' => false,
    'class_namespace' => 'App\\Livewire',
    'legacy_model_binding' => false,
    
    'inject_assets' => true,
    
    'inject_morph_markers' => true,
    
    'navigate' => [
        'show_progress_bar' => true,
    ],
];