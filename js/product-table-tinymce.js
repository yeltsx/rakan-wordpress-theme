(function() {
    tinymce.PluginManager.add('rakan_product_table', function(editor, url) {
        editor.addButton('rakan_product_table', {
            title: 'Inserir Tabela de Produtos',
            icon: 'table',
            onclick: function() {
                var tables = [];
                
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
                        var shortcode = '[product_table id="' + e.data.table_id + '"]';
                        editor.insertContent(shortcode);
                    }
                });
            }
        });
    });
})();