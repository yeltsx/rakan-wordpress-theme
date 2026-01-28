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

// ========================================
// Galeria WordPress Customizada
// ========================================

// Remove galeria padrão e adiciona customizada
remove_shortcode('gallery');
add_shortcode('gallery', 'custom_gallery_shortcode');

function custom_gallery_shortcode($attr) {
    $post = get_post();

    static $instance = 0;
    $instance++;

    $attr = shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post ? $post->ID : 0,
        'size'       => 'large',
        'include'    => '',
        'exclude'    => '',
        'link'       => 'file',
        'columns'    => 3
    ), $attr, 'gallery');

    $id = intval($attr['id']);

    if (!empty($attr['include'])) {
        $attachments = get_posts(array(
            'include'        => $attr['include'],
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => $attr['order'],
            'orderby'        => $attr['orderby']
        ));
    } elseif (!empty($attr['exclude'])) {
        $attachments = get_children(array(
            'post_parent'    => $id,
            'exclude'        => $attr['exclude'],
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => $attr['order'],
            'orderby'        => $attr['orderby']
        ));
    } else {
        $attachments = get_children(array(
            'post_parent'    => $id,
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => $attr['order'],
            'orderby'        => $attr['orderby']
        ));
    }

    if (empty($attachments)) {
        return '';
    }

    $columns = intval($attr['columns']);
    $columns = max(1, min(4, $columns)); // Limita entre 1 e 4 colunas

    $output = '<div class="rakan-gallery rakan-gallery-columns-' . $columns . '" data-gallery-id="' . $instance . '">';

    foreach ($attachments as $attachment) {
        $image_full = wp_get_attachment_image_src($attachment->ID, 'full');
        $image_thumb = wp_get_attachment_image_src($attachment->ID, 'large');
        $image_alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
        $image_caption = wp_get_attachment_caption($attachment->ID);

        $output .= '<div class="rakan-gallery-item">';
        $output .= '<a href="' . esc_url($image_full[0]) . '" class="rakan-gallery-link" data-lightbox="gallery-' . $instance . '" data-title="' . esc_attr($image_caption) . '">';
        $output .= '<img src="' . esc_url($image_thumb[0]) . '" alt="' . esc_attr($image_alt) . '" loading="lazy">';
        if ($image_caption) {
            $output .= '<div class="rakan-gallery-caption">' . esc_html($image_caption) . '</div>';
        }
        $output .= '</a>';
        $output .= '</div>';
    }

    $output .= '</div>';

    return $output;
}

// Adiciona lightbox script
function enqueue_lightbox_scripts() {
    if (is_singular()) {
        wp_enqueue_script('rakan-lightbox', get_template_directory_uri() . '/js/lightbox.js', array(), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_lightbox_scripts');

/**
 * Replace the default <!-- more --> link markup
 * with a custom "read more" link.
 */
function custom_more_link($link) {
    global $post;

    $url = get_permalink($post->ID);

    return sprintf(
        '<a href="%s" class="read-more-link">mais <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>',
        esc_url($url)
    );
}

add_filter('the_content_more_link', 'custom_more_link');
