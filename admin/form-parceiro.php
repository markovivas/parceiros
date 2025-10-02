<?php
// Verificar se é edição ou adição
global $wpdb;
$tabela_parceiros = $wpdb->prefix . 'parceiros';

$parceiro = null;
$titulo_pagina = 'Adicionar Parceiro';
$acao = 'adicionar_parceiro';
$nonce_action = 'adicionar_parceiro';

// Verificar se é edição
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $parceiro = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabela_parceiros WHERE id = %d", $id));
    
    if ($parceiro) {
        $titulo_pagina = 'Editar Parceiro';
        $acao = 'editar_parceiro';
        $nonce_action = 'editar_parceiro';
    }
}

// Obter setores e categorias existentes para sugestões
$setores_existentes = $wpdb->get_col("SELECT DISTINCT setor FROM $tabela_parceiros WHERE setor != '' ORDER BY setor");
$categorias_existentes = $wpdb->get_col("SELECT DISTINCT categoria FROM $tabela_parceiros WHERE categoria != '' ORDER BY categoria");
?>

<div class="wrap">
    <h1><?php echo $titulo_pagina; ?></h1>
    
    <div class="form-parceiro">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo $acao; ?>">
            <?php if ($parceiro): ?>
                <input type="hidden" name="id" value="<?php echo $parceiro->id; ?>">
            <?php endif; ?>
            <?php wp_nonce_field($nonce_action); ?>
            
            <div class="form-grupo">
                <label for="nome">Nome do Parceiro *</label>
                <input type="text" id="nome" name="nome" value="<?php echo $parceiro ? esc_attr($parceiro->nome) : ''; ?>" required>
            </div>
            
            <div class="form-grupo">
                <label for="logo">Logo do Parceiro</label>
                <?php if ($parceiro && !empty($parceiro->logo)): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="<?php echo esc_url($parceiro->logo); ?>" alt="Logo atual" style="max-width: 150px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                        <br>
                        <small>Logo atual</small>
                    </div>
                <?php endif; ?>
                <input type="file" id="logo" name="logo" accept="image/*">
                <small>Formatos: JPG, PNG, GIF. Tamanho máximo: 2MB</small>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-grupo">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" value="<?php echo $parceiro ? esc_attr($parceiro->telefone) : ''; ?>" placeholder="(11) 99999-9999">
                </div>
                
                <div class="form-grupo">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?php echo $parceiro ? esc_attr($parceiro->email) : ''; ?>" placeholder="contato@empresa.com">
                </div>
            </div>
            
            <div class="form-grupo">
                <label for="site">Website</label>
                <input type="url" id="site" name="site" value="<?php echo $parceiro ? esc_url($parceiro->site) : ''; ?>" placeholder="https://empresa.com">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-grupo">
                    <label for="setor">Setor</label>
                    <input type="text" id="setor" name="setor" value="<?php echo $parceiro ? esc_attr($parceiro->setor) : ''; ?>" list="setores-sugeridos" placeholder="Ex: Tecnologia, Marketing, Saúde">
                    <datalist id="setores-sugeridos">
                        <?php foreach ($setores_existentes as $setor): ?>
                            <option value="<?php echo esc_attr($setor); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                
                <div class="form-grupo">
                    <label for="categoria">Categoria/Tipo</label>
                    <input type="text" id="categoria" name="categoria" value="<?php echo $parceiro ? esc_attr($parceiro->categoria) : ''; ?>" list="categorias-sugeridas" placeholder="Ex: Desenvolvimento, Consultoria, Varejo">
                    <datalist id="categorias-sugeridas">
                        <?php foreach ($categorias_existentes as $categoria): ?>
                            <option value="<?php echo esc_attr($categoria); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>
            
            <div class="form-grupo">
                <label for="endereco">Endereço</label>
                <textarea id="endereco" name="endereco" rows="3" placeholder="Endereço completo"><?php echo $parceiro ? esc_textarea($parceiro->endereco) : ''; ?></textarea>
            </div>
            
            <div class="form-grupo">
                <label>
                    <input type="checkbox" name="ativo" value="1" <?php echo ($parceiro && $parceiro->ativo) || !$parceiro ? 'checked' : ''; ?>>
                    Parceiro ativo
                </label>
                <small>Desmarque para desativar este parceiro (não será exibido no site)</small>
            </div>
            
            <div class="form-acoes">
                <button type="submit" class="botao-submit">
                    <?php echo $parceiro ? 'Atualizar Parceiro' : 'Adicionar Parceiro'; ?>
                </button>
                
                <a href="<?php echo admin_url('admin.php?page=parceiros'); ?>" class="botao-cancelar">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.form-parceiro {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    max-width: 700px;
}

