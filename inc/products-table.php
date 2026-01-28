<?php
/**
 * Sistema de Tabelas de Produtos para Tema Rakan
 * Adicione este código ao functions.php do tema child
 */

// Criar tipo de post customizado para as tabelas
function rakan_product_tables_post_type() {
    register_post_type('product_table', array(
        'labels' => array(
            'name' => 'Tabelas de Produtos',
            'singular_name' => 'Tabela de Produtos',
            'add_new' => 'Adicionar Nova Tabela',
            'add_new_item' => 'Adicionar Nova Tabela',
            'edit_item' => 'Editar Tabela',
            'all_items' => 'Todas as Tabelas',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-list-view',
        'supports' => array('title'),
        'capability_type' => 'post',
    ));
}
add_action('init', 'rakan_product_tables_post_type');

// Adicionar meta box para gerenciar produtos da tabela
function rakan_product_table_meta_boxes() {
    add_meta_box(
        'product_table_items',
        'Produtos da Tabela',
        'rakan_product_table_items_callback',
        'product_table',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'rakan_product_table_meta_boxes');

// Callback do meta box
function rakan_product_table_items_callback($post) {
    wp_nonce_field('rakan_product_table_save', 'rakan_product_table_nonce');
    $items = get_post_meta($post->ID, '_product_table_items', true);
    if (!is_array($items)) {
        $items = array();
    }
    ?>
    <div id="product-table-manager">
        <style>
            .product-table-wrapper {
                margin: 20px 0;
            }
            .product-items-list {
                margin-bottom: 20px;
            }
            .product-item {
                background: #f9f9f9;
                border: 1px solid #ddd;
                padding: 15px;
                margin-bottom: 10px;
                border-radius: 4px;
            }
            .product-item-fields {
                display: grid;
                grid-template-columns: 2fr 1fr 1fr 2fr;
                gap: 10px;
                margin-bottom: 10px;
            }
            .product-item-field {
                display: flex;
                flex-direction: column;
            }
            .product-item-field label {
                font-weight: 600;
                margin-bottom: 5px;
                font-size: 12px;
            }
            .product-item-field input {
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 3px;
            }
            .product-item-actions {
                display: flex;
                gap: 10px;
            }
            .remove-item-btn {
                background: #dc3232;
                color: white;
                border: none;
                padding: 8px 15px;
                border-radius: 3px;
                cursor: pointer;
            }
            .move-btn {
                background: #666;
                color: white;
                border: none;
                padding: 8px 12px;
                border-radius: 3px;
                cursor: pointer;
            }
            .add-item-btn {
                background: #2271b1;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 3px;
                cursor: pointer;
                font-size: 14px;
            }
            .shortcode-display {
                background: #fff;
                border: 1px solid #2271b1;
                padding: 15px;
                margin-top: 20px;
                border-radius: 4px;
            }
            .shortcode-display code {
                background: #f0f0f0;
                padding: 5px 10px;
                border-radius: 3px;
                font-size: 14px;
                user-select: all;
            }
        </style>

        <div class="product-table-wrapper">
            <div class="product-items-list" id="product-items-list">
                <?php foreach ($items as $index => $item): ?>
                    <div class="product-item" data-index="<?php echo $index; ?>">
                        <div class="product-item-fields">
                            <div class="product-item-field">
                                <label>Nome do Produto</label>
                                <input type="text" name="product_items[<?php echo $index; ?>][name]" value="<?php echo esc_attr($item['name']); ?>" required>
                            </div>
                            <div class="product-item-field">
                                <label>Quantidade</label>
                                <input type="number" name="product_items[<?php echo $index; ?>][quantity]" value="<?php echo esc_attr($item['quantity']); ?>" min="0" step="1" required>
                            </div>
                            <div class="product-item-field">
                                <label>Valor (R$)</label>
                                <input type="number" name="product_items[<?php echo $index; ?>][price]" value="<?php echo esc_attr($item['price']); ?>" min="0" step="0.01" required>
                            </div>
                            <div class="product-item-field">
                                <label>Link (opcional)</label>
                                <input type="url" name="product_items[<?php echo $index; ?>][link]" value="<?php echo esc_attr($item['link']); ?>" placeholder="https://">
                            </div>
                        </div>
                        <div class="product-item-actions">
                            <button type="button" class="move-btn move-up" title="Mover para cima">↑</button>
                            <button type="button" class="move-btn move-down" title="Mover para baixo">↓</button>
                            <button type="button" class="remove-item-btn">Remover</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="button" class="add-item-btn" id="add-product-item">+ Adicionar Produto</button>

            <?php if ($post->ID): ?>
            <div class="shortcode-display">
                <strong>Shortcode para usar esta tabela:</strong><br>
                <code>[product_table id="<?php echo $post->ID; ?>"]</code>
                <p style="margin-top: 10px; color: #666; font-size: 13px;">
                    Copie e cole este shortcode em qualquer página ou post para exibir a tabela.
                </p>
            </div>
            <?php endif; ?>
        </div>

        <script>
        jQuery(document).ready(function($) {
            let itemIndex = <?php echo count($items); ?>;

            // Adicionar novo item
            $('#add-product-item').on('click', function() {
                const newItem = `
                    <div class="product-item" data-index="${itemIndex}">
                        <div class="product-item-fields">
                            <div class="product-item-field">
                                <label>Nome do Produto</label>
                                <input type="text" name="product_items[${itemIndex}][name]" required>
                            </div>
                            <div class="product-item-field">
                                <label>Quantidade</label>
                                <input type="number" name="product_items[${itemIndex}][quantity]" min="0" step="1" required>
                            </div>
                            <div class="product-item-field">
                                <label>Valor (R$)</label>
                                <input type="number" name="product_items[${itemIndex}][price]" min="0" step="0.01" required>
                            </div>
                            <div class="product-item-field">
                                <label>Link (opcional)</label>
                                <input type="url" name="product_items[${itemIndex}][link]" placeholder="https://">
                            </div>
                        </div>
                        <div class="product-item-actions">
                            <button type="button" class="move-btn move-up" title="Mover para cima">↑</button>
                            <button type="button" class="move-btn move-down" title="Mover para baixo">↓</button>
                            <button type="button" class="remove-item-btn">Remover</button>
                        </div>
                    </div>
                `;
                $('#product-items-list').append(newItem);
                itemIndex++;
            });

            // Remover item
            $(document).on('click', '.remove-item-btn', function() {
                if (confirm('Tem certeza que deseja remover este produto?')) {
                    $(this).closest('.product-item').remove();
                }
            });

            // Mover item para cima
            $(document).on('click', '.move-up', function() {
                const item = $(this).closest('.product-item');
                const prev = item.prev('.product-item');
                if (prev.length) {
                    item.insertBefore(prev);
                }
            });

            // Mover item para baixo
            $(document).on('click', '.move-down', function() {
                const item = $(this).closest('.product-item');
                const next = item.next('.product-item');
                if (next.length) {
                    item.insertAfter(next);
                }
            });
        });
        </script>
    </div>
    <?php
}

// Salvar dados da tabela
function rakan_save_product_table($post_id) {
    if (!isset($_POST['rakan_product_table_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['rakan_product_table_nonce'], 'rakan_product_table_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['product_items'])) {
        $items = array();
        foreach ($_POST['product_items'] as $item) {
            $items[] = array(
                'name' => sanitize_text_field($item['name']),
                'quantity' => floatval($item['quantity']),
                'price' => floatval($item['price']),
                'link' => esc_url_raw($item['link']),
            );
        }
        update_post_meta($post_id, '_product_table_items', $items);
    } else {
        delete_post_meta($post_id, '_product_table_items');
    }
}
add_action('save_post_product_table', 'rakan_save_product_table');

// Shortcode para exibir a tabela
function rakan_product_table_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
    ), $atts);

    $post_id = intval($atts['id']);
    if (!$post_id) {
        return '<p>ID da tabela não especificado.</p>';
    }

    $items = get_post_meta($post_id, '_product_table_items', true);
    if (!is_array($items) || empty($items)) {
        return '<p>Nenhum produto encontrado nesta tabela.</p>';
    }

    $table_title = get_the_title($post_id);
    
    ob_start();
    ?>
    <div class="rakan-product-table-wrapper">
        <style>
            .rakan-product-table-wrapper {
                margin: 2rem 0;
            }
            .rakan-product-table {
                width: 100%;
                border-collapse: collapse;
                background: #000;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                border-radius: 8px;
                overflow: hidden;
            }
            .rakan-product-table thead {
                background: var(--primary-color, #333);
                color: #fff;
            }
            .rakan-product-table th {
                padding: 15px;
                text-align: left;
                font-weight: 600;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .rakan-product-table td {
                padding: 15px;
                border-bottom: 1px solid #404040;
            }
            .rakan-product-table tbody tr:hover {
                background: #404040;
            }
            .rakan-product-table tbody tr:last-child td {
                border-bottom: none;
            }
            .rakan-product-table tfoot {
                background: #000;
                font-weight: 600;
            }
            .rakan-product-table tfoot td {
                padding: 15px;
                border-top: 2px solid #404040;
            }
            .product-link {
                color: var(--primary-color, #2271b1);
                text-decoration: none;
            }
            .product-link:hover {
                text-decoration: underline;
            }
            .price-cell {
                text-align: right;
            }
            .quantity-cell {
                text-align: center;
            }
            @media (max-width: 768px) {
                .rakan-product-table {
                    font-size: 14px;
                }
                .rakan-product-table th,
                .rakan-product-table td {
                    padding: 10px 8px;
                }
            }
        </style>
        
        <table class="rakan-product-table">
            <thead>
                <tr>
                    <th>Nome do Produto</th>
                    <th class="quantity-cell">Quantidade</th>
                    <th class="price-cell">Valor</th>
                    <th class="price-cell">Valor Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                foreach ($items as $item): 
                    $total = $item['quantity'] * $item['price'];
                    $grand_total += $total;
                ?>
                <tr>
                    <td>
                        <?php if (!empty($item['link'])): ?>
                            <a href="<?php echo esc_url($item['link']); ?>" class="product-link" target="_blank" rel="noopener">
                                <?php echo esc_html($item['name']); ?>
                            </a>
                        <?php else: ?>
                            <?php echo esc_html($item['name']); ?>
                        <?php endif; ?>
                    </td>
                    <td class="quantity-cell"><?php echo number_format($item['quantity'], 0, ',', '.'); ?></td>
                    <td class="price-cell">R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                    <td class="price-cell">R$ <?php echo number_format($total, 2, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;">Total Geral:</td>
                    <td class="price-cell">R$ <?php echo number_format($grand_total, 2, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('product_table', 'rakan_product_table_shortcode');

// Adicionar botão no editor TinyMCE
function rakan_product_table_tinymce_button() {
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }
    add_filter('mce_buttons', 'rakan_register_tinymce_button');
    add_filter('mce_external_plugins', 'rakan_add_tinymce_plugin');
}
add_action('admin_init', 'rakan_product_table_tinymce_button');

function rakan_register_tinymce_button($buttons) {
    array_push($buttons, 'rakan_product_table');
    return $buttons;
}

function rakan_add_tinymce_plugin($plugin_array) {
    $plugin_array['rakan_product_table'] = get_stylesheet_directory_uri() . '/js/product-table-tinymce.js';
    return $plugin_array;
}

// Criar o arquivo JS do TinyMCE
function rakan_create_tinymce_js() {
    $js_dir = get_stylesheet_directory() . '/js';
    if (!file_exists($js_dir)) {
        wp_mkdir_p($js_dir);
    }
    
    $js_file = $js_dir . '/product-table-tinymce.js';
    if (!file_exists($js_file)) {
        $js_content = "(function() {
    tinymce.PluginManager.add('rakan_product_table', function(editor, url) {
        editor.addButton('rakan_product_table', {
            title: 'Inserir Tabela de Produtos',
            icon: 'table',
            onclick: function() {
                var tables = " . json_encode(rakan_get_all_product_tables()) . ";
                
                if (tables.length === 0) {
                    alert('Nenhuma tabela encontrada. Crie uma tabela primeiro em Tabelas de Produtos.');
                    return;
                }
                
                var options = tables.map(function(table) {
                    return {text: table.title, value: table.id};
                });
                
                editor.windowManager.open({
                    title: 'Inserir Tabela de Produtos',
                    body: [{
                        type: 'listbox',
                        name: 'table_id',
                        label: 'Selecione a tabela:',
                        values: options
                    }],
                    onsubmit: function(e) {
                        var shortcode = '[product_table id=\"' + e.data.table_id + '\"]';
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
add_action('admin_init', 'rakan_create_tinymce_js');

// Função auxiliar para obter todas as tabelas
function rakan_get_all_product_tables() {
    $tables = get_posts(array(
        'post_type' => 'product_table',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ));
    
    $result = array();
    foreach ($tables as $table) {
        $result[] = array(
            'id' => $table->ID,
            'title' => $table->post_title,
        );
    }
    return $result;
}
?>