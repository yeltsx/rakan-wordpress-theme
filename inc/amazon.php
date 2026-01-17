<?php

// Shortcode para produtos Amazon (apenas manual)
function amazon_product_card($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'title' => '',
        'description' => '',
        'price' => '',
        'image' => '',
    ), $atts);
    
    if (empty($atts['url']) || empty($atts['title']) || empty($atts['image'])) {
        return '<p style="color: var(--rakan-error);">Dados incompletos do produto Amazon.</p>';
    }
    
    ob_start();
    ?>
    <div class="amazon-product-card">
        <a href="<?php echo esc_url($atts['url']); ?>" target="_blank" rel="noopener noreferrer nofollow sponsored" class="amazon-product-link">
            <div class="amazon-product-image">
                <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['title']); ?>" loading="lazy">
            </div>
            <div class="amazon-product-info">
                <h3 class="amazon-product-title"><?php echo esc_html($atts['title']); ?></h3>
                
                <?php if (!empty($atts['description'])) : ?>
                    <p class="amazon-product-description"><?php echo esc_html($atts['description']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($atts['price'])) : ?>
                    <p class="amazon-product-price"><?php echo esc_html($atts['price']); ?></p>
                <?php endif; ?>
                
                <span class="amazon-buy-button">
                    Ver na Amazon
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </span>

                <span class="affiliate">Este link pode conter afiliados. Você como cliente não paga nada mais por isso, mas nós podemos receber uma comissão pela venda.</span>
            </div>
        </a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('amazon', 'amazon_product_card');

// Adiciona botão ao TinyMCE
function amazon_add_tinymce_button() {
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }
    
    if (get_user_option('rich_editing') == 'true') {
        add_filter('mce_external_plugins', 'amazon_add_tinymce_plugin');
        add_filter('mce_buttons', 'amazon_register_button');
    }
}
add_action('admin_init', 'amazon_add_tinymce_button');

function amazon_register_button($buttons) {
    array_push($buttons, 'amazon_product');
    return $buttons;
}

function amazon_add_tinymce_plugin($plugin_array) {
    $plugin_array['amazon_product'] = get_template_directory_uri() . '/js/amazon-tinymce.js';
    return $plugin_array;
}

// Adiciona CSS para o popup
function amazon_tinymce_css() {
    ?>
    <style>
        .amazon-mce-popup {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .amazon-mce-popup h2 {
            margin: 0 0 20px 0;
            font-size: 18px;
            color: #23282d;
        }
        .amazon-mce-popup .form-field {
            margin-bottom: 15px;
        }
        .amazon-mce-popup label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 13px;
            color: #23282d;
        }
        .amazon-mce-popup input[type="text"],
        .amazon-mce-popup input[type="url"],
        .amazon-mce-popup textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 13px;
        }
        .amazon-mce-popup textarea {
            resize: vertical;
            min-height: 60px;
        }
        .amazon-mce-popup .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }
        .amazon-mce-popup .button-group {
            margin-top: 20px;
            text-align: right;
        }
        .amazon-mce-popup button {
            padding: 8px 15px;
            margin-left: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
        }
        .amazon-mce-popup .btn-cancel {
            background: #f1f1f1;
            color: #555;
        }
        .amazon-mce-popup .btn-insert {
            background: #FF9900;
            color: #000;
            font-weight: 600;
        }
        .amazon-mce-popup .btn-insert:hover {
            background: #F08000;
        }
    </style>
    <?php
}
add_action('admin_head', 'amazon_tinymce_css');

