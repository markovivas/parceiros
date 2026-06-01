<div class="parceiros-container">
    <div class="parceiros-filtros">
        <div class="filtro-row">
            <div class="filtro-grupo filtro-pesquisa">
                <label for="pesquisa-parceiros">
                    <svg class="icone-search" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Buscar Parceiro
                </label>
                <input type="text" id="pesquisa-parceiros" placeholder="Digite o nome do parceiro..." autocomplete="off">
            </div>
        </div>

        <div class="filtro-row filtro-selects">
            <div class="filtro-grupo">
                <label for="filtro-setor">Setor</label>
                <div class="select-wrapper">
                    <select id="filtro-setor">
                        <option value="">Todos os Setores</option>
                        <?php foreach ($setores as $setor): ?>
                            <option value="<?php echo esc_attr($setor); ?>"><?php echo esc_html($setor); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <svg class="icone-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </div>

            <div class="filtro-grupo">
                <label for="filtro-categoria">Categoria</label>
                <div class="select-wrapper">
                    <select id="filtro-categoria">
                        <option value="">Todas as Categorias</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo esc_attr($categoria); ?>"><?php echo esc_html($categoria); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <svg class="icone-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </div>

            <div class="filtro-grupo filtro-acoes">
                <label>&nbsp;</label>
                <button type="button" class="botao-limpar-filtros" id="limpar-filtros">Limpar Filtros</button>
            </div>
        </div>
    </div>

    <div class="resultados-info">
        <span id="contagem-resultados"></span>
    </div>

    <?php if (empty($parceiros)): ?>
        <div class="sem-parceiros">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <p>Nenhum parceiro encontrado.</p>
        </div>
    <?php else: ?>
        <div class="grid-parceiros" id="grid-parceiros">
            <?php foreach ($parceiros as $parceiro): ?>
                <div class="card-parceiro" data-setor="<?php echo esc_attr($parceiro->setor); ?>" data-categoria="<?php echo esc_attr($parceiro->categoria); ?>" data-nome="<?php echo esc_attr(strtolower($parceiro->nome)); ?>">
                    <div class="card-header">
                        <div class="parceiro-logo">
                            <?php if (!empty($parceiro->logo)): ?>
                                <img src="<?php echo esc_url($parceiro->logo); ?>" alt="<?php echo esc_attr($parceiro->nome); ?>" loading="lazy">
                            <?php else: ?>
                                <div class="logo-placeholder">
                                    <?php echo esc_html(substr($parceiro->nome, 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <h3 class="parceiro-nome"><?php echo esc_html($parceiro->nome); ?></h3>

                        <div class="parceiro-tags">
                            <?php if (!empty($parceiro->setor)): ?>
                                <span class="tag tag-setor"><?php echo esc_html($parceiro->setor); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($parceiro->categoria)): ?>
                                <span class="tag tag-categoria"><?php echo esc_html($parceiro->categoria); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="parceiro-detalhes">
                            <div class="detalhe-item">
                                <svg class="detalhe-icone" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                <span><?php echo esc_html($parceiro->telefone); ?></span>
                            </div>

                            <div class="detalhe-item">
                                <svg class="detalhe-icone" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                <span><?php echo esc_html($parceiro->email); ?></span>
                            </div>

                            <?php if (!empty($parceiro->endereco)): ?>
                                <div class="detalhe-item">
                                    <svg class="detalhe-icone" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span><?php echo esc_html($parceiro->endereco); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        <?php if (!empty($parceiro->site)): ?>
                            <a href="<?php echo esc_url($parceiro->site); ?>" target="_blank" rel="noopener noreferrer" class="botao-site" title="Visitar Site">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php
                            $whatsapp = preg_replace('/\D/', '', $parceiro->telefone);
                            if (substr($whatsapp, 0, 2) !== '55') {
                                $whatsapp = '55' . $whatsapp;
                            }
                        ?>
                        <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" target="_blank" rel="noopener noreferrer" class="botao-contato" title="Falar pelo WhatsApp">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
