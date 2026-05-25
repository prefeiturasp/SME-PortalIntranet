<?php

$oportunidade_args = [
    'post_type' => 'oportunidade',
    'post_status' => 'publish',
    'posts_per_page' => get_sub_field( 'oportunidades_por_pagina' ) ? get_sub_field( 'oportunidades_por_pagina' ) : 12,
    'paged' => max( 1, get_query_var( 'paged' ) ),
    'ordenar_por_status' => true
];

$filtros_aplicados = false;
$oportunidades = new WP_Query( $oportunidade_args );

?>

<div class="container py-4">

    <?php if ( $oportunidades->have_posts() ) : ?>
        <p class="mb-4 text-muted">
            <strong><?php echo esc_html( $oportunidades->found_posts ); ?></strong>
            <?php echo esc_html( _n( 'oportunidade encontrada', 'oportunidades encontradas', $oportunidades->found_posts ) ); ?>
        </p>
    <?php endif; ?>

    <div class="row">

        <?php if ( !$oportunidades->have_posts() && !$filtros_aplicados ) : ?>
            <div class="container">
                <div class="alerta-sem-oportunidades">
                    <div class="alerta-info">
                        <i class="fa fa-briefcase fa-3x mb-3" aria-hidden="true"></i>
                        <p>No momento, não há Oportunidades disponíveis. Continue acompanhando as publicações deste Portal e não deixe de manter seu currículo sempre atualizado.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php
        if ( $oportunidades->have_posts() ) :
            while ( $oportunidades->have_posts() ) :
                $oportunidades->the_post();
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card-oportunidade">
                        <div class="conteudo-oportunidade">
                            <h3 class="titulo-oportunidade">
                                <?php echo esc_html( get_the_title() ); ?>
                            </h3>

                            <?php if ( $tipos_oportindade = get_field( 'tipo_oportunidade' ) ) : ?>
                                <div class="subtitulo-oportunidade">
                                    <?php foreach ( $tipos_oportindade as $tipo ) : ?>
                                        <p><?php echo esc_html( $tipo['label'] ); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php
                            if ( $local_id = get_field( 'local_trabalho' ) ) :
                                $local_trabalho = get_term_by( 'term_id', $local_id, 'locais' );
                                ?>
                                <div class="info-item">
                                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                                    <span>Local: <?php echo esc_html( $local_trabalho->name ); ?></span>
                                </div>
                                <?php
                            endif;
                            ?>

                            <?php
                            if ( $eixo_id = get_field( 'eixo_atuacao' ) ) :
                                $eixo_atuacao = get_term_by( 'term_id', $eixo_id, 'eixos_atuacao' );
                                ?>
                                <div class="info-item">
                                    <i class="fa fa-database" aria-hidden="true"></i>
                                    <span>Eixo de Atuação: <?php echo esc_html( $eixo_atuacao->name ); ?></span>
                                </div>
                                <?php
                            endif;
                            ?>

                            <?php
                            $inicio_inscricoes = get_field( 'inicio_inscricoes' );
                            $fim_inscricoes = get_field( 'ence_inscricoes' );

                            if ( $inicio_inscricoes && $fim_inscricoes ) :
                                ?>
                                <div class="info-item">
                                    <i class="fa fa-calendar-o" aria-hidden="true"></i>
                                    <span>Prazo de Inscrição: <?php echo esc_html( "{$inicio_inscricoes} a {$fim_inscricoes}" ); ?></span>
                                </div>
                                <?php
                            endif;
                            ?>
                        </div>
                        
                        <div class="card-footer-custom">

                            <?php
                            $status_oportunidade = Oportunidade::get_status( get_the_ID() ) ?? null;
                            
                            if ( $status_oportunidade ) :
                                ?>
                                <span class="badge-oportunidade <?php echo esc_html( $status_oportunidade['class'] ); ?>">
                                    <?php echo esc_html( $status_oportunidade['label'] ); ?>
                                </span>
                                <?php
                            endif;
                            ?>

                            <div>
                                <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="btn btn-detalhes">
                                    Ver Detalhes
                                </a>
                            </div>

                        </div>

                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>

    <div class="container mt-4 eventos-paginacao">
        <div class="row">
            <div class="col-sm-12">
                <div class="pagination-prog text-center">
                    <?php wp_pagenavi( array( 'query' => $oportunidades ) ); ?>
                </div>
            </div>
        </div>
    </div>
</div>