<?php

use EnviaEmailOportunidade\classes\Envia_Emails_Oportunidades_SME;

class Inscricao {

    const TABELA_INSCRICOES = 'int_oportunidade_inscricoes';
    const TABELA_CURRICULO = 'int_banco_talentos';
    const STATUS_INSCRITO = 'inscrito';

    public function __construct() {

        add_action( 'wp_ajax_realizar_inscricao', [$this, 'handle_realizar_inscricao'] );
        add_action( 'wp_ajax_confirmar_participacao', [$this, 'handle_confirmar_participacao'] );

        if ( is_admin() ) {
            add_action( 'wp_ajax_atualizar_etapa_candidatos', [$this, 'handle_atualizar_etapa_candidatos'] );
            add_action( 'wp_ajax_enviar_email_comunicado', [$this, 'handle_enviar_email_comunicado'] );
            add_action( 'wp_ajax_comunicar_selecionados', [$this, 'handle_comunicar_selecionados'] );
            add_action( 'wp_ajax_desfazer_etapa', [$this, 'handle_desfazer_etapa'] );
        }

        // Ações da Cron de atualização automática das etapas
        add_action( 'processar_oportunidades_encerradas', [$this, 'processar_inscricoes_oportunidade_encerrada'] );
        add_action( 'processar_oportunidades_encerradas_backup', [$this, 'processar_inscricoes_oportunidade_encerrada'] );
    }

    /**
     * AJAX - Realizar inscrição
    */
    public function handle_realizar_inscricao() {

        check_ajax_referer( 'realizar_inscricao', 'nonce' );

        $oportunidade_id = absint( $_POST['oportunidade_id'] ?? 0 );
        $resultado = self::realizar_inscricao( get_current_user_id(), $oportunidade_id );

        if ( !$resultado['success'] ) {
            wp_send_json_error( $resultado );
        }

        wp_send_json_success( $resultado );
    }

