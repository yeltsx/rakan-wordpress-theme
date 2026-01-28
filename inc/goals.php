<?php
// ========================================
// Custom Post Type: Metas
// ========================================

function register_metas_post_type() {
    $labels = array(
        'name'                  => 'Metas',
        'singular_name'         => 'Meta',
        'menu_name'             => 'Metas',
        'add_new'               => 'Adicionar Nova',
        'add_new_item'          => 'Adicionar Nova Meta',
        'edit_item'             => 'Editar Meta',
        'new_item'              => 'Nova Meta',
        'view_item'             => 'Ver Meta',
        'search_items'          => 'Buscar Metas',
        'not_found'             => 'Nenhuma meta encontrada',
        'not_found_in_trash'    => 'Nenhuma meta na lixeira',
        'all_items'             => 'Todas as Metas',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-flag',
        'menu_position'         => 5,
        'supports'              => array('title', 'editor', 'thumbnail', 'comments'),
        'rewrite'               => array('slug' => 'meta'),
        'show_in_rest'          => true,
    );

    register_post_type('meta', $args);
}
add_action('init', 'register_metas_post_type');

// Campos personalizados para Metas
function add_meta_custom_fields() {
    add_meta_box(
        'meta_details',
        'Detalhes da Meta',
        'meta_custom_fields_callback',
        'meta',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_meta_custom_fields');

function meta_custom_fields_callback($post) {
    wp_nonce_field('save_meta_details', 'meta_details_nonce');
    
    $status = get_post_meta($post->ID, '_meta_status', true);
    $data_inicio = get_post_meta($post->ID, '_meta_data_inicio', true);
    $data_fim = get_post_meta($post->ID, '_meta_data_fim', true);
    $progresso = get_post_meta($post->ID, '_meta_progresso', true);
    $categoria = get_post_meta($post->ID, '_meta_categoria', true);
    $prioridade = get_post_meta($post->ID, '_meta_prioridade', true);
    ?>
    
    <style>
        .meta-fields-wrapper {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 15px;
        }
        .meta-field {
            display: flex;
            flex-direction: column;
        }
        .meta-field label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .meta-field input,
        .meta-field select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .meta-field-full {
            grid-column: 1 / -1;
        }
    </style>
    
    <div class="meta-fields-wrapper">
        <div class="meta-field">
            <label for="meta_status">Status</label>
            <select name="meta_status" id="meta_status">
                <option value="em_andamento" <?php selected($status, 'em_andamento'); ?>>Em Andamento</option>
                <option value="concluida" <?php selected($status, 'concluida'); ?>>Concluída</option>
                <option value="pausada" <?php selected($status, 'pausada'); ?>>Pausada</option>
                <option value="cancelada" <?php selected($status, 'cancelada'); ?>>Cancelada</option>
            </select>
        </div>
        
        <div class="meta-field">
            <label for="meta_progresso">Progresso (%)</label>
            <input type="number" name="meta_progresso" id="meta_progresso" value="<?php echo esc_attr($progresso); ?>" min="0" max="100" placeholder="0-100">
        </div>
        
        <div class="meta-field">
            <label for="meta_data_inicio">Data de Início</label>
            <input type="date" name="meta_data_inicio" id="meta_data_inicio" value="<?php echo esc_attr($data_inicio); ?>">
        </div>
        
        <div class="meta-field">
            <label for="meta_data_fim">Data Prevista de Conclusão</label>
            <input type="date" name="meta_data_fim" id="meta_data_fim" value="<?php echo esc_attr($data_fim); ?>">
        </div>
        
        <div class="meta-field">
            <label for="meta_categoria">Categoria</label>
            <select name="meta_categoria" id="meta_categoria">
                <option value="">Selecione...</option>
                <option value="saude" <?php selected($categoria, 'saude'); ?>>🏃 Saúde & Fitness</option>
                <option value="educacao" <?php selected($categoria, 'educacao'); ?>>📚 Educação & Leitura</option>
                <option value="carreira" <?php selected($categoria, 'carreira'); ?>>💼 Carreira & Profissional</option>
                <option value="financeiro" <?php selected($categoria, 'financeiro'); ?>>💰 Financeiro</option>
                <option value="pessoal" <?php selected($categoria, 'pessoal'); ?>>🎯 Desenvolvimento Pessoal</option>
                <option value="hobby" <?php selected($categoria, 'hobby'); ?>>🎨 Hobbies & Criatividade</option>
                <option value="relacionamentos" <?php selected($categoria, 'relacionamentos'); ?>>❤️ Relacionamentos</option>
                <option value="outro" <?php selected($categoria, 'outro'); ?>>📌 Outro</option>
            </select>
        </div>
        
        <div class="meta-field">
            <label for="meta_prioridade">Prioridade</label>
            <select name="meta_prioridade" id="meta_prioridade">
                <option value="baixa" <?php selected($prioridade, 'baixa'); ?>>🟢 Baixa</option>
                <option value="media" <?php selected($prioridade, 'media'); ?>>🟡 Média</option>
                <option value="alta" <?php selected($prioridade, 'alta'); ?>>🔴 Alta</option>
            </select>
        </div>
    </div>
    <?php
}

function save_meta_custom_fields($post_id) {
    if (!isset($_POST['meta_details_nonce']) || !wp_verify_nonce($_POST['meta_details_nonce'], 'save_meta_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $fields = array('meta_status', 'meta_data_inicio', 'meta_data_fim', 'meta_progresso', 'meta_categoria', 'meta_prioridade');
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_meta', 'save_meta_custom_fields');

// Inclui Metas na home
function include_metas_in_home($query) {
    if ($query->is_home() && $query->is_main_query()) {
        $query->set('post_type', array('post', 'meta'));
    }
}
add_action('pre_get_posts', 'include_metas_in_home');

// Helper function para pegar ícone de categoria
function get_meta_categoria_icon($categoria) {
    $icons = array(
        'saude' => '🏃',
        'educacao' => '📚',
        'carreira' => '💼',
        'financeiro' => '💰',
        'pessoal' => '🎯',
        'hobby' => '🎨',
        'relacionamentos' => '❤️',
        'outro' => '📌'
    );
    
    return isset($icons[$categoria]) ? $icons[$categoria] : '📌';
}

// Helper function para pegar label de status
function get_meta_status_label($status) {
    $labels = array(
        'em_andamento' => 'Em Andamento',
        'concluida' => 'Concluída',
        'pausada' => 'Pausada',
        'cancelada' => 'Cancelada'
    );
    
    return isset($labels[$status]) ? $labels[$status] : 'Em Andamento';
}