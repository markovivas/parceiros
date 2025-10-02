jQuery(document).ready(function($) {
    // Filtros em tempo real
    $('#pesquisa-parceiros').on('input', function() {
        filtrarParceiros();
    });
    
    $('#filtro-setor, #filtro-categoria').on('change', function() {
        filtrarParceiros();
    });
    
    function filtrarParceiros() {
        var pesquisa = $('#pesquisa-parceiros').val().toLowerCase();
        var setor = $('#filtro-setor').val();
        var categoria = $('#filtro-categoria').val();
        
        $('.card-parceiro').each(function() {
            var $card = $(this);
            var nome = $card.find('.parceiro-nome').text().toLowerCase();
            var cardSetor = $card.data('setor');
            var cardCategoria = $card.data('categoria');
            
            var correspondePesquisa = nome.indexOf(pesquisa) > -1 || pesquisa === '';
            var correspondeSetor = setor === '' || cardSetor === setor;
            var correspondeCategoria = categoria === '' || cardCategoria === categoria;
            
            if (correspondePesquisa && correspondeSetor && correspondeCategoria) {
                $card.show();
            } else {
                $card.hide();
            }
        });
        
        // Mostrar mensagem se não houver resultados
        var cardsVisiveis = $('.card-parceiro:visible').length;
        if (cardsVisiveis === 0) {
            $('.sem-parceiros').show();
        } else {
            $('.sem-parceiros').hide();
        }
    }
    
    // Confirmação para exclusão
    $('.botao-excluir').on('click', function(e) {
        if (!confirm('Tem certeza que deseja excluir este parceiro?')) {
            e.preventDefault();
        }
    });
});