<?php
/**
 * Sistema de Reviews para Tema Rakan
 * Adicione este código ao functions.php do tema child
 */

// Criar Custom Post Type NÃO-PÚBLICO para Reviews
function rakan_reviews_post_type() {
    register_post_type('review', array(
        'labels' => array(
            'name' => 'Reviews',
            'singular_name' => 'Review',
            'add_new' => 'Adicionar Review',
            'add_new_item' => 'Adicionar Novo Review',
            'edit_item' => 'Editar Review',
            'all_items' => 'Todos os Reviews',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-star-filled',
        'supports' => array('title'),
        'capability_type' => 'post',
    ));
}
add_action('init', 'rakan_reviews_post_type');

// Adicionar Meta Boxes
function rakan_reviews_meta_boxes() {
    add_meta_box(
        'review_main_info',
        'Informações Principais',
        'rakan_review_main_info_callback',
        'review',
        'normal',
        'high'
    );
    
    add_meta_box(
        'review_ratings',
        'Avaliações por Categoria',
        'rakan_review_ratings_callback',
        'review',
        'normal',
        'high'
    );
    
    add_meta_box(
        'review_pros_cons',
        'Prós e Contras',
        'rakan_review_pros_cons_callback',
        'review',
        'normal',
        'high'
    );
    
    add_meta_box(
        'review_shortcode',
        'Shortcode',
        'rakan_review_shortcode_callback',
        'review',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'rakan_reviews_meta_boxes');

// Callback - Shortcode Display
function rakan_review_shortcode_callback($post) {
    if ($post->ID) {
        echo '<div style="background: #fff; border: 1px solid #2271b1; padding: 15px; border-radius: 4px;">';
        echo '<p><strong>Use este shortcode para inserir o review:</strong></p>';
        echo '<code style="background: #f0f0f0; padding: 8px 12px; border-radius: 3px; font-size: 14px; user-select: all; display: block; margin-top: 10px;">[review id="' . $post->ID . '"]</code>';
        echo '<p style="margin-top: 10px; color: #666; font-size: 13px;">Cole este shortcode em qualquer página ou post.</p>';
        echo '</div>';
    } else {
        echo '<p style="color: #666;">Publique o review para gerar o shortcode.</p>';
    }
}

// Callback - Informações Principais
function rakan_review_main_info_callback($post) {
    wp_nonce_field('rakan_review_save', 'rakan_review_nonce');
    $final_score = get_post_meta($post->ID, '_review_final_score', true);
    $button_text = get_post_meta($post->ID, '_review_button_text', true);
    $button_link = get_post_meta($post->ID, '_review_button_link', true);
    ?>
    <style>
        .review-admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .review-admin-field {
            display: flex;
            flex-direction: column;
        }
        .review-admin-field label {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .review-admin-field input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .review-final-score-preview {
            text-align: center;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            margin-top: 10px;
        }
        .score-number {
            font-size: 48px;
            font-weight: 700;
            color: #e25507;
            line-height: 1;
        }
        .score-stars {
            font-size: 32px;
            color: #ffb800;
            margin-top: 10px;
        }
    </style>
    
    <div class="review-admin-grid">
        <div class="review-admin-field">
            <label>Nota Final (0-5) *</label>
            <input type="number" 
                   name="final_score" 
                   id="final_score_input"
                   value="<?php echo esc_attr($final_score); ?>" 
                   min="0" 
                   max="5" 
                   step="0.1"
                   required>
            <div class="review-final-score-preview">
                <div class="score-number"><?php echo $final_score ?: '0.0'; ?></div>
                <div class="score-stars" id="score_stars_preview"></div>
            </div>
            <button type="button" id="calculate_average" class="button" style="margin-top: 10px;">
                📊 Calcular Média das Categorias
            </button>
        </div>
        
        <div class="review-admin-field">
            <label>Texto do Botão</label>
            <input type="text" 
                   name="button_text" 
                   value="<?php echo esc_attr($button_text); ?>" 
                   placeholder="Ex: Ver Melhor Preço">
            
            <label style="margin-top: 15px;">Link do Botão</label>
            <input type="url" 
                   name="button_link" 
                   value="<?php echo esc_attr($button_link); ?>" 
                   placeholder="https://">
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        function renderStars(score) {
            const fullStars = Math.floor(score);
            const hasHalf = (score % 1) >= 0.5;
            let html = '';
            
            for (let i = 0; i < 5; i++) {
                if (i < fullStars) {
                    html += '★';
                } else if (i === fullStars && hasHalf) {
                    html += '⯨';
                } else {
                    html += '☆';
                }
            }
            return html;
        }
        
        function updateScorePreview() {
            const score = parseFloat($('#final_score_input').val()) || 0;
            $('.score-number').text(score.toFixed(1));
            $('#score_stars_preview').html(renderStars(score));
        }
        
        $('#final_score_input').on('input', updateScorePreview);
        updateScorePreview();
        
        // Calcular média
        $('#calculate_average').on('click', function() {
            const scores = [];
            $('input[name*="[score]"]').each(function() {
                const val = parseFloat($(this).val());
                if (!isNaN(val)) {
                    scores.push(val);
                }
            });
            
            if (scores.length > 0) {
                const average = scores.reduce((a, b) => a + b, 0) / scores.length;
                $('#final_score_input').val(average.toFixed(1));
                updateScorePreview();
            } else {
                alert('Adicione avaliações por categoria primeiro!');
            }
        });
    });
    </script>
    <?php
}

// Callback - Avaliações por Categoria
function rakan_review_ratings_callback($post) {
    $ratings = get_post_meta($post->ID, '_review_ratings', true) ?: array();
    ?>
    <style>
        .review-rating-item {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .review-rating-fields {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 10px;
            align-items: center;
        }
        .review-rating-fields input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .remove-rating {
            background: #dc3232;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 3px;
            cursor: pointer;
        }
        .add-rating-btn {
            background: #2271b1;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
            margin-top: 10px;
        }
        .star-rating-display {
            color: #ffb800;
            font-size: 18px;
            margin-top: 5px;
        }
    </style>
    
    <div id="review-ratings-container">
        <?php foreach ($ratings as $index => $rating): ?>
        <div class="review-rating-item">
            <div class="review-rating-fields">
                <input type="text" 
                       name="ratings[<?php echo $index; ?>][label]" 
                       value="<?php echo esc_attr($rating['label']); ?>" 
                       placeholder="Nome da categoria (ex: Desempenho)" 
                       required>
                <input type="number" 
                       name="ratings[<?php echo $index; ?>][score]" 
                       value="<?php echo esc_attr($rating['score']); ?>" 
                       min="0" 
                       max="5" 
                       step="0.1" 
                       placeholder="0-5" 
                       class="rating-score-input"
                       required>
                <button type="button" class="remove-rating">Remover</button>
            </div>
            <div class="star-rating-display"></div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <button type="button" class="add-rating-btn" id="add-rating">+ Adicionar Categoria</button>
    
    <script>
    jQuery(document).ready(function($) {
        let ratingIndex = <?php echo count($ratings); ?>;
        
        function renderStars(score) {
            const fullStars = Math.floor(score);
            const hasHalf = (score % 1) >= 0.5;
            let html = '';
            
            for (let i = 0; i < 5; i++) {
                if (i < fullStars) {
                    html += '★';
                } else if (i === fullStars && hasHalf) {
                    html += '⯨';
                } else {
                    html += '☆';
                }
            }
            
            return html + ' (' + score.toFixed(1) + ')';
        }
        
        function updateStarDisplays() {
            $('.review-rating-item').each(function() {
                const $input = $(this).find('.rating-score-input');
                const $display = $(this).find('.star-rating-display');
                const score = parseFloat($input.val()) || 0;
                $display.html(renderStars(score));
            });
        }
        
        $(document).on('input', '.rating-score-input', updateStarDisplays);
        
        $('#add-rating').on('click', function() {
            const newItem = `
                <div class="review-rating-item">
                    <div class="review-rating-fields">
                        <input type="text" name="ratings[${ratingIndex}][label]" placeholder="Nome da categoria" required>
                        <input type="number" name="ratings[${ratingIndex}][score]" min="0" max="5" step="0.1" placeholder="0-5" class="rating-score-input" required>
                        <button type="button" class="remove-rating">Remover</button>
                    </div>
                    <div class="star-rating-display"></div>
                </div>
            `;
            $('#review-ratings-container').append(newItem);
            ratingIndex++;
            updateStarDisplays();
        });
        
        $(document).on('click', '.remove-rating', function() {
            $(this).closest('.review-rating-item').remove();
        });
        
        updateStarDisplays();
    });
    </script>
    <?php
}

// Callback - Prós e Contras
function rakan_review_pros_cons_callback($post) {
    $pros = get_post_meta($post->ID, '_review_pros', true) ?: array();
    $cons = get_post_meta($post->ID, '_review_cons', true) ?: array();
    ?>
    <style>
        .pros-cons-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .pros-section, .cons-section {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .pros-section {
            border-left: 4px solid #4ade80;
        }
        .cons-section {
            border-left: 4px solid #f87171;
        }
        .pros-cons-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .pros-cons-item input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .remove-item-btn {
            background: #dc3232;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 3px;
            cursor: pointer;
        }
        .add-item-btn {
            background: #2271b1;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 3px;
            cursor: pointer;
            margin-top: 10px;
        }
        .section-title {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
    
    <div class="pros-cons-container">
        <div class="pros-section">
            <div class="section-title">✓ Prós</div>
            <div id="pros-list">
                <?php foreach ($pros as $index => $pro): ?>
                <div class="pros-cons-item">
                    <input type="text" 
                           name="pros[]" 
                           value="<?php echo esc_attr($pro); ?>" 
                           placeholder="Adicione um ponto positivo">
                    <button type="button" class="remove-item-btn">×</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-item-btn add-pro">+ Adicionar Pró</button>
        </div>
        
        <div class="cons-section">
            <div class="section-title">✗ Contras</div>
            <div id="cons-list">
                <?php foreach ($cons as $index => $con): ?>
                <div class="pros-cons-item">
                    <input type="text" 
                           name="cons[]" 
                           value="<?php echo esc_attr($con); ?>" 
                           placeholder="Adicione um ponto negativo">
                    <button type="button" class="remove-item-btn">×</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-item-btn add-con">+ Adicionar Contra</button>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.add-pro').on('click', function() {
            const newItem = `
                <div class="pros-cons-item">
                    <input type="text" name="pros[]" placeholder="Adicione um ponto positivo">
                    <button type="button" class="remove-item-btn">×</button>
                </div>
            `;
            $('#pros-list').append(newItem);
        });
        
        $('.add-con').on('click', function() {
            const newItem = `
                <div class="pros-cons-item">
                    <input type="text" name="cons[]" placeholder="Adicione um ponto negativo">
                    <button type="button" class="remove-item-btn">×</button>
                </div>
            `;
            $('#cons-list').append(newItem);
        });
        
        $(document).on('click', '.remove-item-btn', function() {
            $(this).closest('.pros-cons-item').remove();
        });
    });
    </script>
    <?php
}

