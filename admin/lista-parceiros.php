<div class="wrap parceiros-admin">
    <h1 class="wp-heading-inline">Parceiros</h1>
    <a href="<?php echo admin_url('admin.php?page=adicionar-parceiro'); ?>" class="page-title-action">Adicionar Novo</a>

    <?php if (isset($_GET['mensagem'])): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php
                $mensagens = array(
                    'adicionado' => 'Parceiro adicionado com sucesso!',
                    'editado'    => 'Parceiro editado com sucesso!',
                    'excluido'   => 'Parceiro excluído com sucesso!',
                );
                $msg = sanitize_text_field($_GET['mensagem']);
                echo isset($mensagens[$msg]) ? esc_html($mensagens[$msg]) : '';
                ?>
            </p>
        </div>
    <?php endif; ?>

    <form method="get" action="<?php echo admin_url('admin.php'); ?>" class="search-form">
        <input type="hidden" name="page" value="parceiros">
        <p class="search-box">
            <label class="screen-reader-text" for="partner-search-input">Pesquisar Parceiros:</label>
            <input type="search" id="partner-search-input" name="s" value="<?php echo esc_attr($search_term); ?>" placeholder="Pesquisar por nome...">
            <input type="submit" id="search-submit" class="button" value="Pesquisar">
        </p>
    </form>

    <div class="tablenav top">
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $total; ?> item(ns)</span>
            <?php if ($total_paginas > 1): ?>
                <?php echo paginate_links(array(
                    'base'      => add_query_arg('paged', '%#%'),
                    'format'    => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total'     => $total_paginas,
                    'current'   => $paged,
                )); ?>
            <?php endif; ?>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped parceiros-table">
        <thead>
            <tr>
                <th scope="col" class="column-nome<?php echo $orderby === 'nome' ? ' sorted ' . strtolower($order) : ''; ?>">
                    <a href="<?php echo add_query_arg(array('orderby' => 'nome', 'order' => $orderby === 'nome' && $order === 'ASC' ? 'DESC' : 'ASC')); ?>">
                        <span>Nome</span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
                <th scope="col" class="column-setor<?php echo $orderby === 'setor' ? ' sorted ' . strtolower($order) : ''; ?>">
                    <a href="<?php echo add_query_arg(array('orderby' => 'setor', 'order' => $orderby === 'setor' && $order === 'ASC' ? 'DESC' : 'ASC')); ?>">
                        <span>Setor</span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
                <th scope="col">Categoria</th>
                <th scope="col">Telefone</th>
                <th scope="col">E-mail</th>
                <th scope="col">Status</th>
                <th scope="col">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($parceiros)): ?>
                <tr>
                    <td colspan="7" class="colspanchange">
                        Nenhum parceiro encontrado.
                        <?php if (!empty($search_term)): ?>
                            <a href="<?php echo admin_url('admin.php?page=parceiros'); ?>">Limpar busca</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($parceiros as $parceiro): ?>
                    <tr>
                        <td class="column-nome">
                            <strong>
                                <a href="<?php echo admin_url('admin.php?page=adicionar-parceiro&id=' . $parceiro->id); ?>">
                                    <?php echo esc_html($parceiro->nome); ?>
                                </a>
                            </strong>
                        </td>
                        <td><?php echo esc_html($parceiro->setor); ?></td>
                        <td>
                            <span class="categoria-tag"><?php echo esc_html($parceiro->categoria); ?></span>
                        </td>
                        <td><?php echo esc_html($parceiro->telefone); ?></td>
                        <td>
                            <a href="mailto:<?php echo esc_attr($parceiro->email); ?>"><?php echo esc_html($parceiro->email); ?></a>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $parceiro->ativo ? 'status-ativo' : 'status-inativo'; ?>">
                                <?php echo $parceiro->ativo ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </td>
                        <td class="acoes-cell">
                            <a href="<?php echo admin_url('admin.php?page=adicionar-parceiro&id=' . $parceiro->id); ?>" class="button button-small button-secondary">
                                Editar
                            </a>
                            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline" onsubmit="return confirm('Excluir &quot;<?php echo esc_js($parceiro->nome); ?>&quot; permanentemente?')">
                                <input type="hidden" name="action" value="excluir_parceiro">
                                <input type="hidden" name="id" value="<?php echo $parceiro->id; ?>">
                                <?php wp_nonce_field('excluir_parceiro'); ?>
                                <button type="submit" class="button button-small button-link-delete">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th scope="col">Nome</th>
                <th scope="col">Setor</th>
                <th scope="col">Categoria</th>
                <th scope="col">Telefone</th>
                <th scope="col">E-mail</th>
                <th scope="col">Status</th>
                <th scope="col">Ações</th>
            </tr>
        </tfoot>
    </table>

    <div class="tablenav bottom">
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $total; ?> item(ns)</span>
            <?php if ($total_paginas > 1): ?>
                <?php echo paginate_links(array(
                    'base'      => add_query_arg('paged', '%#%'),
                    'format'    => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total'     => $total_paginas,
                    'current'   => $paged,
                )); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
