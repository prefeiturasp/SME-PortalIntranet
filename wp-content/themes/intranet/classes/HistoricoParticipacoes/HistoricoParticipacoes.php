<?php
class Historico_Participacoes {

    private $alert = null;
    private $cpf = null;
    private $dados_participante = null;
    private $eventos = null;
    private $validation_errors = [];

    function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    function admin_menu() {
        add_submenu_page(
            'edit.php',                             // Menu pai: Posts
            'Histórico de Participantes',           // Título da página
            'Histórico de Participantes',           // Texto do menu
            'manage_options',                       // Capabilidade
            'historico-participantes',              // Slug
            array( $this, 'render_page' )           // Callback
        );
    }

    function register_metaboxes() {

        // Metabox da busca
        add_meta_box(
            'historico_participante_busca',         // ID
            'Localizar Histórico do Participante',  // Título
            array( $this, 'box_form' ),             // Callback
            'historico-participantes',              // Tela (slug da página)
            'normal',                               // Contexto
            'default'                               // Prioridade
        );

        // Metabox com os dados do participante buscado
        if ( $this->dados_participante ) {
            add_meta_box(
                'dados_participante_container',             // ID
                'Dados do Participante',                    // Título
                function() {
                    $this->box_dados_participante( $this->dados_participante );
                },
                'historico-participantes',                  // Tela
                'normal',                                   // Contexto
                'default'
            );
        }

        // Metabox com a listagem dos eventos
        add_meta_box(
            'historico_eventos_container',  // ID
            'Listagem de Eventos',          // Título
            array( $this, 'box_eventos' ),   // Callback
            'historico-participantes',      // Tela
            'normal',                       // Contexto
            'default'
        );
    }

    function box_form() {
        ?>
        <form class="form-group mt-3">
            <input type="hidden" name="page" value="historico-participantes">
            <input type="hidden" name="action" value="busca-por-cpf">

            <div class="row justify-content-between align-items-center">
                <div class="col-10">
                    <label for="cpf">CPF do Participante</label>
                    <input
                        type="text"
                        name="cpf"
                        id="cpf"
                        placeholder="000.000.000-00"
                        class="cpf form-control"
                        value="<?php echo esc_html( $_GET['cpf'] ?? "" ); ?>"
                        required
                    >
                </div>

                <div class="col-2 align-self-end">
                    <button id="buscar-participante" class="btn btn-laranja btn-block">Buscar</button>
                </div>
            </div>
        </form>
        <?php

        //Exibe o erro de validação
        $this->show_validation_error( 'cpf' );
    }

    function box_dados_participante( $dados_participante ) {
        
        $sancao_ativa = $this->check_sancao_ativa_participante( $dados_participante->cpf );

        get_template_part( 'classes/HistoricoParticipacoes/template-parts/dados-participante', null, [
            'dados_participante' => $dados_participante,
            'sancao_ativa' => $sancao_ativa
        ]);
    }

    function box_eventos() {
        get_template_part( 'classes/HistoricoParticipacoes/template-parts/lista-eventos', null, [ 'eventos' => $this->eventos ] );
    }

    function render_page() {
        // Scripts/estilos necessários para postboxes
        wp_enqueue_script('postbox');
        wp_enqueue_script('dashboard');
        wp_enqueue_style('dashboard');
        wp_enqueue_script('sorteio-js');
        wp_enqueue_style('bootstrap-sorteio-css');
        wp_enqueue_script('bootstrap-sorteio-js');
        wp_enqueue_style('toastr-sorteio-css');
        wp_enqueue_script('toastr-sorteio-js');
        wp_enqueue_style('sweetalert-sorteio-css');
	    wp_enqueue_script('sweetalert-sorteio-js');
        wp_enqueue_style('select2-bootstrap4-css', 'https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css');

        // Adiciona as tratativas e realiza as ações ao submeter o formulário
        $this->handle_request();
        ?>
        <div class="wrap">
            <h1>Histórico de Participantes</h1>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    
                    <!-- Conteúdo principal -->
                    <div id="post-body-content">
                        <?php 
                        $this->register_metaboxes();
                        do_meta_boxes( 'historico-participantes', 'normal', null ); 
                        ?>
                    </div>

                    <!-- Sidebar -->
                    <div id="postbox-container-1" class="postbox-container">
                        <?php do_meta_boxes( 'historico-participantes', 'side', null ); ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
            jQuery(document).ready(function($){
                postboxes.add_postbox_toggles('historico-participantes'); // mesmo slug do submenu
                $('.cpf').mask('000.000.000-00'); // Máscara para o CPF 
            });
        </script>

        <?php
        if ( $this->alert ) :
            ?>
            <script>
                jQuery(document).ready(function(){
                    Swal.fire({
                        icon: '<?php echo esc_js( $this->alert['tipo'] ); ?>',
                        title: '<?php echo esc_js( $this->alert['titulo'] ); ?>',
                        text: '<?php echo esc_js( $this->alert['mensagem'] ); ?>',
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Fechar'
                    });
                });
            </script>
            <?php
        endif;
    }

