<?php
extract( $args );

$outras_oportunidades_args = [
    'post_type' => 'oportunidade',
    'post_status' => 'publish',
    'post__not_in' => [get_the_id()],
    'posts_per_page' => 8
];

$status_oportunidades = $status ?? 'abertas';
$hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );

switch ( $status_oportunidades ) {

    case 'abertas':

        $outras_oportunidades_args['meta_query'][] = [
            'key'     => 'inicio_inscricoes',
            'value'   => $hoje,
            'compare' => '<=',
            'type'    => 'NUMERIC'
        ];

        $outras_oportunidades_args['meta_query'][] = [
            'key'     => 'ence_inscricoes',
            'value'   => $hoje,
            'compare' => '>=',
            'type'    => 'NUMERIC'
        ];

        break;

    case 'em-breve':

        $outras_oportunidades_args['meta_query'][] = [
            'key'     => 'inicio_inscricoes',
            'value'   => $hoje,
            'compare' => '>',
            'type'    => 'NUMERIC'
        ];

        break;

    case 'encerradas':

        $args[] = [
            'key'     => 'ence_inscricoes',
            'value'   => $hoje,
            'compare' => '<',
            'type'    => 'NUMERIC'
        ];

        break;
}

$outras_oportunidades = new WP_Query( $outras_oportunidades_args );

?>

<?php if ( $outras_oportunidades->have_posts() ) : ?>
    <div class="col-lg-3">
        <div class="card sidebar-card border-0 shadow-sm">
            <div class="sidebar-header">
                <h3><?php echo esc_html( $titulo ); ?></h3>

                <a href="<?php echo esc_url( $url_pagina_principal ); ?>">
                    Ver todas
                </a>
            </div>

            <?php
            while ( $outras_oportunidades->have_posts() ) :
                $outras_oportunidades->the_post();
                ?>

                <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="sidebar-item">
                    <h4><?php echo esc_html( get_the_title() ); ?></h4>

                    <?php if ( $tipos_oportindade = get_field( 'tipo_oportunidade' ) ) : ?>
                        <?php foreach ( $tipos_oportindade as $tipo ) : ?>
                            <span class="m-0"><?php echo esc_html( $tipo['label'] ); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php
                    if ( $local_id = get_field( 'local_trabalho' ) ) :
                        $local_trabalho = get_term_by( 'term_id', $local_id, 'locais' );
                        ?>  
                        <p><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html( $local_trabalho->name ); ?></p>
                        <?php
                    endif;
                    ?>
                </a>

                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
<?php endif; ?>