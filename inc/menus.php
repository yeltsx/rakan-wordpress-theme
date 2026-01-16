<?php
/**
 * Register navigation menus
 */

if (!defined('ABSPATH')) {
    exit;
}

function rakan_register_menus() {
    register_nav_menus([
        'primary' => __('Primary Menu', 'rakan'),
        'footer'  => __('Footer Menu', 'rakan'),
    ]);
}
add_action('after_setup_theme', 'rakan_register_menus');
