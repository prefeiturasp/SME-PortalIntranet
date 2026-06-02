<?php
class Historico_Participacoes {

    private $alert = null;
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
        $campo_selecionado = $_GET['campo'] ?? 'cpf';
        $valor = sanitize_text_field( $_GET['valor'] ) ?? '';
        ?>
        <form class="form-group mt-3">
            <input type="hidden" name="page" value="historico-participantes">
            <input type="hidden" name="action" value="busca-historico">

            <div class="row justify-content-between align-items-center">
                <div class="col-10">

                    <div class="form-group">
                        <strong>Buscar por:</strong><br>

                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="campo"
                                id="campo-cpf"
                                value="cpf"
                                checked
                                >
                            <label class="form-check-label" for="campo-cpf">CPF</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="campo"
                                id="campo-email"
                                value="email"
                                <?php checked( $_GET['campo'], 'email' ) ?>
                                >
                            <label class="form-check-label" for="campo-email">
                                E-mail (Institucional/Secundário)
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="campo"
                                id="campo-celular"
                                value="celular"
                                <?php checked( $_GET['campo'], 'celular' ) ?>
                                >
                            <label class="form-check-label" for="campo-celular">
                                Telefone celular
                            </label>
                        </div>
                    </div>

                    <div class="form-group campo-busca m-0 <?php echo esc_html( $campo_selecionado !== 'cpf' ? 'd-none' : '' ); ?>" data-campo="cpf">
                        <strong>CPF do Participante</strong>

                        <input
                            type="text"
                            name="valor"
                            id="input-cpf"
                            placeholder="000.000.000-00"
                            class="cpf form-control"
                            value="<?php echo $campo_selecionado === 'cpf' ? $valor : ';' ?>"
                            <?php disabled( $campo_selecionado != 'cpf' ); ?>
                        >
                    </div>

                    <div class="form-group campo-busca m-0 <?php echo esc_html( $campo_selecionado !== 'email' ? 'd-none' : '' ); ?>" data-campo="email">
                        <strong>E-mail (Institucional/Secundário)</strong>

                        <input
                            type="email"
                            name="valor"
                            id="input-email"
                            placeholder="Digite o e-mail"
                            class="form-control"
                            value="<?php echo $campo_selecionado === 'email' ? $valor : ''; ?>"
                            <?php disabled( $campo_selecionado != 'email' ); ?>
                        >
                    </div>

                    <div class="form-group campo-busca m-0 <?php echo esc_html( $campo_selecionado !== 'celular' ? 'd-none' : '' ); ?>" data-campo="celular">
                        <strong>Telefone celular</strong>

                        <input
                            type="text"
                            name="valor"
                            id="input-celular"
                            placeholder="(11) 9XXXX-XXXX"
                            class="form-control"
                            value="<?php echo $campo_selecionado === 'celular' ? $valor : ''; ?>"
                            <?php disabled( $campo_selecionado != 'celular' ); ?>
                        >
                    </div>
                </div>

                <div class="col-2 align-self-end">
                    <button id="buscar-participante" class="btn btn-laranja mr-2">Buscar</button>
                    <a
                        href="<?php echo esc_url( admin_url( 'edit.php?page=historico-participantes' ) ); ?>"
                        class="btn btn-outline-secondary"
                        >
                        Limpar
                    </a>
                </div>
            </div>
        </form>
        <?php

        //Exibe o erro de validação
        $this->show_validation_error( $campo_selecionado );
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
                $('#input-celular').mask('(00) 00000-0000'); // Máscara para o telefone

                $('input[name="campo"]').on('change', function () {

                    const campoSelecionado = $(this).val();

                    $('.campo-busca').each(function () {

                        const bloco = $(this);
                        const campo = bloco.data('campo');
                        const input = bloco.find('input');

                        if (campo === campoSelecionado) {
                            bloco.removeClass('d-none');
                            input.prop('disabled', false);

                        } else {
                            bloco.addClass('d-none');
                            input.prop('disabled', true);
                        }
                    });
                });
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

