<?php

wp_add_dashboard_widget(
    'calendario_sorteios_encerrados_widget',
    'Encerrados',
    'renderizar_eventos_encerrados',
);

//Busca e retorna a lista dos eventos encerrados
function renderizar_eventos_encerrados() {
    $hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );
    $user_id = get_current_user_id();

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'meta_query'     => array(
            'relation'   => 'AND',
            array(
                'key'     => 'enc_inscri',
                'value'   => '',
                'compare' => '!='
            ),
            array(
                'key'     => 'enc_inscri',
                'value'   => $hoje,
                'compare' => '<',
                'type'    => 'NUMERIC'
            ),
        ),
        'meta_key' => 'enc_inscri',
        'orderby'  => 'meta_value',
        'order'    => 'DESC'
    );

    $query = new WP_Query($args);

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

    $eventos = ordenar_eventos_por_responsavel( $eventos );

    wp_reset_postdata();

    $cortesias = get_cortesias_encerradas();
    $cortesias = ordenar_eventos_por_responsavel( $cortesias );

    get_template_part( 'includes/widgets/template-parts/lista-eventos', null, [
        'eventos'   => $eventos,
        'cortesias' => $cortesias,
        'filtro'    => null,
        'mensagem'  => 'Não há nenhuma atividade encerrada.'
    ] );
}

function get_cortesias_encerradas() {

    $hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );
    $user_id = get_current_user_id();

    $args = array(
        'post_type'      => 'cortesias',
        'post_status'    => 'publish',
        'posts_per_page' => 5,
        'meta_query'     => array(
            'relation'   => 'AND',
            array(
                'key'     => 'administracao_ingressos',
                'value'   => 'ascom',
            ),
            array(
                'key'     => 'enc_inscri',
                'value'   => '',
                'compare' => '!='
            ),
            array(
                'key'     => 'enc_inscri',
                'value'   => $hoje,
                'compare' => '<',
                'type'    => 'NUMERIC'
            ),
        ),
        'meta_key' => 'enc_inscri',
        'orderby'  => 'meta_value',
        'order'    => 'DESC'
    );

    $query = new WP_Query( $args );

    $cortesias = [];

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

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