// Salvar dados do review
function rakan_save_review($post_id) {
    if (!isset($_POST['rakan_review_nonce'])) return;
    if (!wp_verify_nonce($_POST['rakan_review_nonce'], 'rakan_review_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Salvar nota final
    if (isset($_POST['final_score'])) {
        update_post_meta($post_id, '_review_final_score', floatval($_POST['final_score']));
    }

    // Salvar botão
    if (!empty($_POST['button_text'])) {
        update_post_meta($post_id, '_review_button_text', sanitize_text_field($_POST['button_text']));
    } else {
        delete_post_meta($post_id, '_review_button_text');
    }

    if (!empty($_POST['button_link'])) {
        update_post_meta($post_id, '_review_button_link', esc_url_raw($_POST['button_link']));
    } else {
        delete_post_meta($post_id, '_review_button_link');
    }

    // Salvar avaliações
    if (isset($_POST['ratings'])) {
        $ratings = array();
        foreach ($_POST['ratings'] as $rating) {
            if (!empty($rating['label']) && isset($rating['score'])) {
                $ratings[] = array(
                    'label' => sanitize_text_field($rating['label']),
                    'score' => floatval($rating['score'])
                );
            }
        }
        update_post_meta($post_id, '_review_ratings', $ratings);
    } else {
        delete_post_meta($post_id, '_review_ratings');
    }

    // Salvar prós
    if (isset($_POST['pros'])) {
        $pros = array_filter(array_map('sanitize_text_field', $_POST['pros']));
        update_post_meta($post_id, '_review_pros', $pros);
    } else {
        delete_post_meta($post_id, '_review_pros');
    }

    // Salvar contras
    if (isset($_POST['cons'])) {
        $cons = array_filter(array_map('sanitize_text_field', $_POST['cons']));
        update_post_meta($post_id, '_review_cons', $cons);
    } else {
        delete_post_meta($post_id, '_review_cons');
    }
}
add_action('save_post_review', 'rakan_save_review');

// Função auxiliar para renderizar estrelas
function rakan_render_stars($score) {
    $full_stars = floor($score);
    $has_half = ($score - $full_stars) >= 0.5;
    $html = '';
    
    for ($i = 0; $i < 5; $i++) {
        if ($i < $full_stars) {
            $html .= '★';
        } elseif ($i == $full_stars && $has_half) {
            $html .= '⯨';
        } else {
            $html .= '☆';
        }
    }
    
    return $html;
}

// Shortcode [review id="123"]
function rakan_review_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
    ), $atts);

    $post_id = intval($atts['id']);
    if (!$post_id) {
        return '<p>ID do review não especificado.</p>';
    }

    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'review') {
        return '<p>Review não encontrado.</p>';
    }

    $title = get_the_title($post_id);
    $final_score = get_post_meta($post_id, '_review_final_score', true);
    $button_text = get_post_meta($post_id, '_review_button_text', true);
    $button_link = get_post_meta($post_id, '_review_button_link', true);
    $ratings = get_post_meta($post_id, '_review_ratings', true) ?: array();
    $pros = get_post_meta($post_id, '_review_pros', true) ?: array();
    $cons = get_post_meta($post_id, '_review_cons', true) ?: array();
    
    // Pegar imagem de destaque
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $image = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium') : '';

    // Schema Markup
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Review',
        'itemReviewed' => array(
            '@type' => 'Product',
            'name' => $title,
        ),
        'reviewRating' => array(
            '@type' => 'Rating',
            'ratingValue' => $final_score,
            'bestRating' => '5',
            'worstRating' => '0'
        ),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author()
        )
    );

    if ($image) {
        $schema['itemReviewed']['image'] = $image;
    }

    ob_start();
    ?>
    
    <script type="application/ld+json">
    <?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
    </script>
    
    <style>
        .rakan-review-box{background:var(--rakan-surface-1,#242424);border:1px solid var(--rakan-border,#404040);border-radius:var(--rakan-radius-lg,.875rem);padding:2rem;margin:2rem 0}.review-header{text-align:center;padding-bottom:2rem;border-bottom:1px solid var(--rakan-border,#404040);margin-bottom:2rem}.review-title{font-size:1.75rem;font-weight:700;color:var(--rakan-text-primary,#f5f5f5);margin-bottom:1rem;font-family:var(--rakan-font-heading,'Libre Baskerville',serif)}.review-image{width:200px;height:200px;object-fit:contain;margin:1rem auto;display:block;border-radius:var(--rakan-radius-md,.625rem)}.review-final-score{font-size:4rem;font-weight:700;color:var(--rakan-primary,#e25507);line-height:1;margin-bottom:.5rem}.review-stars{font-size:2rem;color:#ffb800;margin:.5rem 0;letter-spacing:.2rem}.review-score-label{font-size:.9rem;color:var(--rakan-text-muted,#a3a3a3);text-transform:uppercase;letter-spacing:.1rem}.review-ratings-grid{display:grid;gap:1rem;margin-bottom:2rem}.rating-item{display:flex;justify-content:space-between;align-items:center;padding:1rem 1.25rem;background:var(--rakan-surface-2,#2e2e2e);border-radius:var(--rakan-radius-md,.625rem);transition:all .3s}.rating-item:hover{background:rgba(226,85,7,.1);border-left:3px solid var(--rakan-primary,#e25507)}.rating-label{font-weight:600;color:var(--rakan-text-primary,#f5f5f5);font-size:1rem}.rating-score{display:flex;align-items:center;gap:.75rem}.rating-stars{color:#ffb800;font-size:1.1rem;letter-spacing:.1rem}.rating-number{font-weight:700;color:var(--rakan-text-secondary,#d4d4d4);font-size:1.1rem;min-width:2rem;text-align:right}.pros-cons-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin:2rem 0}.pros-box,.cons-box{background:var(--rakan-surface-2,#2e2e2e);border-radius:var(--rakan-radius-md,.625rem);padding:1.5rem}.pros-box{border-left:4px solid var(--rakan-success,#4ade80)}.cons-box{border-left:4px solid var(--rakan-error,#f87171)}.pros-cons-title{font-weight:700;font-size:1.2rem;margin-bottom:1rem;color:var(--rakan-text-primary,#f5f5f5);display:flex;align-items:center;gap:.5rem}.pros-title-icon{color:var(--rakan-success,#4ade80);font-size:1.5rem}.cons-title-icon{color:var(--rakan-error,#f87171);font-size:1.5rem}.pros-list,.cons-list{list-style:none;padding:0;margin:0}.pros-list li,.cons-list li{padding:.75rem 0;padding-left:2rem;position:relative;color:var(--rakan-text-secondary,#d4d4d4);line-height:1.6;border-bottom:1px solid rgba(255,255,255,.05)}.pros-list li:last-child,.cons-list li:last-child{border-bottom:none}.pros-list li:before{content:'✓';position:absolute;left:0;color:var(--rakan-success,#4ade80);font-weight:700;font-size:1.2rem}.cons-list li:before{content:'✗';position:absolute;left:0;color:var(--rakan-error,#f87171);font-weight:700;font-size:1.2rem}.review-button-wrapper{text-align:center;margin-top:2rem;padding-top:2rem;border-top:1px solid var(--rakan-border,#404040)}        .review-button{display:inline-block;padding:1.25rem 3rem;background:var(--rakan-primary,#e25507);color:var(--rakan-text-primary,#f5f5f5);text-decoration:none;border-radius:var(--rakan-radius-md,.625rem);font-weight:700;font-size:1.1rem;transition:all .3s;box-shadow:0 4px 12px rgba(226,85,7,.2)}.review-button:hover{background:var(--rakan-primary-hover,#ff6a1a);transform:translateY(-2px);box-shadow:0 6px 20px rgba(226,85,7,.4);color:var(--rakan-text-primary,#f5f5f5)}@media (max-width:768px){.rakan-review-box{padding:1.5rem}.pros-cons-grid{grid-template-columns:1fr;gap:1rem}.review-final-score{font-size:3rem}.review-stars{font-size:1.5rem}.review-title{font-size:1.5rem}.rating-item{padding:.875rem 1rem}.rating-label{font-size:.9rem}}
    </style>
    
    <div class="rakan-review-box">
        <div class="review-header">
            <?php if ($title): ?>
                <h3 class="review-title"><?php echo esc_html($title); ?></h3>
            <?php endif; ?>
            
            <?php if ($image): ?>
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="review-image">
            <?php endif; ?>
            
            <?php if ($final_score > 0): ?>
                <div class="review-score-label">Nota Final</div>
                <div class="review-final-score"><?php echo number_format($final_score, 1, ',', '.'); ?></div>
                <div class="review-stars"><?php echo rakan_render_stars($final_score); ?></div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($ratings)): ?>
        <div class="review-ratings-grid">
            <?php foreach ($ratings as $rating): ?>
            <div class="rating-item">
                <span class="rating-label"><?php echo esc_html($rating['label']); ?></span>
                <div class="rating-score">
                    <span class="rating-stars"><?php echo rakan_render_stars($rating['score']); ?></span>
                    <span class="rating-number"><?php echo number_format($rating['score'], 1, ',', '.'); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($pros) || !empty($cons)): ?>
        <div class="pros-cons-grid">
            <?php if (!empty($pros)): ?>
            <div class="pros-box">
                <div class="pros-cons-title">
                    <span class="pros-title-icon">✓</span>
                    Prós
                </div>
                <ul class="pros-list">
                    <?php foreach ($pros as $pro): ?>
                        <li><?php echo esc_html($pro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($cons)): ?>
            <div class="cons-box">
                <div class="pros-cons-title">
                    <span class="cons-title-icon">✗</span>
                    Contras
                </div>
                <ul class="cons-list">
                    <?php foreach ($cons as $con): ?>
                        <li><?php echo esc_html($con); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($button_text && $button_link): ?>
        <div class="review-button-wrapper">
            <a href="<?php echo esc_url($button_link); ?>" class="review-button" target="_blank" rel="noopener nofollow sponsored">
                <?php echo esc_html($button_text); ?> →
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <?php
    return ob_get_clean();
}
add_shortcode('review', 'rakan_review_shortcode');

