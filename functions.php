<?php
/**
 * Functions and definitions
 */

function rakan_theme_scripts() {
  wp_enqueue_style('theme-style', get_template_directory_uri() . '/dist/main.css', array(), '1.0.0');
  wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Source+Serif+4:ital,opsz,wght@0,8..60,200..900;1,8..60,200..900&display=swap', array(), null);
}
add_action('wp_enqueue_scripts', 'rakan_theme_scripts');

function rakan_register_menu() {
    register_nav_menus(array(
        'primary' => 'Primary Menu',
        'footer'  => 'Footer Menu'
    ));
}
add_action('after_setup_theme', 'rakan_register_menu');

function rakan_social_networks($wp_customize) {
    $wp_customize->add_section('redes_sociais', array(
        'title' => 'Redes Sociais',
        'priority' => 30,
    ));
    
    $redes = array('bluesky', 'instagram', 'discord');
    foreach ($redes as $rede) {
        $wp_customize->add_setting($rede . '_url', array('default' => '#'));
        $wp_customize->add_control($rede . '_url', array(
            'label' => ucfirst($rede) . ' URL',
            'section' => 'redes_sociais',
            'type' => 'url',
        ));
    }
}
add_action('customize_register', 'rakan_social_networks');

function rakan_widgets_footer() {
    register_sidebar(array(
        'name'          => 'Widget do Rodapé',
        'id'            => 'footer-widget',
        'description'   => 'Adicione um widget de texto ou HTML aqui',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'rakan_widgets_footer');

// Registrar tamanho de thumbnail otimizado
add_theme_support('post-thumbnails');
add_image_size('post-thumbnail', 960, 300, true); // Crop para 960x300

// Otimização de imagens - reduzir qualidade JPEG
add_filter('jpeg_quality', function($quality) {
    return 82; // Reduz de 90 para 82
});

add_filter('wp_editor_set_quality', function($quality) {
    return 82;
});

// Adicionar suporte a lazy loading nativo
function add_lazy_loading_to_images($content) {
    if (is_feed() || is_preview()) {
        return $content;
    }
    return str_replace('<img', '<img loading="lazy"', $content);
}
add_filter('the_content', 'add_lazy_loading_to_images');

// Comprimir imagens ao fazer upload (WebP se disponível)
function compress_uploaded_images($image_data) {
    $image_editor = wp_get_image_editor($image_data['file']);
    
    if (!is_wp_error($image_editor)) {
        $image_editor->set_quality(82);
        $image_editor->save($image_data['file']);
    }
    
    return $image_data;
}
add_filter('wp_generate_attachment_metadata', 'compress_uploaded_images');

// Remover tamanhos de imagem desnecessários para economizar espaço
function remove_default_image_sizes($sizes) {
    unset($sizes['medium_large']);
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'remove_default_image_sizes');

// Habilitar suporte a WebP (WordPress 5.8+)
function enable_webp_upload($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('mime_types', 'enable_webp_upload');

/* Block Styling */
add_action('after_setup_theme', function () {
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
});

add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_style(
        'rakan-editor-fonts',
        'https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;600;700&family=Libre+Baskerville:wght@400;700&family=JetBrains+Mono:wght@400;600&display=swap',
        [],
        null
    );
});


?>