    /**
     * Realiza inscrição
    */
    public static function realizar_inscricao( int $usuario_id, int $oportunidade_id ) {

        global $wpdb;

        if ( !$usuario_id || empty( $usuario_id ) ) {

            return [
                'success' => false,
                'message' => 'Usuário inválido.'
            ];
        }

        if ( !$oportunidade_id || empty( $oportunidade_id ) ) {

            return [
                'success' => false,
                'message' => 'Oportunidade inválida.'
            ];
        }

        $status_oportunidade = Oportunidade::get_status( $oportunidade_id );

        if ( empty( $status_oportunidade ) || $status_oportunidade['value'] !== 'aberta' ) {

            return [
                'success' => false,
                'message' => 'Esta oportunidade não está com inscrições abertas.'
            ];
        }

        $curriculo = self::obter_curriculo_usuario( $usuario_id );

        if ( empty( $curriculo ) ) {

            return [
                'success' => false,
                'message' => 'Nenhum currículo encontrado.'
            ];
        }

        if ( $curriculo->status_curriculo !== 'finalizado' ) {

            return [
                'success' => false,
                'message' => 'É necessário finalizar o preenchimento do currículo.'
            ];
        }

        if ( self::usuario_ja_inscrito( $usuario_id, $oportunidade_id ) ) {

            return [
                'success' => false,
                'message' => 'Você já está inscrito nesta oportunidade.'
            ];
        }

        $insert = $wpdb->insert(
            self::TABELA_INSCRICOES,
            [
                'user_id'         => $usuario_id,
                'curriculo_id'    => $curriculo->id,
                'oportunidade_id' => $oportunidade_id,
                'rf'              => $curriculo->rf,
                'status'          => self::STATUS_INSCRITO,
                'created_at'      => current_time( 'mysql' ),
            ],
            [
                '%d',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
            ]
        );

        if ( $insert === false ) {

            return [
                'success' => false,
                'message' => 'Não foi possível concluir a inscrição.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Inscrição realizada com sucesso.'
        ];
    }

    /**
     * Retorna currículo do usuário
     */
    public static function obter_curriculo_usuario( int $usuario_id ) {

        global $wpdb;
        $tabela = $wpdb->prefix . 'banco_talentos';

        return $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$tabela}
                WHERE user_id = %d
                LIMIT 1
                ",
                $usuario_id
            )
        );
    }

    /**
     * Verifica se usuário possui inscrição ativa
    */
    public static function usuario_ja_inscrito( int $usuario_id, int $oportunidade_id ) {

        global $wpdb;

        $inscricao = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT id
                FROM " . self::TABELA_INSCRICOES . "
                WHERE user_id = %d
                AND oportunidade_id = %d
                LIMIT 1
                ",
                $usuario_id,
                $oportunidade_id
            )
        );

        return !empty( $inscricao );
    }

    /**
     * AJAX - Atualizar etapa inscritos
    */
    public function handle_atualizar_etapa_candidatos() {

        check_ajax_referer( 'atualizar_etapa_candidatos', 'nonce' );

        $id_inscricoes = $_POST['ids'];
        $etapa = $_POST['etapa_codigo'];
        $post_id = absint( $_POST['post_id'] );

        $resultado = self::atualizar_etapa_candidatos( $id_inscricoes, $etapa, $post_id );

        if ( !$resultado['success'] ) {
            wp_send_json_error( $resultado );
        }

        wp_send_json_success( $resultado );
    }

    /**
     * Atualiza etapa dos candidatos
    */
    public static function atualizar_etapa_candidatos( array $id_inscricoes, string $etapa, string $post_id, bool $auto = false ) {

        global $wpdb;

        $etapas_processo = self::get_etapas_processo();

        if ( empty( $id_inscricoes ) ) {

            return [
                'success' => false,
                'message' => 'É necessário selecionar pelo menos um inscrito.'
            ];

        }

        if ( !isset( $etapas_processo[$etapa] ) || empty( $etapas_processo[$etapa] ) ) {

            return [
                'success' => false,
                'message' => 'Etapa inválida.'
            ];

        }

        $ids = array_map( 'absint', $id_inscricoes );

        $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );


        /**
         * Monta atualização do status_anterior
         * somente quando a nova etapa permite desbloqueio
         */
        $status_anterior_case = '';

        if ( self::permite_desbloqueio( $etapa ) ) {


            $sql_status = $wpdb->prepare(
                "
                SELECT id, status
                FROM " . self::TABELA_INSCRICOES . "
                WHERE id IN ($placeholders)
                ",
                $ids
            );


            $inscricoes_atuais = $wpdb->get_results(
                $sql_status,
                ARRAY_A
            );


            if ( !empty( $inscricoes_atuais ) ) {

                $cases = [];


                foreach ( $inscricoes_atuais as $inscricao ) {

                    $cases[] = $wpdb->prepare(
                        "WHEN %d THEN %s",
                        $inscricao['id'],
                        $inscricao['status']
                    );

                }


                $status_anterior_case = "
                    status_anterior = CASE id
                        " . implode( "\n", $cases ) . "
                    END,
                ";

            }

        }

        $updated_at = current_time( 'mysql' );

        if ( $auto ) {

            $timezone = new DateTimeZone( 'America/Sao_Paulo' );
            $encerramento_inscricoes = get_field( 'ence_inscricoes', $post_id, false );
            $encerramento_inscricoes = new DateTime( $encerramento_inscricoes, $timezone );
            $encerramento_inscricoes->setTime(23, 59, 59);
            
            $updated_at = $encerramento_inscricoes->format( 'Y-m-d H:i:s' );
        }

        /**
         * Atualiza inscrições
         */
        $query = $wpdb->prepare(
            "
            UPDATE " . self::TABELA_INSCRICOES . "

            SET
                {$status_anterior_case}
                status = %s,
                updated_at = %s,
                atualizacao_auto = %d

            WHERE id IN ($placeholders)
            ",
            array_merge( [$etapa, $updated_at, $auto], $ids )
        );


        $resultado = $wpdb->query( $query );

        if ( $resultado === false ) {

            return [
                'success' => false,
                'message' => 'Não foi possível completar ação.'
            ];

        }


        /**
         * Envio de emails conforme etapa
         */
        $envio = new \EnviaEmailOportunidade\classes\Envia_Emails_Oportunidades_SME(
            $id_inscricoes,  // Pode ser um único ID ou array de IDs
            $post_id,        // ID da oportunidade
            $etapa           // Etapa atual
        );


        $envio->enviar_emails_conforme_etapa();

        /**
         * Atualiza tabela
         */
        ob_start();

        $inscricoes = Oportunidade::get_inscricoes( $post_id );

        get_template_part( 'includes/oportunidades/template-parts/linhas-tabela-inscritos', null, [
            'participantes' => $inscricoes
        ]); 

        $html = ob_get_clean();

        return [
            'success' => true,
            'message' => 'Etapa atualizada com sucesso.',
            'html' => $html
        ];

    }

    /**
     * Enviar comunicação de confirmação de interesse para o usuário selecionado
     */

    public function handle_comunicar_selecionados() {

        global $wpdb;

        $id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $prazo = isset($_POST['prazo']) ? intval($_POST['prazo']) : 0;
        $tipo_prazo = isset($_POST['tipo_prazo']) ? sanitize_text_field($_POST['tipo_prazo']) : '';
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $conteudo_email = wp_kses_post($_POST['mensagem'] ?? '');       


        if (!$id || !$prazo || !$tipo_prazo) {

            wp_send_json_error([
                'message' => 'Dados obrigatórios não foram enviados.'
            ]);

        }


        $data_atual = current_time('timestamp');


        if ($tipo_prazo === 'horas') {

            $data_prazo = strtotime("+{$prazo} hours", $data_atual);

        } else {

            $data_prazo = strtotime("+{$prazo} days", $data_atual);

        }


        $prazo_confirmacao = date('Y-m-d H:i:s', $data_prazo);


        $resultado = $wpdb->update(
            self::TABELA_INSCRICOES,
            [
                'prazo_confirmacao' => $prazo_confirmacao,
                'status_confirm' => $status,
                'confirmou_presenca' => 0
            ],
            [
                'id' => $id
            ],
            [
                '%s',
                '%s',
                '%d'
            ],
            [
                '%d'
            ]
        );


        if ($resultado !== false) {


            $email_controller = new \EnviaEmailOportunidade\classes\Envia_Emails_Oportunidades_SME(
                $id,
                $post_id,
                $status
            );


            $envio = $email_controller->enviar_email_confirmacao_interesse(
                $conteudo_email,
                $prazo_confirmacao
            );


            if (!$envio) {

                wp_send_json_error([
                    'message' => 'A inscrição foi atualizada, mas ocorreu um erro ao enviar o email.'
                ]);

            }


            ob_start();

            $inscricoes = Oportunidade::get_inscricoes($post_id);

            get_template_part(
                'includes/oportunidades/template-parts/linhas-tabela-inscritos',
                null,
                [
                    'participantes' => $inscricoes
                ]
            );

            $html = ob_get_clean();

            wp_send_json_success([
                'message' => 'A solicitação de confirmação de interesse foi enviada ao candidato com sucesso.',
                'prazo_confirmacao' => $prazo_confirmacao,
                'html' => $html
            ]);

        } else {

            wp_send_json_error([
                'message' => 'Erro ao atualizar a inscrição.'
            ]);

        }

    }

    public function handle_desfazer_etapa() {

        global $wpdb;

        $id = isset($_POST['id']) ? absint($_POST['id']) : 0;
        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;

        if (!$id) {
            wp_send_json_error([
                'message' => 'Inscrição inválida.'
            ]);
        }


        /**
         * Busca status atual e anterior
         */
        $inscricao = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT 
                    status,
                    status_anterior
                FROM " . self::TABELA_INSCRICOES . "
                WHERE id = %d
                ",
                $id
            ),
            ARRAY_A
        );


        if (empty($inscricao)) {
            wp_send_json_error([
                'message' => 'Inscrição não encontrada.'
            ]);
        }


        /**
         * Verifica se o status atual permite desbloqueio
         */
        if (!self::permite_desbloqueio($inscricao['status'])) {
            wp_send_json_error([
                'message' => 'Essa etapa não permite desbloqueio.'
            ]);
        }


        /**
         * Verifica se existe etapa anterior salva
         */
        if (empty($inscricao['status_anterior'])) {
            wp_send_json_error([
                'message' => 'Não existe uma etapa anterior para restaurar.'
            ]);
        }


        /**
         * Retorna para etapa anterior
         */
        $resultado = $wpdb->update(
            self::TABELA_INSCRICOES,
            [
                'status' => $inscricao['status_anterior'],
                'status_anterior' => $inscricao['status_anterior'],
                'updated_at' => current_time('mysql'),
                'atualizacao_auto' => 0,
            ],
            [
                'id' => $id
            ],
            [
                '%s',
                '%s',
                '%s',
                '%d'
            ],
            [
                '%d'
            ]
        );


        if ($resultado === false) {
            wp_send_json_error([
                'message' => 'Não foi possível desfazer a alteração.'
            ]);
        }

        /**
         * Envio de emails conforme etapa
         */
        $envio = new \EnviaEmailOportunidade\classes\Envia_Emails_Oportunidades_SME(
            $id,  // Pode ser um único ID ou array de IDs
            $post_id,        // ID da oportunidade
            $inscricao['status']           // Etapa atual
        );


        $envio->enviar_emails_conforme_etapa();


        if (!$envio) {

            wp_send_json_error([
                'message' => 'A inscrição foi atualizada, mas ocorreu um erro ao enviar o email.'
            ]);

        }

        ob_start();

        $inscricoes = Oportunidade::get_inscricoes($post_id);

        get_template_part(
            'includes/oportunidades/template-parts/linhas-tabela-inscritos',
            null,
            [
                'participantes' => $inscricoes
            ]
        );

        $html = ob_get_clean();


        wp_send_json_success([
            'message' => 'A etapa do candidato foi restaurada com sucesso.',
            'html' => $html
        ]);        

    }

    public static function get_etapas_processo() {
        
        return array(
            'analise_curricular' => array(
                'descricao' => 'Em Análise Curricular',
                'classe' => 'analise-curricular'
            ),
            'nao_avancou_triagem' => array(
                'descricao' => 'Candidatura não avançou na Triagem',
                'classe' => 'triagem-curricular'
            ),
            'convocado_teste' => array(
                'descricao' => 'Convocado para Teste/Avaliação',
                'classe' => 'convocado-teste'
            ),
            'entrevista_agendada' => array(
                'descricao' => 'Entrevista Agendada',
                'classe' => 'entrevista-agendada'
            ),
            'nao_avancou_pos_entrevista' => array(
                'descricao' => 'Candidatura não avançou pós-entrevista',
                'classe' => 'candidatura-nao-avancou'
            ),
            'fase_anuencia' => array(
                'descricao' => 'Em Fase de Anuência',
                'classe' => 'fase-anuencia'
            ),
            'entrega_documentos' => array(
                'descricao' => 'Em Fase de Entrega de Documentos',
                'classe' => 'entrega-documentos'
            ),
            'analise_documental' => array(
                'descricao' => 'Análise Documental + Publicação DOC',
                'classe' => 'analise-documental'
            ),
            'aprovado' => array(
                'descricao' => 'Processo Finalizado - Candidato Aprovado',
                'classe' => 'candidatura-aprovada'
            ),
            'nao_selecionado' => array(
                'descricao' => 'Processo Finalizado - Candidato não selecionado',
                'classe' => 'nao-selecionado'
            ),
            'inscrito' => array(
                'descricao' => '—',
                'classe' => 'inscrito'
            )
        );
    }

    public static function permite_desbloqueio($status) {
        $etapas = [
            'nao_avancou_triagem',
            'nao_avancou_pos_entrevista',
            'nao_selecionado'
        ];

        return in_array($status, $etapas, true);
    }

    public static function permite_comunicacao($status) {
        $etapas_comunicacao = array(
            'convocado_teste',
            'entrevista_agendada',
            'fase_anuencia',
            'entrega_documentos'
        );

        return in_array($status, $etapas_comunicacao, true);
    }

    public static function sem_comunicacao($status) {
        $etapas_desabilitado = array(
            'analise_curricular',
            'analise_documental',
            'aprovado'
        );

        return in_array($status, $etapas_desabilitado, true);
    }


    /**
     * AJAX - Enviar comunicado para os candidatos selecionados
    */
    public function handle_enviar_email_comunicado() {

        check_ajax_referer( 'enviar_email_comunicado', 'nonce' );

        $conteudo_email = wp_kses_post( $_POST['conteudo_email'] ?? '' );
        $post_id = absint( $_POST['post_id'] ?? 0 );
        $id_inscricoes = array_filter(
            array_map(
                'absint',
                explode(',', $_POST['ids'] ?? '')
            )
        );

        if ( empty( $id_inscricoes ) ) {
            wp_send_json_error([
                'message' => 'Nenhum candidato selecionado.'
            ]);
        }

        if ( empty( wp_strip_all_tags( $conteudo_email ) ) ) {
            wp_send_json_error([
                'message' => 'O conteúdo do comunicado é obrigatório.'
            ]);
        }

        if ( !$post_id ) {
            wp_send_json_error([
                'message' => 'Oportunidade inválida.'
            ]);
        }

        $email_controller = new \EnviaEmailOportunidade\classes\Envia_Emails_Oportunidades_SME(
            $id_inscricoes,
            $post_id
        );

        $anexos = $email_controller->processar_anexos_email();

        if ( is_wp_error( $anexos ) ) {
            wp_send_json_error([
                'message' => $anexos->get_error_message()
            ]);
        }

        $resultado = $email_controller->enviar_comunicado_inscritos( $conteudo_email, $anexos );

        if ( is_wp_error($resultado) ) {
            wp_send_json_error([
                'message' => $resultado->get_error_message()
            ]);
        }

        foreach ( $anexos as $anexo ) {
            @unlink( $anexo );
        }

        wp_send_json_success([
            'message' => 'Comunicado enviado com sucesso.',
            'enviados' => $resultado['enviados'],
            'falhas' => $resultado['falhas']
        ]);
    }

    public static function processar_inscricoes_oportunidade_encerrada() {

        try {

            $oportunidades_encerradas = Oportunidade::get_oportunidades_encerradas( true, true );
            $etapa_selecionada = get_field( 'etapa_cron', 'options' ) ?: 'analise_curricular';

            if ( !$oportunidades_encerradas ) {
                return;
            }

            foreach ( $oportunidades_encerradas as $oportunidade_id ) {

                try {

                    $oportunidade_inscricoes = Oportunidade::get_inscritos_by_etapa( $oportunidade_id, 'inscrito' );

                    if ( !$oportunidade_inscricoes ) {
                        continue;
                    }

                    $inscricoes_id = wp_list_pluck( $oportunidade_inscricoes, 'id' );

                    $resultado = self::atualizar_etapa_candidatos( $inscricoes_id, $etapa_selecionada, $oportunidade_id, true );

                    if ( !$resultado['success'] ) {

                        error_log(
                            sprintf(
                                '[CRON OPORTUNIDADE] Falha oportunidade %d: %s',
                                $oportunidade_id,
                                $resultado['message']
                            )
                        );

                    }

                    usleep( 500000 );

                } catch ( \Throwable $e ) {

                    error_log(
                        sprintf(
                            '[CRON OPORTUNIDADE] Erro ao processar oportunidade %d: %s',
                            $oportunidade_id,
                            $e->getMessage()
                        )
                    );

                    continue;
                }
            }

        } catch ( \Throwable $e ) {

            error_log( '[CRON OPORTUNIDADES] Erro: ' . $e->getMessage() );

        }
    }

    public static function get_inscricoes_by_user_id( ?int $oportunidade_destaque = null, ?int $user_id = null ) {
        global $wpdb;
        
        $user_id = $user_id ?: get_current_user_id();

        $params = [
            Envia_Emails_Oportunidades_SME::TIPO_CONFIRMACAO,
            Envia_Emails_Oportunidades_SME::TIPO_CONFIRMACAO,
            $user_id
        ];

        $order_by = "i.created_at DESC";

        if ( $oportunidade_destaque ) {
            $order_by = "
                CASE
                    WHEN i.oportunidade_id = %d THEN 0
                    ELSE 1
                END,
                i.created_at DESC
            ";

            $params[] = $oportunidade_destaque;
        }

        $inscricoes = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT
                    i.*,

                    ec.public_id AS comunicado_public_id,
                    cf.public_id AS confirmacao_public_id

                FROM " . self::TABELA_INSCRICOES . " i

                -- Último comunicado
                LEFT JOIN (
                    SELECT
                        d.inscricao_id,
                        MAX(d.envio_id) AS comunicado_id
                    FROM " . Envia_Emails_Oportunidades_SME::TABELA_DESTINATARIOS . " d
                    INNER JOIN " . Envia_Emails_Oportunidades_SME::TABELA_ENVIOS . " e
                        ON e.id = d.envio_id
                    WHERE e.tipo_envio <> %s
                    GROUP BY d.inscricao_id
                ) ultimo_comunicado
                    ON ultimo_comunicado.inscricao_id = i.id

                LEFT JOIN " . Envia_Emails_Oportunidades_SME::TABELA_ENVIOS . " ec
                    ON ec.id = ultimo_comunicado.comunicado_id

                -- Última confirmação
                LEFT JOIN (
                    SELECT
                        d.inscricao_id,
                        MAX(d.envio_id) AS confirmacao_id
                    FROM " . Envia_Emails_Oportunidades_SME::TABELA_DESTINATARIOS . " d
                    INNER JOIN " . Envia_Emails_Oportunidades_SME::TABELA_ENVIOS . " e
                        ON e.id = d.envio_id
                    WHERE e.tipo_envio = %s
                    GROUP BY d.inscricao_id
                ) ultima_confirmacao
                    ON ultima_confirmacao.inscricao_id = i.id

                LEFT JOIN " . Envia_Emails_Oportunidades_SME::TABELA_ENVIOS . " cf
                    ON cf.id = ultima_confirmacao.confirmacao_id

                WHERE i.user_id = %d

                ORDER BY {$order_by}
                ",
                $params
            )
        );

        return $inscricoes;
    }

    public static function get_inscricao_by_id( int $inscricao_id ) {

        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT
                    i.*,

                    ec.public_id AS comunicado_public_id,
                    cf.public_id AS confirmacao_public_id

                FROM " . self::TABELA_INSCRICOES . " i

                -- Último comunicado
                LEFT JOIN (
                    SELECT
                        d.inscricao_id,
                        MAX(d.envio_id) AS comunicado_id
                    FROM " . Envia_Emails_Oportunidades_SME::TABELA_DESTINATARIOS . " d
                    INNER JOIN " . Envia_Emails_Oportunidades_SME::TABELA_ENVIOS . " e
                        ON e.id = d.envio_id
                    WHERE e.tipo_envio <> %s
                    GROUP BY d.inscricao_id
                ) ultimo_comunicado
                    ON ultimo_comunicado.inscricao_id = i.id

                LEFT JOIN " . Envia_Emails_Oportunidades_SME::TABELA_ENVIOS . " ec
                    ON ec.id = ultimo_comunicado.comunicado_id

                -- Última confirmação
                LEFT JOIN (
                    SELECT
                        d.inscricao_id,
                        MAX(d.envio_id) AS confirmacao_id
                    FROM " . Envia_Emails_Oportunidades_SME::TABELA_DESTINATARIOS . " d
                    INNER JOIN " . Envia_Emails_Oportunidades_SME::TABELA_ENVIOS . " e
                        ON e.id = d.envio_id
                    WHERE e.tipo_envio = %s
                    GROUP BY d.inscricao_id
                ) ultima_confirmacao
                    ON ultima_confirmacao.inscricao_id = i.id

                LEFT JOIN " . Envia_Emails_Oportunidades_SME::TABELA_ENVIOS . " cf
                    ON cf.id = ultima_confirmacao.confirmacao_id

                WHERE
                    i.id = %d

                LIMIT 1
                ",
                Envia_Emails_Oportunidades_SME::TIPO_CONFIRMACAO,
                Envia_Emails_Oportunidades_SME::TIPO_CONFIRMACAO,
                $inscricao_id,
            ),
        );
    }

    public function handle_confirmar_participacao() {

        check_ajax_referer( 'confirmar_participacao', 'nonce' );

        if ( ! is_user_logged_in() ) {

            wp_send_json_error([
                'message' => 'Usuário não autenticado.'
            ], 401);

        }

        $inscricao_id = absint( $_POST['inscricao_id'] ?? 0 );
        $post_id = absint( $_POST['post_id'] ?? 0 );
        $confirmou_presenca = absint( $_POST['confirmou_presenca'] ?? 0 );

        $resultado = self::confirmar_participacao( $inscricao_id, $post_id, $confirmou_presenca );

        if ( is_wp_error( $resultado ) ) {

            wp_send_json_error([
                'message' => $resultado->get_error_message()
            ]);

        }

        $inscricao = self::get_inscricao_by_id( $inscricao_id );
        $etapas_processo = self::get_etapas_processo();

        ob_start();

        get_template_part( 'includes/oportunidades/template-parts/minhas-oportunidades/linha-inscricao', null, [
            'inscricao' => $inscricao,
            'etapas_processo' => $etapas_processo
        ]);

        $html = ob_get_clean();

        wp_send_json_success([
            'message' => $resultado['message'],
            'html' => $html
        ]);

    }

    public static function confirmar_participacao( int $inscricao_id, int $post_id, int $confirmou_presenca ) {

        global $wpdb;

        if ( !$inscricao_id || !$post_id || !in_array( $confirmou_presenca, [1,2], true ) ) {

            return new WP_Error(
                'parametros_invalidos',
                'Os parametros são inválidos. Verifique as informações enviadas.'
            );

        }

        $inscricao = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT
                    id,
                    oportunidade_id,
                    confirmou_presenca,
                    prazo_confirmacao
                FROM " . self::TABELA_INSCRICOES . "
                WHERE
                    id = %d
                    AND user_id = %d
                LIMIT 1
                ",
                $inscricao_id,
                get_current_user_id()
            ),
            ARRAY_A
        );

        if ( empty( $inscricao ) ) {

            return new WP_Error(
                'inscricao_nao_encontrada',
                'Não foi possível encontrar a inscrição solicitada.',
            );

        }

        if ( (int) $inscricao['oportunidade_id'] !== $post_id ) {

            return new WP_Error(
                'oportunidade_invalida',
                'A oportunidade informada é inválida.',
            );

        }

        if ( $inscricao['confirmou_presenca'] > 0 ) {

            return new WP_Error(
                'acao_realizada',
                'Você já realizou esta ação.'
            );

        }

        if ( !empty( $inscricao['prazo_confirmacao'] ) ) {

            $agora = obter_data_com_timezone( 'Y-m-d H:i:s', 'America/Sao_Paulo' );
            $agora = strtotime( $agora );
            $prazo_confirmacao = strtotime( $inscricao['prazo_confirmacao'] );

            if ( $prazo_confirmacao < $agora ) {
                return new WP_Error(
                    'prazo_expirado',
                    'O prazo para confirmação expirou.',
                );
            }
        }

        $resultado = $wpdb->update(
            self::TABELA_INSCRICOES,
            [
                'confirmou_presenca' => $confirmou_presenca,
                'updated_at' => obter_data_com_timezone( 'Y-m-d H:i:s', 'America/Sao_Paulo' )
            ],
            [
                'id' => $inscricao_id
            ],
            [
                '%d',
                '%s'
            ],
            [
                '%d'
            ]
        );

        if ( $resultado === false ) {

            return new WP_Error(
                'erro_inesperado',
                'Não foi possível registrar sua resposta.',
            );
        }

        return [
            'success' => true,
            'message' => $confirmou_presenca === 1
                ? 'Interesse confirmado com sucesso.'
                : 'Interesse cancelado com sucesso.'
        ];

    }
}

new Inscricao();