// Adicionar botão no TinyMCE
function rakan_review_tinymce_button() {
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }
    add_filter('mce_buttons', 'rakan_register_review_button');
    add_filter('mce_external_plugins', 'rakan_add_review_plugin');
}
add_action('admin_init', 'rakan_review_tinymce_button');

function rakan_register_review_button($buttons) {
    array_push($buttons, 'rakan_review');
    return $buttons;
}

function rakan_add_review_plugin($plugin_array) {
    $plugin_array['rakan_review'] = get_stylesheet_directory_uri() . '/js/review-tinymce.js';
    return $plugin_array;
}

// Criar arquivo JS do TinyMCE
function rakan_create_review_tinymce_js() {
    $js_dir = get_stylesheet_directory() . '/js';
    if (!file_exists($js_dir)) {
        wp_mkdir_p($js_dir);
    }
    
    $js_file = $js_dir . '/review-tinymce.js';
    if (!file_exists($js_file)) {
        $reviews = rakan_get_all_reviews();
        $js_content = "(function() {
    tinymce.PluginManager.add('rakan_review', function(editor, url) {
        editor.addButton('rakan_review', {
            title: 'Inserir Review',
            icon: 'star',
            onclick: function() {
                var reviews = " . json_encode($reviews) . ";
                
                if (reviews.length === 0) {
                    alert('Nenhum review encontrado. Crie um review primeiro em Reviews.');
                    return;
                }
                
                var options = reviews.map(function(review) {
                    return {text: review.title, value: review.id};
                });
                
                editor.windowManager.open({
                    title: 'Inserir Review',
                    body: [{
                        type: 'listbox',
                        name: 'review_id',
                        label: 'Selecione o review:',
                        values: options
                    }],
                    onsubmit: function(e) {
                        var shortcode = '[review id=\"' + e.data.review_id + '\"]';
                        editor.insertContent(shortcode);
                    }
                });
            }
        });
    });
})();";
        file_put_contents($js_file, $js_content);
    }
}
add_action('admin_init', 'rakan_create_review_tinymce_js');

// Função auxiliar para obter todos os reviews
function rakan_get_all_reviews() {
    $reviews = get_posts(array(
        'post_type' => 'review',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ));
    
    $result = array();
    foreach ($reviews as $review) {
        $result[] = array(
            'id' => $review->ID,
            'title' => $review->post_title,
        );
    }
    return $result;
}