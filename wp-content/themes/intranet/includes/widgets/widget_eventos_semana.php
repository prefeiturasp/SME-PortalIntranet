<?php

wp_add_dashboard_widget(
    'calendario_sorteios_semana_widget',
    'Sorteios da Semana',
    'renderizar_eventos_semana',
);

function obter_dias_semana_atual( bool $remover_hoje = false ) {
    $labels = [];
    $hoje = obter_data_com_timezone( 'Y-m-d', 'America/Sao_Paulo' );
    $data_inicio = new DateTime( 'monday this week' );
    $data_fim    = new DateTime( 'sunday this week' );
    $intervalo = new DateInterval( 'P1D' );
    $periodo   = new DatePeriod( $data_inicio, $intervalo, $data_fim->modify( '+1 day' ) );

    foreach ($periodo as $data) {
        if ( $remover_hoje && $data->format( 'Y-m-d' ) === $hoje ) {
            continue;
        }

        $label = date_i18n('l - d/m/Y', $data->getTimestamp());
        $labels[$label] = [];
    }

    return $labels;
}

//Busca e retorna a lista dos eventos da semana
function renderizar_eventos_semana() {
    $hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );
    $timestamp = strtotime( $hoje );
    $inicio_semana  = date( 'Ymd', strtotime( 'monday this week', $timestamp ) );
    $fim_semana    = date('Ymd', strtotime( 'sunday this week', $timestamp ) );

    add_filter( 'posts_where', 'filtro_posts_where_evento_datas' );
    add_filter( 'posts_where', 'filtro_posts_where_evento_premios' );

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'meta_query'     => array(
            'relation' => 'OR',
            'sorteio_multiplo' => array(
                'key'     => 'evento_datas_$_data_sorteio',
                'value'   => array($inicio_semana, $fim_semana),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC'
            ),
            'sorteio_premios' => array(
                'key'     => 'evento_premios_$_data_sorteio',
                'value'   => array($inicio_semana, $fim_semana),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC'
            ),
            'sorteio_unico' => array(
                'key'     => 'data_sorteio',
                'value'   => array($inicio_semana, $fim_semana),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC'
            ),
            'sorteio_periodo' => array(
                'key'     => 'evento_periodo_data_sorteio',
                'value'   => array($inicio_semana, $fim_semana),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC'
            ),
        ),
        'suppress_filters' => false,
        'orderby'          => array(
            'sorteio_unico'    => 'ASC',
            'sorteio_multiplo' => 'ASC',
            'sorteio_premios'  => 'ASC',
            'sorteio_periodo'  => 'ASC',
        ),
    );

    $query = new WP_Query( $args );

    remove_filter( 'posts_where', 'filtro_posts_where_evento_datas' );
    remove_filter( 'posts_where', 'filtro_posts_where_evento_premios' );

    $eventos = obter_dias_semana_atual( true );

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
    
            $evento_datas = [];

            // Caso 1: repetidor
            if (have_rows('evento_datas')) {
                while (have_rows('evento_datas')) {
                    the_row();

                    $data_sorteio = get_sub_field( 'data_sorteio' );
                    $data_evento = get_sub_field( 'data' );
    
                    if ($data_sorteio) {
                        $data_formatada = date('Y-m-d', strtotime($data_sorteio));
    
                        // ignora dia atual
                        if ($data_formatada === $hoje) {
                            continue;
                        }
    
                        $evento_datas[] = [
                            'data_sorteio'  => $data_formatada,
                            'data_evento'   => $data_evento,
                        ];
                    }
                }
            } elseif (have_rows('evento_premios')) {
                while (have_rows('evento_premios')) {
                    the_row();

                    $data_sorteio = get_sub_field( 'data_sorteio' );
                    $data_evento = get_sub_field( 'data' );
    
                    if ($data_sorteio) {
                        $data_formatada = date('Y-m-d', strtotime($data_sorteio));
    
                        // ignora dia atual
                        if ($data_formatada === $hoje) {
                            continue;
                        }
    
                        $evento_datas[] = [
                            'data_sorteio'  => $data_formatada,
                            'data_evento'   => $data_evento,
                        ];
                    }
                }
            } elseif( $data_sorteio = get_field('evento_periodo_data_sorteio') ){
                // Caso 2: campo único
                $data_sorteio = get_field('evento_periodo_data_sorteio', $post_id, false);
    
                if ($data_sorteio) {
                    $data_formatada = date('Y-m-d', strtotime($data_sorteio));
    
                    if ($data_formatada !== $hoje) {
                        $evento_datas[] = [
                            'data_sorteio'  => $data_sorteio,
                            'data_evento'   => $data_formatada,
                            'titulo' => get_the_title(),
                        ];
                    }
                }
            } else {

                // Caso 2: campo único
                $data_sorteio = get_field('data_sorteio');
                $hora_sorteio = get_field('hora_sorteio');
    
                if ($data_sorteio) {
                    $data_formatada = date('Y-m-d', strtotime($data_sorteio));
    
                    if ($data_formatada !== $hoje) {
                        $evento_datas[] = [
                            'data_sorteio'  => $data_formatada,
                            'data_evento'   => $data_formatada . ' ' . $hora_sorteio,
                        ];
                    }
                }
            }
            
            // Agrupa os sorteios por data
            foreach ($evento_datas as $item) {
                $data = $item['data_sorteio'];
                $label = date_i18n( 'l - d/m/Y', strtotime ($data ) );

                if ( in_array( $label, array_keys( $eventos ) ) ) {

                    $eventos[$label][$post_id] = [
                        'post_id'  => $post_id,
                        'title'    => get_the_title(),
                        'data'     => $data,
                        'link'     => get_edit_post_link(),
                        'local'    => get_field('local'),
                    ];

                }
            }
        }
    }

    wp_reset_postdata();
    remove_filter('posts_where', 'my_events_where');

    get_template_part( 'includes/widgets/template-parts/accordion-dias', null, ['eventos' => $eventos] );
}
