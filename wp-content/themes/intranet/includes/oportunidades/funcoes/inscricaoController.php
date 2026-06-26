<?php

class Inscricao {

    const TABELA_INSCRICOES = 'int_oportunidade_inscricoes';

    const STATUS_INSCRITO = 'inscrito';

    public function __construct() {

        add_action( 'wp_ajax_realizar_inscricao', [$this, 'handle_realizar_inscricao'] );

        if ( is_admin() ) {
            add_action( 'wp_ajax_atualizar_etapa_candidatos', [$this, 'handle_atualizar_etapa_candidatos'] );
            add_action( 'wp_ajax_comunicar_selecionados', [$this, 'handle_comunicar_selecionados'] );
            add_action( 'wp_ajax_enviar_email_comunicado', [$this, 'handle_enviar_email_comunicado'] );
        }
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
    public static function atualizar_etapa_candidatos( array $id_inscricoes, string $etapa, string $post_id ) {

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

        $query = $wpdb->prepare(
            "
            UPDATE " . self::TABELA_INSCRICOES . "

            SET
                status = %s,
                updated_at = %s

            WHERE id IN ($placeholders)
            ",
            array_merge( [$etapa, current_time( 'mysql' )], $ids )
        );


        $resultado = $wpdb->query( $query );

        if ( $resultado === false ) {

            return [
                'success' => false,
                'message' => 'Não foi possível completar ação.'
            ];
        }
        
        
        // Instanciando a classe
        $envio = new \EnviaEmailOportunidade\classes\Envia_Emails_Oportunidades_SME(
            $id_inscricoes,  // Pode ser um único ID ou array de IDs
            $post_id,        // ID da oportunidade
            $etapa           // Etapa atual
        );

        // Executando o envio
        $envio->enviar_emails_conforme_etapa();

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
                $post_id
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
}

new Inscricao();