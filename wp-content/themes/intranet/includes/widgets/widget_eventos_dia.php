<?php

use Pest\Support\Arr;

wp_add_dashboard_widget(
    'calendario_sorteios_hoje_widget',
    'Hoje',
    'renderizar_eventos_hoje',
);


//Busca e retorna a lista dos eventos do dia
function renderizar_eventos_hoje() {
    $hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );
    $user_id = get_current_user_id();

    add_filter( 'posts_where', 'filtro_posts_where_evento_datas' );
    add_filter( 'posts_where', 'filtro_posts_where_evento_premios' );

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 100,
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => 'data_sorteio',
                'value'   => $hoje,
                'compare' => '=',
                'type'    => 'NUMERIC'
            ),
            array(
                'key'     => 'evento_datas_$_data_sorteio',
                'value'   => $hoje,
                'compare' => '=',
                'type'    => 'NUMERIC'
            ),
            array(
                'key'     => 'evento_premios_$_data_sorteio',
                'value'   => $hoje,
                'compare' => '=',
                'type'    => 'NUMERIC'
            ),
            array(
                'key'     => 'evento_periodo_data_sorteio',
                'value'   => $hoje,
                'compare' => '=',
                'type'    => 'NUMERIC'
            ),
        ),
        'orderby'  => 'meta_value',
        'order'    => 'ASC'
    );

    $query = new WP_Query($args);

    remove_filter( 'posts_where', 'filtro_posts_where_evento_datas' );
    remove_filter( 'posts_where', 'filtro_posts_where_evento_premios' );

    $eventos = [];

    while ($query->have_posts()) {
        $query->the_post();

        $data = get_field('data_sorteio');
        $hora_real = get_field('hora_sorteio'); // valor do ACF original (pode estar vazio)

        // Hora artificial apenas para ordenação
        $hora_ord = $hora_real;
        if (empty($hora_ord)) {
            $hora_ord = ($comparador === '<') ? '23:59' : '00:00';
        }

        $timestamp = strtotime($data . ' ' . $hora_ord);

        $responsavel = get_field( 'responsavel_noticia' );
        $nome_responsavel = $responsavel->display_name ?? 'Sem responsável';
        $id_responsavel = $responsavel->ID ?? 0;
        $classe_responsavel = '';

        if ( empty( $id_responsavel ) ) {
            $classe_responsavel = 'text-secondary';
        } elseif ( $user_id == $id_responsavel ) {
            $classe_responsavel = 'text-success';
        } else {
            $classe_responsavel = 'text-primary';
        }

        $eventos[] = [
            'post_id' => get_the_ID(),
            'timestamp' => $timestamp,
            'data' => $data,
            'hora' => $hora_real, // usamos a hora original aqui
            'title' => get_the_title(),
            'local' => get_field('local'),
            'nome_responsavel' => $nome_responsavel,
            'id_responsavel' => $id_responsavel,
            'classe_responsavel' => $classe_responsavel
        ];
    }

    wp_reset_postdata();

    usort($eventos, function($a, $b) use ($ordem) {
        return ($ordem === 'ASC') ? $a['timestamp'] <=> $b['timestamp'] : $b['timestamp'] <=> $a['timestamp'];
    });

    $cortesias = get_cortesias_dia( $hoje );
    $cortesias = ordenar_eventos_por_responsavel( $cortesias );
    $eventos = ordenar_eventos_por_responsavel( $eventos );

    get_template_part( 'includes/widgets/template-parts/lista-eventos', null, [
        'eventos'   => $eventos,
        'cortesias' => $cortesias,
        'filtro'    => 'hoje',
        'mensagem'  => 'Nenhuma atividade prevista para hoje.'
    ] );
}

function get_cortesias_dia( string $data ) {

    $user_id = get_current_user_id();

    add_filter( 'posts_where', 'filtro_posts_where_evento_datas' );
    add_filter( 'posts_where', 'filtro_posts_where_evento_premios' );

    $cortesias_args = array(
        'post_type'      => 'cortesias',
        'posts_per_page' => 100,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'     => 'administracao_ingressos',
                'value'   => 'ascom',
            ),
            array(
                'relation' => 'OR',
                array(
                    'key'     => 'evento_datas_$_encerramento_inscricoes',
                    'value'   => $data,
                    'compare' => '=',
                    'type'    => 'NUMERIC'
                ),
                array(
                    'key'     => 'evento_premios_$_encerramento_inscricoes',
                    'value'   => $data,
                    'compare' => '=',
                    'type'    => 'NUMERIC'
                ),
                array(
                    'key'     => 'evento_periodo_encerramento_inscricoes',
                    'value'   => $data,
                    'compare' => '=',
                    'type'    => 'NUMERIC'
                ),
            )
        ),
        'orderby'  => 'meta_value',
        'order'    => 'ASC'
    );

    $query_cortesias = new WP_Query( $cortesias_args );

    remove_filter( 'posts_where', 'filtro_posts_where_evento_datas' );
    remove_filter( 'posts_where', 'filtro_posts_where_evento_premios' );

    $cortesias = [];
    
    if ( $query_cortesias->have_posts() ) {
        while ($query_cortesias->have_posts()) {
            $query_cortesias->the_post();

            $responsavel = get_field( 'responsavel_noticia' );
            $nome_responsavel = $responsavel->display_name ?? 'Sem responsável';
            $id_responsavel = $responsavel->ID ?? 0;
            $classe_responsavel = '';

            if ( empty( $id_responsavel ) ) {
                $classe_responsavel = 'text-secondary';
            } elseif ( $user_id == $id_responsavel ) {
                $classe_responsavel = 'text-success';
            } else {
                $classe_responsavel = 'text-primary';
            }

            $cortesias[] = [
                'post_id' => get_the_ID(),
                'title' => get_the_title(),
                'local' => get_field('local'),
                'nome_responsavel' => $nome_responsavel,
                'id_responsavel' => $id_responsavel,
                'classe_responsavel' => $classe_responsavel
            ];
        }

        wp_reset_postdata();
    }

    return $cortesias;
}
