<?php
global $wpdb;
$tabela_parceiros = $wpdb->prefix . 'parceiros';

$parceiro = null;
$titulo_pagina = 'Adicionar Parceiro';
$acao = 'adicionar_parceiro';
$nonce_action = 'adicionar_parceiro';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $parceiro = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabela_parceiros WHERE id = %d", $id));

    if ($parceiro) {
        $titulo_pagina = 'Editar Parceiro';
        $acao = 'editar_parceiro';
        $nonce_action = 'editar_parceiro';
    }
}

$setores_existentes = $wpdb->get_col("SELECT DISTINCT setor FROM $tabela_parceiros WHERE setor != '' ORDER BY setor");
$categorias_existentes = $wpdb->get_col("SELECT DISTINCT categoria FROM $tabela_parceiros WHERE categoria != '' ORDER BY categoria");
?>

<div class="wrap">
    <h1><?php echo esc_html($titulo_pagina); ?></h1>

    <div class="parceiro-form-wrapper">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data" class="parceiro-form">
            <input type="hidden" name="action" value="<?php echo esc_attr($acao); ?>">
            <?php if ($parceiro): ?>
                <input type="hidden" name="id" value="<?php echo intval($parceiro->id); ?>">
            <?php endif; ?>
            <?php wp_nonce_field($nonce_action); ?>

            <div class="form-row">
                <div class="form-group form-group-full">
                    <label for="nome">Nome do Parceiro <span class="required">*</span></label>
                    <input type="text" id="nome" name="nome" value="<?php echo $parceiro ? esc_attr($parceiro->nome) : ''; ?>" required placeholder="Nome da empresa ou parceiro">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="logo">Logo</label>
                    <?php if ($parceiro && !empty($parceiro->logo)): ?>
                        <div class="logo-atual">
                            <div class="logo-box">
                                <img src="<?php echo esc_url($parceiro->logo); ?>" alt="Logo atual">
                            </div>
                            <small>Logo atual</small>
                        </div>
                    <?php endif; ?>
                    <div class="file-input-wrapper">
                        <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/gif,image/webp" data-max-size="2097152">
                        <label for="logo" class="file-label">Escolher arquivo</label>
                        <span class="file-name"></span>
                    </div>
                    <p class="description">Formatos: JPG, PNG, GIF, WebP. Máximo: 2MB.</p>
                </div>
            </div>

            <div class="form-row form-row-dual">
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" value="<?php echo $parceiro ? esc_attr($parceiro->telefone) : ''; ?>" placeholder="(11) 99999-9999" class="mask-telefone">
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?php echo $parceiro ? esc_attr($parceiro->email) : ''; ?>" placeholder="contato@empresa.com">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group-full">
                    <label for="site">Website</label>
                    <input type="url" id="site" name="site" value="<?php echo $parceiro ? esc_url($parceiro->site) : ''; ?>" placeholder="https://empresa.com">
                </div>
            </div>

            <div class="form-row form-row-dual">
                <div class="form-group">
                    <label for="setor">Setor</label>
                    <input type="text" id="setor" name="setor" value="<?php echo $parceiro ? esc_attr($parceiro->setor) : ''; ?>" list="setores-sugeridos" placeholder="Ex: Tecnologia, Saúde">
                    <datalist id="setores-sugeridos">
                        <?php foreach ($setores_existentes as $setor): ?>
                            <option value="<?php echo esc_attr($setor); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <input type="text" id="categoria" name="categoria" value="<?php echo $parceiro ? esc_attr($parceiro->categoria) : ''; ?>" list="categorias-sugeridas" placeholder="Ex: Consultoria, Varejo">
                    <datalist id="categorias-sugeridas">
                        <?php foreach ($categorias_existentes as $categoria): ?>
                            <option value="<?php echo esc_attr($categoria); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group-full">
                    <label for="endereco">Endereço</label>
                    <textarea id="endereco" name="endereco" rows="3" placeholder="Endereço completo da empresa"><?php echo $parceiro ? esc_textarea($parceiro->endereco) : ''; ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group-full">
                    <label class="checkbox-label">
                        <input type="checkbox" name="ativo" value="1" <?php echo ($parceiro && $parceiro->ativo) || !$parceiro ? 'checked' : ''; ?>>
                        <span class="checkbox-custom"></span>
                        Parceiro ativo
                    </label>
                    <p class="description">Desmarque para desativar este parceiro (não será exibido no site).</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary button-hero">
                    <?php echo $parceiro ? 'Atualizar Parceiro' : 'Adicionar Parceiro'; ?>
                </button>
                <a href="<?php echo admin_url('admin.php?page=parceiros'); ?>" class="button button-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    function maskPhone(val) {
        var v = val.replace(/\D/g, '');
        if (v.length <= 10) {
            v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else {
            v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        }
        return v;
    }

    $('#telefone').on('input', function() {
        $(this).val(maskPhone($(this).val()));
    });

    $('.parceiro-form').on('submit', function(e) {
        var nome = $('#nome').val().trim();
        if (!nome) {
            e.preventDefault();
            $('#nome').focus().addClass('error');
            alert('Preencha o nome do parceiro.');
            return;
        }

        var logo = $('#logo')[0].files[0];
        if (logo && logo.size > 2097152) {
            e.preventDefault();
            alert('O logo excede 2MB.');
            return;
        }

        var email = $('#email').val().trim();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            e.preventDefault();
            $('#email').focus().addClass('error');
            alert('E-mail inválido.');
            return;
        }

        var site = $('#site').val().trim();
        if (site) {
            try { new URL(site); } catch(_) {
                e.preventDefault();
                $('#site').focus().addClass('error');
                alert('URL inválida.');
                return;
            }
        }
    });

    $('#logo').on('change', function() {
        var input = this;
        var label = $(this).next('.file-label');
        var nameSpan = $(this).siblings('.file-name');

        if (input.files && input.files[0]) {
            nameSpan.text(input.files[0].name);

            $('.logo-preview').remove();
            var reader = new FileReader();
            reader.onload = function(e) {
                $('<div class="logo-preview"><div class="logo-box"><img src="' + e.target.result + '" alt="Preview"></div><small>Preview da nova logo</small></div>').insertAfter(input.closest('.file-input-wrapper'));
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    $('input.error').on('input', function() {
        $(this).removeClass('error');
    });
});
</script>
