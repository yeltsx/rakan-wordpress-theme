<?php
/**
 * Block editor styles and assets
 */

if (!defined('ABSPATH')) {
    exit;
}

function rakan_editor_setup() {
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
}
add_action('after_setup_theme', 'rakan_editor_setup');

function rakan_editor_fonts() {
    wp_enqueue_style(
        'rakan-editor-fonts',
        'https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;600;700&family=Libre+Baskerville:wght@400;700&family=JetBrains+Mono:wght@400;600&display=swap',
        [],
        null
    );
}
add_action('enqueue_block_editor_assets', 'rakan_editor_fonts');
