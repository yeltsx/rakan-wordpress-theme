(function() {
    tinymce.PluginManager.add('rakan_review', function(editor, url) {
        editor.addButton('rakan_review', {
            title: 'Inserir Review',
            icon: 'star',
            onclick: function() {
                var reviews = [{"id":121,"title":"Teste"}];
                
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
                        var shortcode = '[review id="' + e.data.review_id + '"]';
                        editor.insertContent(shortcode);
                    }
                });
            }
        });
    });
})();