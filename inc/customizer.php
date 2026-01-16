<?php
/**
 * Theme Customizer settings
 */

if (!defined('ABSPATH')) {
    exit;
}

function rakan_social_networks_customizer($wp_customize) {

    $wp_customize->add_section('rakan_social_networks', [
        'title'    => __('Social Networks', 'rakan'),
        'priority' => 30,
    ]);

    $networks = ['bluesky', 'instagram', 'discord'];

    foreach ($networks as $network) {
        $wp_customize->add_setting("{$network}_url", [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control("{$network}_url", [
            'label'   => ucfirst($network) . ' URL',
            'section' => 'rakan_social_networks',
            'type'    => 'url',
        ]);
    }
}
add_action('customize_register', 'rakan_social_networks_customizer');
