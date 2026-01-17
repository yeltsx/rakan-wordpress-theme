<?php
/**
 * Lighthouse SEO, Accessibility and Best Practices fixes
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/* HTML COMPRESSION */

function rakan_start_html_compression(): void {
    if (is_admin() || defined('REST_REQUEST') || is_feed() || is_preview()) {
        return;
    }

    ob_start('rakan_compress_html');
}
add_action('template_redirect', 'rakan_start_html_compression');

function rakan_compress_html(string $html): string {
    if (trim($html) === '') {
        return $html;
    }

    $preserve = [];
    $i = 0;

    $html = preg_replace_callback(
        '#<(pre|code|textarea)[^>]*>.*?</\1>#is',
        function ($matches) use (&$preserve, &$i) {
            $key = "###HTML_PRESERVE_{$i}###";
            $preserve[$key] = $matches[0];
            $i++;
            return $key;
        },
        $html
    );

    $html = preg_replace('/\s{2,}/', ' ', $html);
    $html = preg_replace('/>\s+</', '><', $html);
    $html = str_replace(["\n", "\r", "\t"], '', $html);

    if (!empty($preserve)) {
        $html = str_replace(array_keys($preserve), array_values($preserve), $html);
    }

    return $html;
}

/* EXPIRE HEADERS FOR STATIC ASSETS */

function rakan_set_asset_cache_headers(): void {
    if (headers_sent()) {
        return;
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $extension = pathinfo(parse_url($uri, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);

    if ($extension === '') {
        return;
    }

    $cacheable = [
        'css', 'js',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'woff', 'woff2', 'ttf', 'otf'
    ];

    if (!in_array($extension, $cacheable, true)) {
        return;
    }

    $max_age = 31536000;

    header('Cache-Control: public, max-age=' . $max_age . ', immutable');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . ' GMT');
}
add_action('send_headers', 'rakan_set_asset_cache_headers');

/* NON-BLOCKING CSS */

function rakan_non_blocking_styles(string $html, string $handle, string $href, string $media): string {
    if (is_admin()) {
        return $html;
    }

    if ($handle === 'theme-style') {
        return '<link rel="stylesheet" href="' . esc_url($href) . '" media="print" onload="this.media=\'all\'">' .
               '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>';
    }

    return $html;
}
add_filter('style_loader_tag', 'rakan_non_blocking_styles', 10, 4);

/* DEFER JAVASCRIPT (FRONT ONLY) */

function rakan_defer_scripts(string $tag, string $handle, string $src): string {
    if (is_admin()) {
        return $tag;
    }

    $exclude = [
        'jquery-core',
        'jquery-migrate',
        'inline',
    ];

    foreach ($exclude as $blocked) {
        if (str_contains($handle, $blocked)) {
            return $tag;
        }
    }

    if (str_contains($tag, 'defer') || str_contains($tag, 'async')) {
        return $tag;
    }

    return str_replace(' src=', ' defer src=', $tag);
}
add_filter('script_loader_tag', 'rakan_defer_scripts', 10, 3);

function rakan_optimize_images_loading(string $content): string {
    if (is_admin() || trim($content) === '') {
        return $content;
    }

    libxml_use_internal_errors(true);

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadHTML(
        mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'),
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );

    $images = $dom->getElementsByTagName('img');

    $is_first_image = true;

    foreach ($images as $img) {
        if (!$img->hasAttribute('src')) {
            continue;
        }

        if ($is_first_image) {
            $img->setAttribute('fetchpriority', 'high');
            $img->removeAttribute('loading');
            $is_first_image = false;
            continue;
        }

        if (!$img->hasAttribute('loading')) {
            $img->setAttribute('loading', 'lazy');
        }
    }

    return $dom->saveHTML();
}
add_filter('the_content', 'rakan_optimize_images_loading', 25);

/* Ensure featured image gets high priority */

function rakan_featured_image_fetchpriority(array $attr): array {
    if (is_admin()) {
        return $attr;
    }

    static $done = false;

    if ($done) {
        return $attr;
    }

    $attr['fetchpriority'] = 'high';
    unset($attr['loading']);

    $done = true;

    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'rakan_featured_image_fetchpriority', 10);

function rakan_preconnect_google_fonts(): void {
    if (is_admin()) {
        return;
    }

    static $done = false;
    if ($done) {
        return;
    }

    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . PHP_EOL;
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . PHP_EOL;

    $done = true;
}
add_action('wp_head', 'rakan_preconnect_google_fonts', 1);

/**
 * Enable WordPress automatic <title> tag
 */

add_action('after_setup_theme', function (): void {
    add_theme_support('title-tag');
});

add_filter('document_title_parts', function (array $title): array {
    if (is_home() || is_front_page()) {
        $title['tagline'] = get_bloginfo('description');
    }

    return $title;
});
