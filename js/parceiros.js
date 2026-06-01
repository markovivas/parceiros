jQuery(document).ready(function($) {
    'use strict';

    var $cards = $('.card-parceiro');
    var $grid = $('#grid-parceiros');
    var $contagem = $('#contagem-resultados');
    var $semParceiros = $('.sem-parceiros');
    var filtrosAtivos = false;

    function atualizarContagem() {
        if (!$contagem.length) return;
        var visiveis = $cards.filter(':visible').length;
        var total = $cards.length;
        if (filtrosAtivos) {
            $contagem.text('Mostrando ' + visiveis + ' de ' + total + ' parceiro(s)');
        } else {
            $contagem.text('');
        }
    }

    function filtrarParceiros() {
        var pesquisa = $('#pesquisa-parceiros').val().toLowerCase().trim();
        var setor = $('#filtro-setor').val();
        var categoria = $('#filtro-categoria').val();

        filtrosAtivos = pesquisa !== '' || setor !== '' || categoria !== '';

        $cards.each(function() {
            var $card = $(this);
            var nome = ($card.data('nome') || '').toLowerCase();
            var cardSetor = $card.data('setor') || '';
            var cardCategoria = $card.data('categoria') || '';

            var matchPesquisa = pesquisa === '' || nome.indexOf(pesquisa) > -1;
            var matchSetor = setor === '' || cardSetor === setor;
            var matchCategoria = categoria === '' || cardCategoria === categoria;

            if (matchPesquisa && matchSetor && matchCategoria) {
                $card.removeClass('filtrado-out').css({ position: '', visibility: '' });
            } else {
                $card.addClass('filtrado-out');
                setTimeout(function() {
                    if ($card.hasClass('filtrado-out')) {
                        $card.css({ position: 'absolute', visibility: 'hidden' });
                    }
                }, 300);
            }
        });

        var visiveis = $cards.filter(':visible').length;
        if (visiveis === 0 && $semParceiros.length) {
            if (!$semParceiros.find('.sem-resultados-msg').length) {
                $semParceiros.find('p').html('Nenhum parceiro corresponde aos filtros aplicados. <button type="button" class="botao-limpar-filtros" id="limpar-filtros-inline">Limpar filtros</button>');
            }
            $semParceiros.show();
        } else if ($semParceiros.length) {
            $semParceiros.hide();
        }

        atualizarContagem();
    }

    $('#pesquisa-parceiros').on('input', filtrarParceiros);
    $('#filtro-setor, #filtro-categoria').on('change', filtrarParceiros);

    $(document).on('click', '#limpar-filtros, #limpar-filtros-inline', function() {
        $('#pesquisa-parceiros').val('');
        $('#filtro-setor').val('');
        $('#filtro-categoria').val('');
        filtrarParceiros();
        $('#pesquisa-parceiros').focus();
    });

    // Scroll Reveal com Intersection Observer
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        $cards.each(function() {
            this.style.animationPlayState = 'paused';
            observer.observe(this);
        });
    }

    // Atualizar contagem inicial
    atualizarContagem();
});
