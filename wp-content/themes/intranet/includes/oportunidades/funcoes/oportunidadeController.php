<?php

class Oportunidade {

    const TABELA_INSCRICOES = 'int_oportunidade_inscricoes';
    const TABELA_CURRICULO = 'int_banco_talentos';

    public function __construct() {

        if ( !is_admin() ) {
            add_filter( 'posts_clauses', [$this, 'ordenar_oportunidades_por_status'], 10, 2 );
        }
    }

    // Retorna o status da oportunidade.
    public static function get_status( $post_id ) {

        $inicio = get_post_meta( $post_id, 'inicio_inscricoes', true );
        $fim = get_post_meta( $post_id, 'ence_inscricoes', true );

        $hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );

        if ( !empty( $inicio ) && $inicio > $hoje ) {
            return [
                'value' => 'breve',
                'label' => 'Inscrições em Breve',
                'class' => 'inscricoes-em-breve'
            ];
        }

        if ( !empty($fim) && $fim < $hoje ) {
            return [
                'value' => 'encerrada',
                'label' => 'Inscrições Encerradas',
                'class' => 'inscricoes-encerradas'
            ];
        }

        return [
                'value' => 'aberta',
                'label' => 'Inscrições Abertas',
                'class' => 'inscricoes-abertas'
            ];
    }

    // Aplica o filtro para personalizar a ordenação do resultado das consultas de oportunidades
    public function ordenar_oportunidades_por_status ( $clauses, $query ) {

        if ( !$query->get( 'ordenar_por_status' ) || $query->get('post_type') !== 'oportunidade' ) {
            return $clauses;
        }

        global $wpdb;

        $hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );

        $clauses['join'] .= "
            LEFT JOIN {$wpdb->postmeta} inicio_meta
                ON ({$wpdb->posts}.ID = inicio_meta.post_id
                AND inicio_meta.meta_key = 'inicio_inscricoes')

            LEFT JOIN {$wpdb->postmeta} fim_meta
                ON ({$wpdb->posts}.ID = fim_meta.post_id
                AND fim_meta.meta_key = 'ence_inscricoes')
        ";

        // 1 = Em breve, 2 = Inscrições abertas, 3 = Encerradas
        $clauses['orderby'] = "
            CASE
                WHEN inicio_meta.meta_value > '{$hoje}'
                THEN 1

                WHEN fim_meta.meta_value < '{$hoje}'
                THEN 3

                ELSE 2

            END ASC,

            {$wpdb->posts}.post_date DESC
        ";

        $clauses['groupby'] = "{$wpdb->posts}.ID";

        return $clauses;
    }

    public static function get_inscricoes( int $oportunidade_id ) {
        global $wpdb;

        $inscricoes = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT 
                    oi.id,
                    oi.curriculo_id,
                    oi.rf,
                    oi.status,
                    oi.prazo_confirmacao,
                    oi.status_confirm,
                    oi.confirmou_presenca,
                    oi.created_at,
                    oi.updated_at,
                    oi.atualizacao_auto,
                    bt.nome_completo,
                    bt.nome_social,
                    bt.email_principal,
                    bt.telefone_whatsapp
                FROM " . self::TABELA_INSCRICOES . " AS oi
                INNER JOIN " . self::TABELA_CURRICULO . " AS bt 
                    ON oi.curriculo_id = bt.id
                WHERE oi.oportunidade_id = %d
                ORDER BY oi.created_at ASC",
                $oportunidade_id
            ),
            ARRAY_A
        );

        return $inscricoes;
    }

    public static function get_inscritos_by_etapa( int $oportunidade_id, string $etapa_processo ) {

        global $wpdb;

        $inscricoes = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT 
                    id,
                    curriculo_id,
                    rf,
                    status
                FROM " . self::TABELA_INSCRICOES . "
                WHERE oportunidade_id = %d
                AND status = %s",
                $oportunidade_id,
                $etapa_processo
            ),
            ARRAY_A
        );

        return $inscricoes;
    }

    public static function get_oportunidades_encerradas( bool $dia_anterior = false, bool $apenas_ids = false ) {
        
        $hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );
    
        $args = [
            'post_type' => 'oportunidade',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'ence_inscricoes',
                    'value' => $hoje,
                    'compare' => '<'
                ]
            ]
        ];

        if ( $dia_anterior ) {

            $timezone = new DateTimeZone( 'America/Sao_Paulo' );
            $ontem = DateTime::createFromFormat( 'Ymd', $hoje, $timezone );
            $ontem->modify('-1 day');

            $args['meta_query'] = [
                [
                    'key' => 'ence_inscricoes',
                    'value' => $ontem->format( 'Ymd' ),
                    'compare' => '='
                ]
            ];
        }

        if ( $apenas_ids ) {
            $args['fields'] = 'ids';
        }

        return get_posts( $args );
    }

}

new Oportunidade();