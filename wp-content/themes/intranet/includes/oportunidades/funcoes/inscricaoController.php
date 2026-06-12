<?php

class Inscricao {

    const TABELA_INSCRICOES = 'int_oportunidade_inscricoes';

    const STATUS_INSCRITO = 'inscrito';

    public function __construct() {

        add_action( 'wp_ajax_realizar_inscricao', [$this, 'handle_realizar_inscricao'] );
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

}

new Inscricao();