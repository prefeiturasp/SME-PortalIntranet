<?php
class Historico_Participacoes {

    private $alert = null;
    private $dados_participante = null;
    private $eventos = null;
    private $validation_errors = [];

    function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action(
            'wp_ajax_exportar_historico_participacoes',
            [$this, 'exportar_historico_participacoes']
        );
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
            function() {
                    $this->box_eventos( $this->dados_participante );
                },   // Callback
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

                <div class="col-2 align-self-end d-flex">
                    <a
                        href="<?php echo esc_url( admin_url( 'edit.php?page=historico-participantes' ) ); ?>"
                        class="btn btn-outline-secondary flex-fill mr-2"
                        >
                        Limpar Filtros
                    </a>
                    <button id="buscar-participante" class="btn flex-fill btn-laranja">Buscar</button>
                    
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

    function box_eventos( $dados_participante ) {

        $sancao_ativa = [];  
    
        if($dados_participante->cpf != ''){
            $sancao_ativa = $this->check_sancao_ativa_participante( $dados_participante->cpf );
        }

        get_template_part( 'classes/HistoricoParticipacoes/template-parts/lista-eventos', null, [ 
            'eventos' => $this->eventos,
            'dados' => $sancao_ativa
        ] );
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

    public function get_eventos_por_inscricoes( array $inscricoes ) {
        global $wpdb;

        if ( empty( $inscricoes ) ) {
            return [];
        }

        $ids_sorteio = [];
        $ids_cortesia = [];

        foreach ( $inscricoes as $inscricao ) {

            if ( empty( $inscricao['id'] ) || empty( $inscricao['tipo'] ) ) {
                continue;
            }

            $id = absint( $inscricao['id'] );
            $tipo = sanitize_key( $inscricao['tipo'] );

            if ( ! $id ) {
                continue;
            }

            if ( $tipo === 'sorteio' ) {
                $ids_sorteio[] = $id;
            }

            if ( $tipo === 'cortesia' ) {
                $ids_cortesia[] = $id;
            }
        }

        if ( empty( $ids_sorteio ) && empty( $ids_cortesia ) ) {
            return [];
        }

        $tabela_destinatarios = $wpdb->prefix . 'historico_envios_destinatarios';

        $consultas = [];
        $params = [];

        /*
        * Inscrições de sorteio
        */
        if ( ! empty( $ids_sorteio ) ) {

            $placeholders = implode(
                ', ',
                array_fill( 0, count( $ids_sorteio ), '%d' )
            );

            $consultas[] = "
                SELECT 
                    i.id,
                    i.cpf,
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

                WHERE i.id IN ($placeholders)
            ";

            $params = array_merge( $params, $ids_sorteio );
        }

        /*
        * Inscrições de cortesia
        */
        if ( ! empty( $ids_cortesia ) ) {

            $placeholders = implode(
                ', ',
                array_fill( 0, count( $ids_cortesia ), '%d' )
            );

            $consultas[] = "
                SELECT 
                    i.id,
                    i.cpf,
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

                WHERE i.id IN ($placeholders)
            ";

            $params = array_merge( $params, $ids_cortesia );
        }

        $union = implode( "\n UNION ALL \n", $consultas );

        $sql = "
            SELECT 
                e.*,

                CASE 
                    WHEN h.inscricao_id IS NULL THEN 0
                    ELSE 1
                END AS tem_historico

            FROM (
                {$union}
            ) e

            LEFT JOIN (
                SELECT DISTINCT inscricao_id
                FROM {$tabela_destinatarios}
            ) h

            ON h.inscricao_id = e.id

            ORDER BY e.data_inscricao DESC
        ";

        $query = $wpdb->prepare( $sql, ...$params );

        return $wpdb->get_results( $query );
    }

    public function exportar_historico_participacoes() {

        if ( empty( $_POST['inscricoes'] ) ) {
            wp_send_json_error([
                'message' => 'Nenhuma inscrição foi informada.'
            ]);
        }

        $inscricoes = json_decode(
            wp_unslash( $_POST['inscricoes'] ),
            true
        );

        if ( ! is_array( $inscricoes ) ) {
            wp_send_json_error([
                'message' => 'Dados de inscrições inválidos.'
            ]);
        }

        $inscricoes_validas = [];

        foreach ( $inscricoes as $inscricao ) {

            if ( ! is_array( $inscricao ) ) {
                continue;
            }

            $id = isset( $inscricao['id'] )
                ? absint( $inscricao['id'] )
                : 0;

            $tipo = isset( $inscricao['tipo'] )
                ? sanitize_key( $inscricao['tipo'] )
                : '';

            if ( ! $id || ! in_array( $tipo, ['sorteio', 'cortesia'], true ) ) {
                continue;
            }

            $inscricoes_validas[] = [
                'id' => $id,
                'tipo' => $tipo,
            ];
        }

        if ( empty( $inscricoes_validas ) ) {
            wp_send_json_error([
                'message' => 'Nenhuma inscrição válida foi informada.'
            ]);
        }

        /*
        * Recupera os eventos das inscrições selecionadas.
        */
        $eventos = $this->get_eventos_por_inscricoes(
            $inscricoes_validas
        );

        if ( empty( $eventos ) ) {
            wp_send_json_error([
                'message' => 'Nenhuma inscrição foi encontrada.'
            ]);
        }

        /*
        * Recupera os dados do participante.
        *
        * O CPF vem dos eventos retornados pelo banco.
        */
        $dados_participante = $this->get_dados_participante(
            'cpf',
            $eventos[0]->cpf
        );

        if ( ! $dados_participante ) {
            wp_send_json_error([
                'message' => 'Não foi possível localizar os dados do participante.'
            ]);
        }

        /*
        * Recupera a sanção ativa do participante.
        */
        $sancao_ativa = $this->check_sancao_ativa_participante(
            $dados_participante->cpf
        );

        /*
        * Gera o arquivo Excel.
        */
        $this->gerar_excel_historico(
            $eventos,
            $dados_participante,
            $sancao_ativa
        );
    }

    private function gerar_excel_historico(
        array $eventos,
        object $dados_participante,
        $sancao_ativa
    ): void {

        
        $agora = new \DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $dados = [];

        /*
        * Define o perfil do participante.
        */
        $perfil = 'ESTAGIÁRIO';

        if ( $dados_participante->user_id && $dados_participante->user_id > 0 ) {

            $tipo = get_user_meta(
                $dados_participante->user_id,
                'parceira',
                true
            );

            $perfil = $tipo == 1 ? 'PARCEIRO' : 'SERVIDOR';
        }

        /*
        * Monta as informações do participante.
        *
        * Todas as informações ficarão na mesma célula,
        * utilizando quebra de linha.
        */
        $informacoes_participante = [
            '<b>Nome Completo:</b> ' . ( $dados_participante->nome_completo ?: '-' ),
            '<b>CPF:</b> ' . ( $dados_participante->cpf ?: '-' ),
            '<b>E-mail principal:</b> ' . ( $dados_participante->email_institucional ?: '-' ),
            '<b>E-mail secundário:</b> ' . ( $dados_participante->email_secundario ?: '-' ),
            '<b>Telefone Celular:</b> ' . ( $dados_participante->celular ?: '-' ),
            '<b>Telefone Comercial:</b> ' . ( $dados_participante->telefone_comercial ?: '-' ),
            '<b>Perfil:</b> ' . $perfil,
            '<b>DRE/SME:</b> ' . ( $dados_participante->dre ?: '-' ),
            '<b>Cargo atual:</b> ' . ( $dados_participante->cargo_principal ?: '-' ),
            '<b>Escola/Setor:</b> ' . ( $dados_participante->unidade_setor ?: '-' ),
        ];        

        /*
        * Transforma as informações em uma única string,
        * utilizando quebra de linha dentro da célula.
        */
       $dados_participante_excel =
        "\n" .
        implode(
            "\n",
            $informacoes_participante
        ) .
        "\n";

        $mensagem_sancao = '';

        if ( isset( $sancao_ativa ) && ! empty( $sancao_ativa ) ) {

            $data_formatada = date(
                'd/m/Y',
                strtotime( $sancao_ativa['data_validade'] )
            );

            $mensagem_sancao =
                'Atenção! Você está temporariamente impedido de se inscrever em novas oportunidades, devido à ausência em uma participação anterior. ' . "\n" . 'Você poderá realizar novas inscrições a partir de ' .
                $data_formatada .
                '.';
        }

        /*
        * Cabeçalho do relatório
        */
        $dados[] = [
            sprintf(
                '<style font-size="22" bgcolor="#d9ead3" height="40" border="medium" color="#000000" bordercolor="#000000" valign="center"><middle><b>Histórico do Participante | Extraído em %s</b></middle></style>',
                current_time( 'd/m/Y - H:i' )
            ),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        /*
        * Informações do participante
        */
        $dados[] = [
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" border="medium" bordercolor="#000000" height="180" valign="center"><middle>' . $dados_participante_excel . '</middle></style>',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        if ( ! empty( $mensagem_sancao ) ) {

            $dados[] = [
                '<style font-size="12" bgcolor="#d9ead3" height="35" color="#000000" border="medium" bordercolor="#000000" valign="center"><middle>' . $mensagem_sancao . '</middle></style>',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ];
        }

        /*
        * Cabeçalho das colunas
        */
        $dados[] = [
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="medium" bordercolor="#000000"><center><wraptext><middle><b>ID do Evento</b></middle></wraptext></center></style>',
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="medium" bordercolor="#000000"><center><wraptext><middle><b>Nome Evento</b></middle></wraptext></center></style>',
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="medium" bordercolor="#000000"><center><wraptext><middle><b>Modalidade</b></middle></wraptext></center></style>',
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="medium" bordercolor="#000000"><center><wraptext><middle><b>Foi Sorteado?</b></middle></wraptext></center></style>',
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="medium" bordercolor="#000000"><center><wraptext><middle><b>Confirmou Presença?</b></middle></wraptext></center></style>',
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="medium" bordercolor="#000000"><center><wraptext><middle><b>Instruções Enviadas?</b></middle></wraptext></center></style>',
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="medium" bordercolor="#000000"><center><wraptext><middle><b>Contato Extra</b></middle></wraptext></center></style>',
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="medium" bordercolor="#000000"><center><wraptext><middle><b>Compareceu ou Resgatou?</b></middle></wraptext></center></style>',
        ];

        /*
        * Dados
        */
        foreach ( $eventos as $evento ) {
        
            $tipo = '';
            $sorteado = '';

            $requerConfirmacao = get_post_meta($evento->post_id, 'confirm_presen', true);            
            if( $evento->prazo_confirmacao && ($evento->sorteado || $evento->tipo == 'cortesia') ) {
                $confirmacaoPresenca = $evento->confirmou_presenca;
                $data_validar = new DateTime($evento->prazo_confirmacao, new DateTimeZone('America/Sao_Paulo'));                                
                
                if($confirmacaoPresenca == '1'){
                    $confirmacao = 'Confirmada';
                } elseif($confirmacaoPresenca == '2'){
                    $confirmacao = 'Cancelou participação';
                } else {			
                    if($agora > $data_validar){
                        $confirmacao = 'Prazo expirado';
                    } else {
                        $confirmacao = 'Aguardando';
                    }
                }
            } elseif (!$requerConfirmacao) {
                $confirmacao = 'Não requer';
            } else {
                $confirmacao = 'Aguardando';
            }

            if($evento->sorteado || $evento->tipo == 'cortesia') {
                if($evento->enviou_email_instrucoes) {                    
                    $instrucoes = 'Sim';                     
                } else {
                    $instrucoes = 'Não';
                }
            } else {
                $instrucoes = '-';
            }

            if($evento->sorteado || $evento->tipo == 'cortesia') {
                switch ($evento->tipo_contato) {
                    case '1':                        
                        $contato = 'Contato por telefone';
                        break;
                    case '2':                        
                        $contato = 'Contato por e-mail';
                        break;
                    case '3':                        
                        $contato = 'Contato por WhatsApp';
                        break;
                    default:
                        $contato = '-';
                }
            } else {
                $contato = '-';
            }

            $sancao_evento = $this->check_sancao_ativa_participante(
                $evento->cpf
            );

            if ( $evento->sorteado || $evento->tipo == 'cortesia' ) {

                if ( $evento->compareceu ) {
                    $compareceu = 'Sim';

                } elseif (
                    is_array( $sancao_evento )
                    && $sancao_evento['id_inscricao'] == $evento->id
                ) {
                    $compareceu = 'Bloqueado por Falta';

                } else {
                    $compareceu = 'Não';
                }

            } else {
                $compareceu = '-';
            }

            if ( $evento->tipo === 'sorteio' ) {
                $tipo = 'Sorteio';
                $sorteado = $evento->sorteado ? 'Sim' : 'Não';
            } else {
                $tipo = 'Ordem de Inscrição';
                $sorteado = 'N/A';
            }

            $dados[] = [
                '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="thin" bordercolor="#000000"><center><wraptext><middle><b>' . $evento->post_id . '</b></middle></wraptext></center></style>',
                '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="thin" bordercolor="#000000"><center><wraptext><middle>' . $evento->nome_evento . '</middle></wraptext></center></style>',
                '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="thin" bordercolor="#000000"><center><wraptext><middle>' . $tipo . '</middle></wraptext></center></style>',
                '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="thin" bordercolor="#000000"><center><wraptext><middle>' . $sorteado . '</middle></wraptext></center></style>',
                '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="thin" bordercolor="#000000"><center><wraptext><middle>' . $confirmacao . '</middle></wraptext></center></style>',
                '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="thin" bordercolor="#000000"><center><wraptext><middle>' . $instrucoes . '</middle></wraptext></center></style>',
                '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="thin" bordercolor="#000000"><center><wraptext><middle>' . $contato . '</middle></wraptext></center></style>',                
                '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="thin" bordercolor="#000000"><center><wraptext><middle>' . $compareceu . '</middle></wraptext></center></style>',
            ];
        }

        /*
        * Legendas
        */

        /*
        * Espaço antes da primeira legenda
        */
        $dados[] = [
            '<style height="8"></style>',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];
        
        $dados[] = [
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="medium" bordercolor="#000000"><middle><b>N/A - coluna “Foi Sorteado?”: Indica eventos da modalidade Ordem de Inscrição, nos quais a participação é definida pela ordem de inscrição dos participantes, não havendo realização de sorteio.</b></middle></style>',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        /*
        * Espaço antes da segunda legenda
        */
        $dados[] = [
            '<style height="8"></style>',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $dados[] = [
            '<style font-size="12" bgcolor="#ebf1de" color="#000000" valign="center" border="medium" bordercolor="#000000"><middle><b>Não Requer - coluna “Confirmou Presença?”:  Indica que o evento não exige confirmação de presença do participante.</b></middle></style>',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $xlsx = Classes\Lib\SimpleXLSXGenExp::fromArray( $dados );
        $xlsx->setDefaultFont('Aptos Narrow');
        $xlsx->setColWidth(1, 15);

        $xlsx->mergeCells( 'A1:H1' ); // mescla cabeçalho
        $xlsx->mergeCells( 'A2:H2' ); // mescla informações do participante

        /*
        * Mescla a mensagem de sanção, caso exista.
        */
        if ( ! empty( $mensagem_sancao ) ) {
            $xlsx->mergeCells( 'A3:H3' );
        }

        /*
        * Ativa o filtro no cabeçalho das inscrições.
        */

        $linha_cabecalho = ! empty( $mensagem_sancao ) ? 4 : 3;
        $ultima_linha = count( $dados ) - 4;

        $xlsx->autoFilter(
            'A' . $linha_cabecalho . ':H' . $ultima_linha,
        );

        /*
        * Descobre as últimas duas linhas,
        * que correspondem às legendas.
        */
        $total_linhas = count( $dados );

        $linha_legenda_2 = $total_linhas;
        $linha_legenda_1 = $total_linhas - 2;

        /*
        * Mescla as duas legendas.
        */
        $xlsx->mergeCells(
            'A' . $linha_legenda_1 . ':H' . $linha_legenda_1
        );

        $xlsx->mergeCells(
            'A' . $linha_legenda_2 . ':H' . $linha_legenda_2
        );

        $nome_participante = remove_accents(
            $dados_participante->nome_completo
        );

        $nome_participante = preg_replace(
            '/[^A-Za-z0-9]+/',
            '_',
            $nome_participante
        );

        $nome_participante = trim(
            $nome_participante,
            '_'
        );

        $data_relatorio = $agora->format( 'd_m_Y' );
        $hora_relatorio = $agora->format( 'H_i' );

        $nome_arquivo = sprintf(
            'Relatorio_Historico_Do_Participante_%s_Extraido_%s_%s.xlsx',
            $nome_participante,
            $data_relatorio,
            $hora_relatorio
        );

        $xlsx->downloadAs( $nome_arquivo );

        exit;
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