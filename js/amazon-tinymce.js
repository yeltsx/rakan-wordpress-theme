(function() {
    tinymce.PluginManager.add('amazon_product', function(editor, url) {
        
        // Adiciona o botão
        editor.addButton('amazon_product', {
            text: '📦 Amazon',
            icon: false,
            tooltip: 'Inserir Produto Amazon',
            onclick: function() {
                openAmazonPopup();
            }
        });
        
        // Função para abrir popup
        function openAmazonPopup() {
            editor.windowManager.open({
                title: 'Adicionar Produto Amazon',
                body: [
                    {
                        type: 'textbox',
                        name: 'url',
                        label: 'URL do Produto',
                        placeholder: 'https://amzn.to/xxxxx',
                        value: ''
                    },
                    {
                        type: 'textbox',
                        name: 'title',
                        label: 'Título do Produto',
                        placeholder: 'Nome do livro ou produto',
                        value: ''
                    },
                    {
                        type: 'textbox',
                        name: 'image',
                        label: 'URL da Imagem',
                        placeholder: 'https://m.media-amazon.com/images/...',
                        value: ''
                    },
                    {
                        type: 'textbox',
                        name: 'price',
                        label: 'Preço (opcional)',
                        placeholder: 'R$31,99',
                        value: ''
                    },
                    {
                        type: 'textbox',
                        name: 'description',
                        label: 'Descrição (opcional - máx 160 caracteres)',
                        placeholder: 'Descrição curta do produto...',
                        multiline: true,
                        minHeight: 60,
                        value: ''
                    }
                ],
                onsubmit: function(e) {
                    var url = e.data.url.trim();
                    var title = e.data.title.trim();
                    var image = e.data.image.trim();
                    var price = e.data.price.trim();
                    var description = e.data.description.trim();
                    
                    // Validação
                    if (!url || !title || !image) {
                        alert('Por favor, preencha pelo menos: URL, Título e Imagem');
                        return false;
                    }
                    
                    // Trunca descrição se necessário
                    if (description.length > 160) {
                        description = description.substring(0, 157) + '...';
                    }
                    
                    // Monta o shortcode
                    var shortcode = '[amazon url="' + url + '" title="' + title + '" image="' + image + '"';
                    
                    if (price) {
                        shortcode += ' price="' + price + '"';
                    }
                    
                    if (description) {
                        shortcode += ' description="' + description + '"';
                    }
                    
                    shortcode += ']';
                    
                    // Insere no editor
                    editor.insertContent(shortcode);
                }
            });
        }
    });
})();