    private function handle_request() {

        if ( isset( $_GET['action'] ) && $_GET['action'] === 'busca-por-cpf' ) {

            if ( empty( $_GET['cpf'] ) ) {
                $this->add_validation_error( 'cpf', 'O campo CPF é obrigatório.' );
                return;
            }

            $cpf = preg_replace( '/\D/', '', $_GET['cpf'] );

            if ( strlen( $cpf ) !== 11 ) {
                $this->add_validation_error( 'cpf', 'CPF inválido. O CPF deve ter 11 dígitos.' );
                return;
            }

            $this->cpf = $cpf;
            $dados = $this->get_dados_participante( $cpf );

            if ( !$dados ) {
                $this->show_alert( 'Aviso', 'Não foram encontradas inscrições para o CPF informado.', 'info' );
                return;
            }

            $this->dados_participante = $dados;
            $this->eventos = $this->get_eventos_participante( $cpf );
        }
    }

    private function get_dados_participante( string $cpf ) {
        global $wpdb;
        
        $dados_mais_recentes = null;

        $sorteio = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    user_id,
                    cpf,
                    nome_completo,
                    email_institucional,
                    email_secundario,
                    celular,
                    telefone_comercial,
                    dre,
                    cargo_principal,
                    unidade_setor
                FROM {$wpdb->prefix}inscricoes
                WHERE cpf = %s
                ORDER BY data_inscricao DESC
                LIMIT 1",
                $cpf
            )
        );

        $cortesia = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    user_id,
                    cpf,
                    nome_completo,
                    email_institucional,
                    email_secundario,
                    celular,
                    telefone_comercial,
                    dre,
                    cargo_principal,
                    unidade_setor
                FROM {$wpdb->prefix}cortesias_inscricoes
                WHERE cpf = %s
                ORDER BY data_inscricao DESC
                LIMIT 1",
                $cpf
            )
        );

        if ( $sorteio && $cortesia ) {

            $dados_mais_recentes = strtotime($sorteio->data_inscricao) > strtotime($cortesia->data_concessao)
                ? $sorteio
                : $cortesia;

        } elseif ( $sorteio ) {
            $dados_mais_recentes = $sorteio;

        } elseif ( $cortesia ) {
            $dados_mais_recentes = $cortesia;
        }

        return $dados_mais_recentes;
    }

    private function get_eventos_participante(string $cpf) {
        global $wpdb;

        $sorteios = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT 
                    i.id,
                    i.post_id,
                    i.sorteado,
                    i.confirmou_presenca,
                    i.enviou_email_instrucoes,
                    i.compareceu,
                    i.data_inscricao,
                    p.post_title AS nome_evento,
                    'sorteio' AS tipo
                FROM {$wpdb->prefix}inscricoes i
                INNER JOIN {$wpdb->posts} p ON p.ID = i.post_id
                WHERE i.cpf = %s",
                $cpf
            )

        );

        $cortesias = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT 
                    i.id,
                    i.post_id,
                    i.confirmou_presenca,
                    i.enviou_email_instrucoes,
                    i.compareceu,
                    i.data_inscricao,
                    p.post_title AS nome_evento,
                    'cortesia' AS tipo
                FROM {$wpdb->prefix}cortesias_inscricoes i
                INNER JOIN {$wpdb->posts} p ON p.ID = i.post_id
                WHERE i.cpf = %s",
                $cpf
            )
        );

        $eventos = array_merge( $sorteios, $cortesias );

        usort($eventos, function ($a, $b) {
            return strtotime($b->data_inscricao) - strtotime($a->data_inscricao);
        });

        return $eventos;
    }

    private function check_sancao_ativa_participante( string $cpf ) {
        global $wpdb;

        $hoje = obter_data_com_timezone( 'Y-m-d', 'America/Sao_Paulo' );
        $tabela_sancoes = $wpdb->prefix . 'inscricao_sancoes';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, data_validade FROM $tabela_sancoes WHERE cpf = %s AND data_validade > %s",
                $cpf,
                $hoje
            ),
            ARRAY_A
        );
    }

    private function show_alert( string $titulo, string $mensagem, string $tipo = 'info' ) {
        $this->alert = [
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'tipo' => $tipo,
        ];
    }

    private function add_validation_error( $campo, $mensagem ) {
        $this->validation_errors[$campo] = $mensagem;
    }

    private function show_validation_error( $campo ) {

        if ( isset( $this->validation_errors[$campo] ) ) {
            echo '<span class="notice notice-error inline mt-2">' . $this->validation_errors[$campo] . '</span>';
        }
    }
}

new Historico_Participacoes;