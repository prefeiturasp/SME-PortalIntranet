<?php

wp_add_dashboard_widget(
    'calendario_sorteios_encerrados_widget',
    'Eventos Encerrados',
    'renderizar_eventos_encerrados',
);

//Busca e retorna a lista dos eventos encerrados
function renderizar_eventos_encerrados() {
    $hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );

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

        $eventos[] = [
            'post_id' => get_the_ID(),
            'timestamp' => $timestamp,
            'data' => $data,
            'hora' => $hora_real, // usamos a hora original aqui
            'title' => get_the_title(),
            'local' => get_field('local'),
        ];
    }

    wp_reset_postdata();

    get_template_part( 'includes/widgets/template-parts/lista-eventos', null, [
        'eventos'   => $eventos,
        'filtro'    => null,
        'mensagem'  => 'Não há sorteios encerrados.'
    ] );
}
