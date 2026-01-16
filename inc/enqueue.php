<?php
/**
 * Enqueue frontend styles and fonts
 */

if (!defined('ABSPATH')) {
    exit;
}

function rakan_enqueue_assets() {
    wp_enqueue_style(
        'rakan-theme-style',
        get_template_directory_uri() . '/dist/main.css',
        [],
        '1.0.0'
    );

    wp_enqueue_style(
        'rakan-google-fonts',
        'https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Source+Serif+4:ital,opsz,wght@0,8..60,200..900;1,8..60,200..900&display=swap',
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'rakan_enqueue_assets');
