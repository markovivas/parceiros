<?php
/*
Plugin Name: Sistema de Parceiros
Description: Sistema para gerenciar parceiros/parcerias com filtros e shortcode
Version: 2.0
Author: Seu Nome
*/

if (!defined('ABSPATH')) {
    exit;
}

class SistemaParceiros {

    private $tabela;

    public function __construct() {
        global $wpdb;
        $this->tabela = $wpdb->prefix . 'parceiros';

        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'ativar_plugin'));
    }

    public function ativar_plugin() {
        $this->criar_tabela_parceiros();
        $this->inserir_dados_exemplo();
    }

    private function criar_tabela_parceiros() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->tabela} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            nome varchar(255) NOT NULL,
            logo varchar(255),
            telefone varchar(20),
            endereco text,
            email varchar(100),
            site varchar(255),
            setor varchar(100),
            categoria varchar(100),
            ativo tinyint(1) DEFAULT 1,
            data_criacao datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private function inserir_dados_exemplo() {
        global $wpdb;

        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$this->tabela}");
        if ($total > 0) {
            return;
        }

        $parceiros_exemplo = array(
            array(
                'nome' => 'Tech Solutions',
                'logo' => '',
                'telefone' => '(11) 1234-5678',
                'endereco' => 'Av. Paulista, 1000 - São Paulo, SP',
                'email' => 'contato@techsolutions.com',
                'site' => 'https://techsolutions.com',
                'setor' => 'Tecnologia',
                'categoria' => 'Desenvolvimento'
            ),
            array(
                'nome' => 'Marketing Digital Pro',
                'logo' => '',
                'telefone' => '(21) 9876-5432',
                'endereco' => 'Rua do Ouvidor, 50 - Rio de Janeiro, RJ',
                'email' => 'contato@marketingpro.com',
                'site' => 'https://marketingpro.com',
                'setor' => 'Marketing',
                'categoria' => 'Digital'
            ),
            array(
                'nome' => 'Consultoria Gestão & Negócios',
                'logo' => '',
                'telefone' => '(31) 3333-4444',
                'endereco' => 'Av. Amazonas, 500 - Belo Horizonte, MG',
                'email' => 'contato@consultoriagn.com.br',
                'site' => 'https://consultoriagn.com.br',
                'setor' => 'Consultoria',
                'categoria' => 'Gestão'
            ),
            array(
                'nome' => 'Saúde Total Clínica',
                'logo' => '',
                'telefone' => '(41) 3222-1111',
                'endereco' => 'Rua XV de Novembro, 200 - Curitiba, PR',
                'email' => 'contato@saudetotal.com.br',
                'site' => 'https://saudetotal.com.br',
                'setor' => 'Saúde',
                'categoria' => 'Clínica'
            ),
        );

        foreach ($parceiros_exemplo as $parceiro) {
            $wpdb->insert($this->tabela, $parceiro);
        }
    }

    public function init() {
        add_action('wp_enqueue_scripts', array($this, 'carregar_recursos'));
        add_shortcode('parceiros', array($this, 'shortcode_parceiros'));

        if (is_admin()) {
            add_action('admin_menu', array($this, 'adicionar_menu_admin'));
            add_action('admin_post_adicionar_parceiro', array($this, 'processar_form_adicionar'));
            add_action('admin_post_editar_parceiro', array($this, 'processar_form_editar'));
            add_action('admin_post_excluir_parceiro', array($this, 'processar_form_excluir'));
            add_action('admin_enqueue_scripts', array($this, 'carregar_recursos_admin'));
        }
    }

    public function carregar_recursos() {
        $versao = '2.0';
        wp_enqueue_style('parceiros-css', plugin_dir_url(__FILE__) . 'css/parceiros.css', array(), $versao);
        wp_enqueue_script('parceiros-js', plugin_dir_url(__FILE__) . 'js/parceiros.js', array('jquery'), $versao, true);

        wp_localize_script('parceiros-js', 'parceiros_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('parceiros_nonce')
        ));
    }

    public function carregar_recursos_admin($hook) {
        if (strpos($hook, 'parceiros') === false && strpos($hook, 'adicionar-parceiro') === false) {
            return;
        }
        wp_enqueue_style('parceiros-admin-css', plugin_dir_url(__FILE__) . 'css/parceiros.css', array(), '2.0');
    }

    public function shortcode_parceiros($atts) {
        $atts = shortcode_atts(array(
            'categoria' => '',
            'setor' => '',
            'limite' => -1
        ), $atts);

        ob_start();
        $this->exibir_parceiros($atts);
        return ob_get_clean();
    }

    private function exibir_parceiros($filtros = array()) {
        global $wpdb;

        $where = "WHERE ativo = 1";
        $prep = array();

        if (!empty($filtros['categoria'])) {
            $where .= " AND categoria = %s";
            $prep[] = $filtros['categoria'];
        }
        if (!empty($filtros['setor'])) {
            $where .= " AND setor = %s";
            $prep[] = $filtros['setor'];
        }

        $limite = '';
        if ($filtros['limite'] > 0) {
            $limite = "LIMIT " . intval($filtros['limite']);
        }

        if (!empty($prep)) {
            $parceiros = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$this->tabela} $where ORDER BY nome $limite", $prep
            ));
        } else {
            $parceiros = $wpdb->get_results(
                "SELECT * FROM {$this->tabela} $where ORDER BY nome $limite"
            );
        }

        $setores = $wpdb->get_col("SELECT DISTINCT setor FROM {$this->tabela} WHERE ativo = 1 AND setor != '' ORDER BY setor");
        $categorias = $wpdb->get_col("SELECT DISTINCT categoria FROM {$this->tabela} WHERE ativo = 1 AND categoria != '' ORDER BY categoria");

        include plugin_dir_path(__FILE__) . 'templates/lista-parceiros.php';
    }

    public function adicionar_menu_admin() {
        add_menu_page(
            'Parceiros',
            'Parceiros',
            'manage_options',
            'parceiros',
            array($this, 'pagina_admin_parceiros'),
            'dashicons-groups',
            30
        );

        add_submenu_page(
            'parceiros',
            'Todos os Parceiros',
            'Todos os Parceiros',
            'manage_options',
            'parceiros',
            array($this, 'pagina_admin_parceiros')
        );

        add_submenu_page(
            'parceiros',
            'Adicionar Parceiro',
            'Adicionar Novo',
            'manage_options',
            'adicionar-parceiro',
            array($this, 'pagina_adicionar_parceiro')
        );
    }

    public function pagina_admin_parceiros() {
        if (!current_user_can('manage_options')) {
            wp_die('Acesso negado.');
        }

        global $wpdb;
        $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        $offset = ($paged - 1) * $per_page;
        $orderby = isset($_GET['orderby']) ? sanitize_sql_orderby($_GET['orderby']) : 'nome';
        $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';

        $allowed_orderby = array('nome', 'setor', 'categoria', 'telefone', 'email', 'ativo', 'data_criacao');
        if (!in_array($orderby, $allowed_orderby)) {
            $orderby = 'nome';
        }

        $where = '';
        $prep = array();
        if (!empty($search_term)) {
            $where = "WHERE nome LIKE %s";
            $prep[] = '%' . $wpdb->esc_like($search_term) . '%';
        }

        $total = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM {$this->tabela} $where", $prep)
        );

        $sql = $wpdb->prepare(
            "SELECT * FROM {$this->tabela} $where ORDER BY $orderby $order LIMIT %d OFFSET %d",
            array_merge($prep, array($per_page, $offset))
        );
        $parceiros = $wpdb->get_results($sql);

        $total_paginas = ceil($total / $per_page);

        include plugin_dir_path(__FILE__) . 'admin/lista-parceiros.php';
    }

    public function pagina_adicionar_parceiro() {
        if (!current_user_can('manage_options')) {
            wp_die('Acesso negado.');
        }
        include plugin_dir_path(__FILE__) . 'admin/form-parceiro.php';
    }

    private function validar_upload_logo($file) {
        $allowed_types = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp');
        $max_size = 2 * 1024 * 1024;

        $file_type = wp_check_filetype($file['name']);
        if (!isset($allowed_types[$file_type['ext']])) {
            return new WP_Error('invalid_format', 'Formato inválido. Use JPG, PNG, GIF ou WebP.');
        }

        if ($file['size'] > $max_size) {
            return new WP_Error('file_too_large', 'Arquivo muito grande. Máximo: 2MB.');
        }

        $upload = wp_handle_upload($file, array('test_form' => false));
        if ($upload && !isset($upload['error'])) {
            return $upload['url'];
        }

        return new WP_Error('upload_error', $upload['error']);
    }

    public function processar_form_adicionar() {
        if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['_wpnonce'], 'adicionar_parceiro')) {
            wp_die('Erro de segurança.');
        }

        global $wpdb;

        $dados = array(
            'nome' => sanitize_text_field($_POST['nome']),
            'telefone' => sanitize_text_field($_POST['telefone']),
            'endereco' => sanitize_textarea_field($_POST['endereco']),
            'email' => sanitize_email($_POST['email']),
            'site' => esc_url_raw($_POST['site']),
            'setor' => sanitize_text_field($_POST['setor']),
            'categoria' => sanitize_text_field($_POST['categoria']),
            'ativo' => isset($_POST['ativo']) ? 1 : 0
        );

        if (!empty($_FILES['logo']['name'])) {
            $upload = $this->validar_upload_logo($_FILES['logo']);
            if (is_wp_error($upload)) {
                wp_die($upload->get_error_message());
            }
            $dados['logo'] = $upload;
        }

        $wpdb->insert($this->tabela, $dados);

        wp_redirect(admin_url('admin.php?page=parceiros&mensagem=adicionado'));
        exit;
    }

    public function processar_form_editar() {
        if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['_wpnonce'], 'editar_parceiro')) {
            wp_die('Erro de segurança.');
        }

        global $wpdb;
        $id = intval($_POST['id']);

        $dados = array(
            'nome' => sanitize_text_field($_POST['nome']),
            'telefone' => sanitize_text_field($_POST['telefone']),
            'endereco' => sanitize_textarea_field($_POST['endereco']),
            'email' => sanitize_email($_POST['email']),
            'site' => esc_url_raw($_POST['site']),
            'setor' => sanitize_text_field($_POST['setor']),
            'categoria' => sanitize_text_field($_POST['categoria']),
            'ativo' => isset($_POST['ativo']) ? 1 : 0
        );

        if (!empty($_FILES['logo']['name'])) {
            $upload = $this->validar_upload_logo($_FILES['logo']);
            if (is_wp_error($upload)) {
                wp_die($upload->get_error_message());
            }
            $dados['logo'] = $upload;
        }

        $wpdb->update($this->tabela, $dados, array('id' => $id));

        wp_redirect(admin_url('admin.php?page=parceiros&mensagem=editado'));
        exit;
    }

    public function processar_form_excluir() {
        if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['_wpnonce'], 'excluir_parceiro')) {
            wp_die('Erro de segurança.');
        }

        global $wpdb;
        $id = intval($_POST['id']);
        $wpdb->delete($this->tabela, array('id' => $id));

        wp_redirect(admin_url('admin.php?page=parceiros&mensagem=excluido'));
        exit;
    }
}

new SistemaParceiros();

function obter_parceiros($filtros = array()) {
    global $wpdb;
    $tabela = $wpdb->prefix . 'parceiros';

    $where = "WHERE ativo = 1";
    $prep = array();

    if (!empty($filtros['categoria'])) {
        $where .= " AND categoria = %s";
        $prep[] = $filtros['categoria'];
    }
    if (!empty($filtros['setor'])) {
        $where .= " AND setor = %s";
        $prep[] = $filtros['setor'];
    }

    if (!empty($prep)) {
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $tabela $where ORDER BY nome", $prep
        ));
    }

    return $wpdb->get_results("SELECT * FROM $tabela $where ORDER BY nome");
}
