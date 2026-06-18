<?php

class Inscricao {

    const TABELA_INSCRICOES = 'int_oportunidade_inscricoes';

    const STATUS_INSCRITO = 'inscrito';

    public function __construct() {

        add_action( 'wp_ajax_realizar_inscricao', [$this, 'handle_realizar_inscricao'] );

        if ( is_admin() ) {
            add_action( 'wp_ajax_atualizar_etapa_candidatos', [$this, 'handle_atualizar_etapa_candidatos'] );
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
     * Realiza inscrição
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
                'descricao' => '-',
                'classe' => 'inscrito'
            )
        );
    }

}

new Inscricao();