.form-grupo {
    margin-bottom: 25px;
}

.form-grupo label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #23282d;
    font-size: 14px;
}

.form-grupo input[type="text"],
.form-grupo input[type="email"],
.form-grupo input[type="url"],
.form-grupo input[type="file"],
.form-grupo select,
.form-grupo textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
    box-sizing: border-box;
}

.form-grupo input:focus,
.form-grupo select:focus,
.form-grupo textarea:focus {
    border-color: #007cba;
    outline: none;
    box-shadow: 0 0 0 1px #007cba;
}

.form-grupo textarea {
    height: 80px;
    resize: vertical;
}

.form-grupo small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-style: italic;
}

.form-grupo input[type="checkbox"] {
    margin-right: 8px;
}

.form-grupo label input[type="checkbox"] {
    display: inline-block;
    width: auto;
}

.form-acoes {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 15px;
    align-items: center;
}

.botao-submit {
    background: #007cba;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: background 0.3s;
}

.botao-submit:hover {
    background: #005a87;
}

.botao-cancelar {
    color: #666;
    text-decoration: none;
    padding: 10px 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.3s;
}

.botao-cancelar:hover {
    background: #f8f9fa;
    color: #333;
}

/* Estilos para datalist */
datalist {
    position: absolute;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
}

/* Responsividade */
@media (max-width: 768px) {
    .form-parceiro {
        padding: 20px;
        margin: 0 10px;
    }
    
    .form-acoes {
        flex-direction: column;
        align-items: stretch;
    }
    
    .botao-submit,
    .botao-cancelar {
        text-align: center;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Máscara para telefone
    $('#telefone').on('input', function() {
        var valor = $(this).val().replace(/\D/g, '');
        
        if (valor.length <= 10) {
            valor = valor.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else {
            valor = valor.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
        }
        
        $(this).val(valor);
    });
    
    // Validação do formulário
    $('form').on('submit', function(e) {
        var nome = $('#nome').val().trim();
        
        if (nome === '') {
            e.preventDefault();
            alert('Por favor, preencha o nome do parceiro.');
            $('#nome').focus();
            return false;
        }
        
         // Validação do tamanho do arquivo
         var logo = $('#logo')[0].files[0];
         if (logo && logo.size > 2 * 1024 * 1024) {
              e.preventDefault();
              alert('O arquivo do logo é muito grande. O tamanho máximo permitido é 2MB.');
              return false;
         }
        
        // Validação de e-mail se preenchido
        var email = $('#email').val().trim();
        if (email !== '' && !isValidEmail(email)) {
            e.preventDefault();
            alert('Por favor, insira um e-mail válido.');
            $('#email').focus();
            return false;
        }
        
        // Validação de URL se preenchido
        var site = $('#site').val().trim();
        if (site !== '' && !isValidURL(site)) {
            e.preventDefault();
            alert('Por favor, insira uma URL válida.');
            $('#site').focus();
            return false;
        }
    });
    
    function isValidEmail(email) {
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    function isValidURL(url) {
        try {
            new URL(url);
            return true;
        } catch (_) {
            return false;
        }
    }
    
    // Preview de imagem antes do upload
    $('#logo').on('change', function(e) {
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                // Remove preview anterior se existir
                $('.logo-preview').remove();
                
                // Cria novo preview
                var preview = $('<div class="logo-preview" style="margin-bottom: 10px;">' +
                    '<img src="' + e.target.result + '" alt="Preview" style="max-width: 150px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">' +
                    '<br><small>Preview da nova logo</small>' +
                    '</div>');
                
                $(input).before(preview);
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    });
});
</script>