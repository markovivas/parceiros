<?php
/*
Plugin Name: Sistema de Parceiros
Description: Sistema para gerenciar parceiros/parcerias com filtros e shortcode
Version: 1.0
Author: Seu Nome
*/

// Evitar acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Classe principal do plugin
class SistemaParceiros {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'ativar_plugin'));
    }
    
    public function ativar_plugin() {
        $this->criar_tabela_parceiros();
        $this->inserir_dados_exemplo();
    }
    
    // Inicializar o plugin
    public function init() {
        add_action('wp_enqueue_scripts', array($this, 'carregar_recursos'));
        add_shortcode('parceiros', array($this, 'shortcode_parceiros'));
        
        // Para área administrativa
        if (is_admin()) {
            add_action('admin_menu', array($this, 'adicionar_menu_admin'));
            add_action('admin_post_adicionar_parceiro', array($this, 'processar_form_adicionar'));
            add_action('admin_post_editar_parceiro', array($this, 'processar_form_editar'));
            add_action('admin_post_excluir_parceiro', array($this, 'processar_form_excluir'));
        }
    }
    
    // Carregar CSS e JS
    public function carregar_recursos() {
        wp_enqueue_style('parceiros-css', plugin_dir_url(__FILE__) . 'css/parceiros.css');
        wp_enqueue_script('parceiros-js', plugin_dir_url(__FILE__) . 'js/parceiros.js', array('jquery'), '1.0', true);
        
        // Localizar script para AJAX
        wp_localize_script('parceiros-js', 'parceiros_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('parceiros_nonce')
        ));
    }
    
    // Criar tabela no banco de dados
    private function criar_tabela_parceiros() {
        global $wpdb;
        
        $tabela_parceiros = $wpdb->prefix . 'parceiros';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $tabela_parceiros (
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
    
    // Inserir dados de exemplo
    private function inserir_dados_exemplo() {
        global $wpdb;
        $tabela_parceiros = $wpdb->prefix . 'parceiros';
        
        $parceiros_exemplo = array(
            array(
                'nome' => 'Empresa Tech Solutions',
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
            )
        );
        
        foreach ($parceiros_exemplo as $parceiro) {
            $wpdb->insert($tabela_parceiros, $parceiro);
        }
    }
    
    // Shortcode para exibir parceiros
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
    
    // Exibir parceiros no frontend
    private function exibir_parceiros($filtros = array()) {
        global $wpdb;
        $tabela_parceiros = $wpdb->prefix . 'parceiros';
        
        // Construir query com filtros
        $where = "WHERE ativo = 1";
        if (!empty($filtros['categoria'])) {
            $where .= $wpdb->prepare(" AND categoria = %s", $filtros['categoria']);
        }
        if (!empty($filtros['setor'])) {
            $where .= $wpdb->prepare(" AND setor = %s", $filtros['setor']);
        }
        
        $limite = '';
        if ($filtros['limite'] > 0) {
            $limite = "LIMIT " . intval($filtros['limite']);
        }
        
        $parceiros = $wpdb->get_results("
            SELECT * FROM $tabela_parceiros 
            $where 
            ORDER BY nome 
            $limite
        ");
        
        // Obter setores e categorias únicas para os filtros
        $setores = $wpdb->get_col("SELECT DISTINCT setor FROM $tabela_parceiros WHERE ativo = 1 AND setor != '' ORDER BY setor");
        $categorias = $wpdb->get_col("SELECT DISTINCT categoria FROM $tabela_parceiros WHERE ativo = 1 AND categoria != '' ORDER BY categoria");
        
        // Incluir template
        include plugin_dir_path(__FILE__) . 'templates/lista-parceiros.php';
    }
    
    // Adicionar menu administrativo
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
            'Adicionar Parceiro',
            'manage_options',
            'adicionar-parceiro',
            array($this, 'pagina_adicionar_parceiro')
        );
    }
    
    // Página administrativa principal
    public function pagina_admin_parceiros() {
        global $wpdb;
        $tabela_parceiros = $wpdb->prefix . 'parceiros';
        
        $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $sql = "SELECT * FROM $tabela_parceiros";
        if (!empty($search_term)) {
            $sql .= $wpdb->prepare(" WHERE nome LIKE '%%%s%%'", $search_term);
        }
        $sql .= " ORDER BY nome";
        $parceiros = $wpdb->get_results($sql);
        
        include plugin_dir_path(__FILE__) . 'admin/lista-parceiros.php';
    }
    
    // Página para adicionar parceiro
    public function pagina_adicionar_parceiro() {
        include plugin_dir_path(__FILE__) . 'admin/form-parceiro.php';
    }
    
    // Processar formulário de adicionar parceiro
    public function processar_form_adicionar() {
        if (!current_user_can('manage_options')) {
            wp_die('Sem permissão');
        }
        
        check_admin_referer('adicionar_parceiro');
        
        global $wpdb;
        $tabela_parceiros = $wpdb->prefix . 'parceiros';
        
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
        
        // Processar upload de logo
        if (!empty($_FILES['logo']['name'])) {
            $file = $_FILES['logo'];
            $allowed_types = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif');
            $max_size = 2 * 1024 * 1024; // 2MB
            
            // Validate file type
            $file_type = wp_check_filetype($file['name']);
            if (!array_key_exists($file_type['ext'], $allowed_types)) {
                wp_die("Erro: Formato de arquivo inválido. Apenas JPG, PNG e GIF são permitidos.");
            }
            
            // Validate file size
            if ($file['size'] > $max_size) {
                wp_die("Erro: O arquivo é muito grande. O tamanho máximo permitido é 2MB.");
            }
            
            // Handle the upload
            $upload = wp_handle_upload($file, array('test_form' => false));
        
             if ($upload && !isset($upload['error'])) {
                  $dados['logo'] = $upload['url'];
             } else
                  wp_die('Erro ao fazer upload do logo: ' . $upload['error']);
        }
        
        $wpdb->insert($tabela_parceiros, $dados);
        
        wp_redirect(admin_url('admin.php?page=parceiros&mensagem=adicionado'));
        exit;
    }
    
    // Processar formulário de editar parceiro
    public function processar_form_editar() {
        if (!current_user_can('manage_options')) {
            wp_die('Sem permissão');
        }
        
        check_admin_referer('editar_parceiro');
        
        global $wpdb;
        $tabela_parceiros = $wpdb->prefix . 'parceiros';
        
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
        
        // Processar upload de logo
        if (!empty($_FILES['logo']['name'])) {
             $file = $_FILES['logo'];
             $allowed_types = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif');
             $max_size = 2 * 1024 * 1024; // 2MB
            
             // Validate file type
             $file_type = wp_check_filetype($file['name']);
             if (!array_key_exists($file_type['ext'], $allowed_types)) {
                  wp_die("Erro: Formato de arquivo inválido. Apenas JPG, PNG e GIF são permitidos.");
            }
            
             // Validate file size
             if ($file['size'] > $max_size) {
                  wp_die("Erro: O arquivo é muito grande. O tamanho máximo permitido é 2MB.");
             }
            
             // Handle the upload
             $upload = wp_handle_upload($file, array('test_form' => false));
        
             if ($upload && !isset($upload['error'])) {
                  $dados['logo'] = $upload['url'];
             } else
                  wp_die('Erro ao fazer upload do logo: ' . $upload['error']);
        }
        
        $wpdb->update($tabela_parceiros, $dados, array('id' => $id));
        
        wp_redirect(admin_url('admin.php?page=parceiros&mensagem=editado'));
        exit;
    }
    
    // Processar exclusão de parceiro
    public function processar_form_excluir() {
        if (!current_user_can('manage_options')) {
            wp_die('Sem permissão');
        }
        
        check_admin_referer('excluir_parceiro');
        
        global $wpdb;
        $tabela_parceiros = $wpdb->prefix . 'parceiros';
        
        $id = intval($_POST['id']);
        $wpdb->delete($tabela_parceiros, array('id' => $id));
        
        wp_redirect(admin_url('admin.php?page=parceiros&mensagem=excluido'));
        exit;
    }
}

// Inicializar o plugin
new SistemaParceiros();

// Funções auxiliares
function obter_parceiros($filtros = array()) {
    global $wpdb;
    $tabela_parceiros = $wpdb->prefix . 'parceiros';
    
    $where = "WHERE ativo = 1";
    if (!empty($filtros['categoria'])) {
        $where .= $wpdb->prepare(" AND categoria = %s", $filtros['categoria']);
    }
    if (!empty($filtros['setor'])) {
        $where .= $wpdb->prepare(" AND setor = %s", $filtros['setor']);
    }
    
    return $wpdb->get_results("SELECT * FROM $tabela_parceiros $where ORDER BY nome");
}
?>