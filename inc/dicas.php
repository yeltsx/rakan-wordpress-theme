<?php
function rakan_dicas_post_type() {
    register_post_type('dica', array(
        'labels' => array(
            'name' => 'Dicas',
            'singular_name' => 'Dica',
            'add_new' => 'Adicionar Dica',
            'add_new_item' => 'Adicionar Nova Dica',
            'edit_item' => 'Editar Dica',
            'all_items' => 'Todas as Dicas',
            'view_item' => 'Ver Dica',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'dicas'),
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-lightbulb',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'capability_type' => 'post',
        'taxonomies' => array('category', 'post_tag'),
        'show_in_rest' => true, // Habilita editor Gutenberg
    ));
}
add_action('init', 'rakan_dicas_post_type');

// IMPORTANTE: Adicionar dicas à query principal da home
function rakan_add_dicas_to_query($query) {
    if (!is_admin() && $query->is_main_query() && (is_home() || is_front_page())) {
        $post_types = array('post', 'meta', 'dica');
        $query->set('post_type', $post_types);
    }
}
add_action('pre_get_posts', 'rakan_add_dicas_to_query');

// Adicionar meta box para linguagem de código
function rakan_dicas_meta_boxes() {
    add_meta_box(
        'dica_code_language',
        'Configurações da Dica',
        'rakan_dica_code_language_callback',
        'dica',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'rakan_dicas_meta_boxes');

// Callback do meta box
function rakan_dica_code_language_callback($post) {
    wp_nonce_field('rakan_dica_save', 'rakan_dica_nonce');
    $language = get_post_meta($post->ID, '_dica_language', true);
    $tipo = get_post_meta($post->ID, '_dica_tipo', true);
    ?>
    <p>
        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Tipo de Dica:</label>
        <select name="dica_tipo" style="width: 100%; padding: 6px;">
            <option value="geral" <?php selected($tipo, 'geral'); ?>>💡 Geral</option>
            <option value="codigo" <?php selected($tipo, 'codigo'); ?>>💻 Código</option>
            <option value="tutorial" <?php selected($tipo, 'tutorial'); ?>>📚 Tutorial</option>
            <option value="solucao" <?php selected($tipo, 'solucao'); ?>>🔧 Solução</option>
        </select>
    </p>
    
    <p>
        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Linguagem (se for código):</label>
        <input type="text" 
               name="dica_language" 
               value="<?php echo esc_attr($language); ?>" 
               placeholder="Ex: php, javascript, css"
               style="width: 100%; padding: 6px;">
        <small style="color: #666; display: block; margin-top: 5px;">
            Deixe vazio se não for código
        </small>
    </p>
    <?php
}

// Salvar meta dados
function rakan_save_dica($post_id) {
    if (!isset($_POST['rakan_dica_nonce'])) return;
    if (!wp_verify_nonce($_POST['rakan_dica_nonce'], 'rakan_dica_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['dica_tipo'])) {
        update_post_meta($post_id, '_dica_tipo', sanitize_text_field($_POST['dica_tipo']));
    }

    if (isset($_POST['dica_language'])) {
        update_post_meta($post_id, '_dica_language', sanitize_text_field($_POST['dica_language']));
    }
}
add_action('save_post_dica', 'rakan_save_dica');

// Função helper para ícone do tipo
function get_dica_tipo_icon($tipo) {
    $icons = array(
        'geral' => '💡',
        'codigo' => '💻',
        'tutorial' => '📚',
        'solucao' => '🔧'
    );
    return isset($icons[$tipo]) ? $icons[$tipo] : '💡';
}

// Função helper para label do tipo
function get_dica_tipo_label($tipo) {
    $labels = array(
        'geral' => 'Dica Geral',
        'codigo' => 'Código',
        'tutorial' => 'Tutorial',
        'solucao' => 'Solução'
    );
    return isset($labels[$tipo]) ? $labels[$tipo] : 'Dica';
}
