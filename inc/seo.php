<?php
/**
 * SEO, Structured Data and Accessibility
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/* SEO + OPEN GRAPH */

function rakan_seo_meta_tags(): void {
    if (is_admin()) {
        return;
    }

    global $post;

    $site_name  = get_bloginfo('name');
    $site_desc  = get_bloginfo('description');
    $title      = wp_get_document_title();
    $url        = is_singular() ? get_permalink() : home_url('/');
    $type       = is_singular() ? 'article' : 'website';

    $description = is_singular() && has_excerpt($post)
        ? wp_strip_all_tags(get_the_excerpt($post))
        : $site_desc;

    echo '<meta name="description" content="' . esc_attr($description) . '">' . PHP_EOL;

    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . PHP_EOL;
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . PHP_EOL;
    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . PHP_EOL;
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . PHP_EOL;
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . PHP_EOL;

    if (is_singular() && has_post_thumbnail($post)) {
        echo '<meta property="og:image" content="' . esc_url(get_the_post_thumbnail_url($post, 'full')) . '">' . PHP_EOL;
    }
}
add_action('wp_head', 'rakan_seo_meta_tags', 5);

/* ACCESSIBILITY: AUTO ALT FOR IMAGES */

function rakan_auto_alt_images(string $content): string {
    if (is_admin() || trim($content) === '') {
        return $content;
    }

    libxml_use_internal_errors(true);

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadHTML(
        mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'),
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );

    foreach ($dom->getElementsByTagName('img') as $img) {
        if (!$img->hasAttribute('alt') || trim($img->getAttribute('alt')) === '') {
            $img->setAttribute('alt', esc_attr(get_the_title()));
        }
    }

    return $dom->saveHTML();
}
add_filter('the_content', 'rakan_auto_alt_images', 20);

/* ACCESSIBILITY + SEO: TITLE ATTRIBUTE ON POST LINKS */

function rakan_title_on_post_links(string $content): string {
    if (!is_home() && !is_archive()) {
        return $content;
    }

    return preg_replace_callback(
        '/<a\s+([^>]+)>(.*?)<\/a>/is',
        function (array $m): string {
            if (str_contains($m[1], 'title=')) {
                return $m[0];
            }

            $text = trim(wp_strip_all_tags($m[2]));
            if ($text === '') {
                return $m[0];
            }

            $title = sprintf(
                __('Click to read about %s', 'rakan'),
                $text
            );

            return '<a ' . $m[1] . ' title="' . esc_attr($title) . '">' . $m[2] . '</a>';
        },
        $content
    );
}
add_filter('the_content', 'rakan_title_on_post_links', 15);

/* JSON-LD: WEBSITE + SEARCH */

function rakan_schema_website(): void {
    if (!is_front_page()) {
        return;
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => get_bloginfo('name'),
        'url'      => home_url('/'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => home_url('/?s={search_term_string}'),
            'query-input' => 'required name=search_term_string',
        ],
    ];

    echo '<script type="application/ld+json">';
    echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo '</script>';
}
add_action('wp_head', 'rakan_schema_website', 20);

/* JSON-LD: ARTICLE / BLOG POSTING */

function rakan_schema_article(): void {
    if (!is_singular('post')) {
        return;
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'BlogPosting',
        'headline' => get_the_title(),
        'datePublished' => get_the_date(DATE_ATOM),
        'dateModified'  => get_the_modified_date(DATE_ATOM),
        'author' => [
            '@type' => 'Person',
            'name'  => get_the_author(),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => get_site_icon_url(),
            ],
        ],
        'mainEntityOfPage' => get_permalink(),
    ];

    if (has_post_thumbnail()) {
        $schema['image'] = get_the_post_thumbnail_url(null, 'full');
    }

    echo '<script type="application/ld+json">';
    echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo '</script>';
}
add_action('wp_head', 'rakan_schema_article', 25);

/* JSON-LD: BREADCRUMB */

function rakan_schema_breadcrumb(): void {
    if (!is_singular()) {
        return;
    }

    $items = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __('Home', 'rakan'),
            'item' => home_url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => get_the_title(),
            'item' => get_permalink(),
        ],
    ];

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => $items,
    ];

    echo '<script type="application/ld+json">';
    echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo '</script>';
}
add_action('wp_head', 'rakan_schema_breadcrumb', 30);

/* ACCESSIBILITY: LANGUAGE ATTRIBUTE */

add_filter('language_attributes', function (string $output): string {
    return str_contains($output, 'lang=')
        ? $output
        : $output . ' lang="' . get_bloginfo('language') . '"';
});