        if ( isset( $_GET['action'] ) && $_GET['action'] === 'busca-historico' ) {

            $campo = $_GET['campo'] ?? 'cpf';

            if ( empty( $_GET['valor'] ) ) {
                $this->add_validation_error( $campo, 'Informe o CPF, e-mail ou telefone celular do participante para realizar a busca.' );
                return;
            }

            $valor = sanitize_text_field(  $_GET['valor'] );

            if ( $campo === 'cpf' &&  strlen( preg_replace( '/\D/', '', $_GET['valor'] ) ) !== 11 ) {
                $this->add_validation_error( 'cpf', 'CPF inválido. O CPF deve ter 11 dígitos.' );
                return;
            }

            if ( $campo === 'celular' &&  strlen( preg_replace( '/\D/', '', $_GET['valor'] ) ) !== 11 ) {
                $this->add_validation_error( 'celular', 'Celular inválido. O número de celular deve ter 11 dígitos.' );
                return;
            }

            $dados = $this->get_dados_participante( $campo, $valor );

            if ( !$dados ) {
                $this->show_alert( 'Aviso', 'Nenhum participante foi localizado com os dados informados.', 'info' );
                return;
            }

            $this->dados_participante = $dados;
            $this->eventos = $this->get_eventos_participante( $campo, $valor );
        }
    }

    private function get_dados_participante( string $campo, string $valor )
    {
        global $wpdb;

        $campos_validos = ['cpf', 'email', 'celular'];

        if ( !in_array( $campo, $campos_validos, true ) ) {
            return null;
        }

        switch ( $campo ) {

            case 'cpf':
                $where = 'cpf = %s';
                $valor = preg_replace( '/\D/', '', $valor );
                $prepare = [$valor];
                break;

            case 'email':
                $where = '(email_institucional = %s OR email_secundario = %s)';
                $valor = sanitize_email( $valor );
                $prepare = [$valor, $valor];
                break;

            case 'celular':
                $valor = preg_replace ('/\D/', '', $valor );
                $where = "REGEXP_REPLACE(celular, '[^0-9]', '') = %s";
                $prepare = [$valor];
                break;
        }

        $sql_sorteio = "
            SELECT
                user_id,
                cpf,
                nome_completo,
                email_institucional,
                email_secundario,
                celular,
                telefone_comercial,
                dre,
                cargo_principal,
                unidade_setor,
                data_inscricao
            FROM {$wpdb->prefix}inscricoes
            WHERE {$where}
            ORDER BY data_inscricao DESC
            LIMIT 1
        ";

        $sql_cortesia = "
            SELECT
                user_id,
                cpf,
                nome_completo,
                email_institucional,
                email_secundario,
                celular,
                telefone_comercial,
                dre,
                cargo_principal,
                unidade_setor,
                data_inscricao
            FROM {$wpdb->prefix}cortesias_inscricoes
            WHERE {$where}
            ORDER BY data_inscricao DESC
            LIMIT 1
        ";

        $sorteio = $wpdb->get_row( $wpdb->prepare( $sql_sorteio, ...$prepare ) );
        $cortesia = $wpdb->get_row( $wpdb->prepare( $sql_cortesia, ...$prepare ) );

        if ( $sorteio && $cortesia ) {

            return strtotime( $sorteio->data_inscricao ) > strtotime( $cortesia->data_inscricao )
                ? $sorteio
                : $cortesia;
        }

        return $sorteio ?: $cortesia;
    }

    public function get_eventos_participante( string $campo, string $valor ) {
        global $wpdb;

        $campos_validos = ['cpf', 'email', 'celular'];

        if ( !in_array( $campo, $campos_validos, true ) ) {
            return [];
        }

        switch ( $campo ) {

            case 'cpf':
                $where = 'cpf = %s';
                $valor = preg_replace( '/\D/', '', $valor );
                $prepare = [$valor];
                break;

            case 'email':
                $where = '(email_institucional = %s OR email_secundario = %s)';
                $valor = sanitize_email( $valor );
                $prepare = [$valor, $valor];
                break;

            case 'celular':
                $valor = preg_replace ('/\D/', '', $valor );
                $where = "REGEXP_REPLACE(celular, '[^0-9]', '') = %s";
                $prepare = [$valor];
                break;
        }

        $tabela_destinatarios = $wpdb->prefix . 'historico_envios_destinatarios';

        $sql = "
            SELECT 
                e.*,

                CASE 
                    WHEN h.inscricao_id IS NULL THEN 0
                    ELSE 1
                END AS tem_historico

            FROM (

                SELECT 
                    i.id,
                    i.post_id,
                    i.sorteado,
                    i.confirmou_presenca,
                    i.prazo_confirmacao,
                    i.enviou_email_instrucoes,
                    i.compareceu,
                    i.tipo_contato,
                    i.data_inscricao,
                    p.post_title AS nome_evento,
                    'sorteio' AS tipo

                FROM {$wpdb->prefix}inscricoes i

                INNER JOIN {$wpdb->posts} p
                    ON p.ID = i.post_id

                WHERE {$where}


                UNION ALL


                SELECT 
                    i.id,
                    i.post_id,
                    NULL AS sorteado,
                    i.confirmou_presenca,
                    i.prazo_confirmacao,
                    i.enviou_email_instrucoes,
                    i.compareceu,
                    i.tipo_contato,
                    i.data_inscricao,
                    p.post_title AS nome_evento,
                    'cortesia' AS tipo

                FROM {$wpdb->prefix}cortesias_inscricoes i

                INNER JOIN {$wpdb->posts} p
                    ON p.ID = i.post_id

                WHERE {$where}

            ) e

            LEFT JOIN (
                SELECT DISTINCT inscricao_id
                FROM {$tabela_destinatarios}
            ) h

            ON h.inscricao_id = e.id

            ORDER BY e.data_inscricao DESC
        ";

        $params = array_merge($prepare, $prepare);

        $query = $wpdb->prepare($sql, ...$params);

        return $wpdb->get_results($query);
    }

    public function get_eventos_participante_com_filtros(string $cpf, array $filtros = []) {

        global $wpdb;

        $tabela_destinatarios = $wpdb->prefix . 'historico_envios_destinatarios';

        $where = [];
        $params = [$cpf, $cpf];

        /*
        | Filtro: Buscar evento
        */

        if ( !empty( $filtros['evento'] ) ) {

            $where[] = 'e.nome_evento LIKE %s';
            $params[] = '%' . $wpdb->esc_like( $filtros['evento'] ) . '%';
        }

        /*
        | Filtro: Modalidade
        | sorteio
        | cortesia
        */

        if ( !empty( $filtros['modalidade'] ) ) {

            $where[] = 'e.tipo = %s';
            $params[] = sanitize_text_field( $filtros['modalidade'] );
        }

        /*
        | Filtro: Minha participação
        | confirmada
        | prazo_expirado
        | cancelou
        | bloqueado_falta
        */

        if ( !empty( $filtros['participacao'] ) ) {

            $where[] = 'e.status_participacao = %s';
            $params[] = sanitize_text_field( $filtros['participacao'] );
        }

        /*
        | Filtro: Ações pendentes
        | cancelar_inscricao
        | confirmar_presença
        */

        if ( !empty( $filtros['acoes'] ) && $filtros['acoes'] === 'confirmar_presenca' ) {

            $where[] = 'e.acao_pendente = %s';
            $params[] = sanitize_text_field( $filtros['acoes'] );
        }

        $where_sql = '';

        if ( !empty( $where ) ) {
            $where_sql = 'WHERE ' . implode( ' AND ', $where );
        }

        $sql = "
            SELECT 
                e.*,

                CASE 
                    WHEN h.inscricao_id IS NULL THEN 0
                    ELSE 1
                END AS tem_historico

            FROM (

                SELECT 
                    i.id,
                    i.post_id,
                    i.sorteado,
                    i.confirmou_presenca,
                    i.prazo_confirmacao,
                    i.enviou_email_instrucoes,
                    i.compareceu,
                    i.tipo_contato,
                    i.data_inscricao,
                    p.post_title AS nome_evento,

                    'sorteio' AS tipo,

                    /* Status participação */

                    CASE

                        WHEN i.compareceu = 0
                        THEN 'bloqueado_falta'

                        WHEN i.confirmou_presenca = 1
                        THEN 'confirmada'

                        WHEN i.confirmou_presenca = 2
                        THEN 'cancelou'

                        WHEN i.confirmou_presenca = 0
                            AND i.prazo_confirmacao IS NOT NULL
                            AND i.prazo_confirmacao < NOW()
                        THEN 'prazo_expirado'

                        ELSE NULL

                    END AS status_participacao,
                
                    /* Ação pendente */

                    CASE

                        WHEN i.confirmou_presenca = 0
                            AND i.prazo_confirmacao IS NOT NULL
                            AND i.prazo_confirmacao > NOW()

                        THEN 'confirmar_presenca'

                        ELSE NULL

                    END AS acao_pendente

                FROM {$wpdb->prefix}inscricoes i

                INNER JOIN {$wpdb->posts} p
                    ON p.ID = i.post_id

                WHERE i.cpf = %s


                UNION ALL


                SELECT 
                    i.id,
                    i.post_id,

                    NULL AS sorteado,

                    i.confirmou_presenca,
                    i.prazo_confirmacao,
                    i.enviou_email_instrucoes,
                    i.compareceu,
                    i.tipo_contato,
                    i.data_inscricao,

                    p.post_title AS nome_evento,

                    'cortesia' AS tipo,

                    /* Status participação */

                    CASE

                        WHEN i.compareceu = 0
                        THEN 'bloqueado_falta'

                        WHEN i.confirmou_presenca = 1
                        THEN 'confirmada'

                        WHEN i.confirmou_presenca = 2
                        THEN 'cancelou'

                        WHEN i.confirmou_presenca = 0
                            AND i.prazo_confirmacao IS NOT NULL
                            AND i.prazo_confirmacao < NOW()
                        THEN 'prazo_expirado'

                        ELSE NULL

                    END AS status_participacao,

                    /* Ação pendente */

                    CASE

                        WHEN i.confirmou_presenca = 0
                            AND i.prazo_confirmacao IS NOT NULL
                            AND i.prazo_confirmacao > NOW()

                        THEN 'confirmar_presenca'

                        ELSE NULL

                    END AS acao_pendente

                FROM {$wpdb->prefix}cortesias_inscricoes i

                INNER JOIN {$wpdb->posts} p
                    ON p.ID = i.post_id

                WHERE i.cpf = %s

            ) e

            LEFT JOIN (
                SELECT DISTINCT inscricao_id
                FROM {$tabela_destinatarios}
            ) h

            ON h.inscricao_id = e.id

            {$where_sql}

            ORDER BY e.data_inscricao DESC
        ";

        $query = $wpdb->prepare( $sql, ...$params );
        $inscricoes = $wpdb->get_results( $query );

        if ( !empty( $filtros['local'] ) ) {

            $local = intval( $filtros['local'] );

            $inscricoes = array_filter( $inscricoes, function( $inscricao ) use ( $local ) {

                $tags = get_the_terms(
                    $inscricao->post_id,
                    'post_tag'
                );

                if ( empty( $tags ) || is_wp_error( $tags ) ) {
                    return false;
                }

                $tag = reset( $tags );

                return intval( $tag->term_id ) === $local;

            });
        }

        if ( !empty( $filtros['acoes'] ) && $filtros['acoes'] === 'cancelar_inscricao' ) {

            $hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );
            $inscricoes = array_filter( $inscricoes, function( $inscricao ) use ( $hoje ) {

                $encerramento_inscricoes = get_field( 'enc_inscri', $inscricao->post_id );

                return $encerramento_inscricoes >= $hoje;

            });

        }

        // Adiciona o status da inscrição (Resultado) no resultado do filtro
        foreach ( $inscricoes as $inscricao ) {
            $inscricao->resultado_inscricao = get_status_resultado_inscricao( $inscricao );
        }

        if ( !empty( $filtros['resultado'] ) ) {

            $inscricoes = array_filter( $inscricoes, function( $inscricao ) use ( $filtros ) {

                return $inscricao->resultado_inscricao === $filtros['resultado'];

            });
        }
        
        return $inscricoes;
    }

    public function check_sancao_ativa_participante( string $cpf ) {
        global $wpdb;

        $hoje = obter_data_com_timezone( 'Y-m-d', 'America/Sao_Paulo' );
        $tabela_sancoes = $wpdb->prefix . 'inscricao_sancoes';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, id_inscricao, data_validade FROM $tabela_sancoes WHERE cpf = %s AND data_validade > %s",
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