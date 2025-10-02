<div class="wrap">
    <h1>Gerenciar Parceiros</h1>
    
    <div class="search-box">
        <label class="screen-reader-text" for="partner-search-input">Pesquisar Parceiros:</label>
        <input type="search" id="partner-search-input" name="s" value="" placeholder="Pesquisar parceiros...">
        <input type="submit" id="search-submit" class="button" value="Pesquisar">
    </div>
    
    <a href="<?php echo admin_url('admin.php?page=adicionar-parceiro'); ?>" class="botao-adicionar">Adicionar Novo Parceiro</a>

    <?php if (isset($_GET['mensagem'])): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php 
                switch ($_GET['mensagem']) {
                    case 'adicionado':
                        echo 'Parceiro adicionado com sucesso!';
                        break;
                    case 'editado':
                        echo 'Parceiro editado com sucesso!';
                        break;
                    case 'excluido':
                        echo 'Parceiro excluído com sucesso!';
                        break;
                }
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <div class="parceiros-lista-admin">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Setor</th>
                    <th>Categoria</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($parceiros)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Nenhum parceiro cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($parceiros as $parceiro): ?>
                        <tr>
                            <td><?php echo esc_html($parceiro->nome); ?></td>
                            <td><?php echo esc_html($parceiro->setor); ?></td>
                            <td><?php echo esc_html($parceiro->categoria); ?></td>
                            <td><?php echo esc_html($parceiro->telefone); ?></td>
                            <td><?php echo esc_html($parceiro->email); ?></td>
                            <td><?php echo $parceiro->ativo ? 'Ativo' : 'Inativo'; ?></td>
                            <td>
                                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="editar_parceiro">
                                    <input type="hidden" name="id" value="<?php echo $parceiro->id; ?>">
                                    <?php wp_nonce_field('editar_parceiro'); ?>
                                    <button type="submit" class="acao-botao botao-editar">Editar</button>
                                </form>
                                
                                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="excluir_parceiro">
                                    <input type="hidden" name="id" value="<?php echo $parceiro->id; ?>">
                                    <?php wp_nonce_field('excluir_parceiro'); ?>
                                    <button type="submit" class="acao-botao botao-excluir" onclick="return confirm('Tem certeza?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>