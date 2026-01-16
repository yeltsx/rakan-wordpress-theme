<?php
/**
 * Image optimization and handling
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Reduce JPEG quality
 */
add_filter('jpeg_quality', fn () => 82);
add_filter('wp_editor_set_quality', fn () => 82);

/**
 * Add native lazy loading to content images
 */
function rakan_add_lazy_loading($content) {
    if (is_feed() || is_preview()) {
        return $content;
    }

    return preg_replace(
        '/<img(?![^>]+loading=)/i',
        '<img loading="lazy"',
        $content
    );
}
add_filter('the_content', 'rakan_add_lazy_loading');

/**
 * Compress images on upload
 */
function rakan_compress_uploaded_images($metadata, $attachment_id) {
    $file = get_attached_file($attachment_id);
    $editor = wp_get_image_editor($file);

    if (!is_wp_error($editor)) {
        $editor->set_quality(82);
        $editor->save($file);
    }

    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'rakan_compress_uploaded_images', 10, 2);

/**
 * Remove unnecessary image sizes
 */
function rakan_remove_default_image_sizes($sizes) {
    unset($sizes['medium_large'], $sizes['1536x1536'], $sizes['2048x2048']);
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'rakan_remove_default_image_sizes');

/**
 * Enable WebP uploads
 */
add_filter('mime_types', function ($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
});
