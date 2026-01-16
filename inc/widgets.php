<?php
/**
 * Register widget areas
 */

if (!defined('ABSPATH')) {
    exit;
}

function rakan_footer_widgets() {
    register_sidebar([
        'name'          => __('Footer Widget Area', 'rakan'),
        'id'            => 'footer-widget',
        'description'   => __('Add text or HTML widgets here.', 'rakan'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'rakan_footer_widgets');
