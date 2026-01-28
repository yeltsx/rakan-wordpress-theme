<?php
/**
 * Theme bootstrap file
 * Loads all theme features in a modular way.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load theme feature files
 */
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/menus.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/widgets.php';
require_once get_template_directory() . '/inc/images.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/opt.php';
require_once get_template_directory() . '/inc/amazon.php';
require_once get_template_directory() . '/inc/goals.php';
require_once get_template_directory() . '/inc/products-table.php';
require_once get_template_directory() . '/inc/reviews.php';
require_once get_template_directory() . '/inc/dicas.php';


/**
 * Theme supports
 */
add_theme_support('post-thumbnails');
add_image_size('post-thumbnail', 960, 300, true);
