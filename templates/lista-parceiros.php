<div class="parceiros-container">
    <div class="parceiros-filtros">
        <div class="filtro-grupo">
            <label for="pesquisa-parceiros">Pesquisar Parceiro:</label>
            <input type="text" id="pesquisa-parceiros" placeholder="Digite o nome do parceiro...">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="filtro-grupo">
                <label for="filtro-setor">Filtrar por Setor:</label>
                <select id="filtro-setor">
                    <option value="">Todos os Setores</option>
                    <?php foreach ($setores as $setor): ?>
                        <option value="<?php echo esc_attr($setor); ?>"><?php echo esc_html($setor); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filtro-grupo">
                <label for="filtro-categoria">Filtrar por Categoria:</label>
                <select id="filtro-categoria">
                    <option value="">Todas as Categorias</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo esc_attr($categoria); ?>"><?php echo esc_html($categoria); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    
    <?php if (empty($parceiros)): ?>
        <div class="sem-parceiros">
            Nenhum parceiro encontrado.
        </div>
    <?php else: ?>
        <div class="grid-parceiros">
            <?php foreach ($parceiros as $parceiro): ?>
                <div class="card-parceiro" data-setor="<?php echo esc_attr($parceiro->setor); ?>" data-categoria="<?php echo esc_attr($parceiro->categoria); ?>">
                    <div class="parceiro-logo">
                        <?php if (!empty($parceiro->logo)): ?>
                            <img src="<?php echo esc_url($parceiro->logo); ?>" alt="<?php echo esc_attr($parceiro->nome); ?>">
                        <?php else: ?>
                            <div class="logo-placeholder">LOGO</div>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="parceiro-nome"><?php echo esc_html($parceiro->nome); ?></h3>
                    
                    <?php if (!empty($parceiro->setor)): ?>
                        <span class="parceiro-setor"><?php echo esc_html($parceiro->setor); ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($parceiro->categoria)): ?>
                        <span class="parceiro-categoria"><?php echo esc_html($parceiro->categoria); ?></span>
                    <?php endif; ?>
                    
                    <div class="parceiro-info">
                        <i>📞</i>
                        <span><?php echo esc_html($parceiro->telefone); ?></span>
                    </div>
                    
                    <?php if (!empty($parceiro->endereco)): ?>
                        <div class="parceiro-info">
                            <i>📍</i>
                            <span><?php echo esc_html($parceiro->endereco); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="parceiro-info">
                        <i>✉️</i>
                        <span><?php echo esc_html($parceiro->email); ?></span>
                    </div>
                    
                    <?php if (!empty($parceiro->site)): ?>
                        <div class="parceiro-info">
                            <i>🌐</i>
                            <span><a href="<?php echo esc_url($parceiro->site); ?>" target="_blank">Visitar Site</a></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="parceiro-acoes">
                        <a href="mailto:<?php echo esc_attr($parceiro->email); ?>" class="botao-contato">Entrar em Contato</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>