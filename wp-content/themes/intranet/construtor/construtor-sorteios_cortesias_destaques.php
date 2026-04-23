<?php

wp_enqueue_script( 'swiper-slider' );
wp_enqueue_style( 'swiper-slider' );

$eventos_destaque_ids = get_sub_field( 'sorteios_cortesias_destaques' );
$paginas_destaque_ids = get_sub_field( 'paginas_destaque' );
$colunas_evento = $paginas_destaque_ids ? 'col-md-9' : 'col-md-12';

$eventos_args = [
    'post_type' => ['post', 'cortesias'],
    'post_status' => 'publish',
    'ignore_sticky_posts' => true,
    'posts_per_page' => 5,
];

if ( !empty( $eventos_destaque_ids ) ) {
    $eventos_args['post__in'] = $eventos_destaque_ids;
    $eventos_args['orderby'] = 'post__in';
}

$eventos = new WP_Query( $eventos_args );

?>

<div class="container my-5">

    <div class="row g-3 row-equal-height">

        <!-- Páginas em destaque -->
        <?php if ( $paginas_destaque_ids ) : ?>
            <div class="col-md-3">
                <div class="side-cards mb-4 mb-sm-0">
                    <?php foreach ( $paginas_destaque_ids as $pagina_id ) : ?>
                        <a href="<?php echo esc_url( get_the_permalink( $pagina_id ) ); ?>" class="side-card">
                            <img 
                            src="<?php echo esc_url( get_the_post_thumbnail_url( $pagina_id ) ); ?>" 
                            alt="<?php echo esc_html( get_the_title( $pagina_id ) ); ?>">

                            <span class="side-card-title">
                                <?php echo esc_html( get_the_title( $pagina_id ) ); ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>


        <!-- Slider de eventos em destaqu -->
        <?php if ( $eventos->have_posts() ) : ?>

            <div class="<?php echo esc_html( $colunas_evento ); ?> slider-column position-relative">
                <div class="swiper main-content-slider">
                    <div class="swiper-wrapper">
                        <?php
                        while ( $eventos->have_posts() ) :
                            $eventos->the_post();

                            $tipo_post = get_post_type_label( get_the_ID() );
                            $tipo_evento = get_field( 'tipo_evento' );
                            $local = get_field('local');
                            $local_term =  get_term( $local ) ?: false;
                            $image = get_the_post_thumbnail_url( get_the_ID(), 'default-image' );

                            if ( !$image ) {
                                $image = get_field( 'sorteios_cortesias_placeholder', 'options' );
                            }

                            ?>
                            <div class="swiper-slide">
                                <div class="slide-item">
                                    <div class="row g-0 h-100">
                                        <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="col-md-8 slide-image p-0">
                                            <img src="<?php echo esc_url( $image ); ?>" alt="<?php the_title(); ?>">
                                        </a>

                                        <div class="col-md-4 slide-content pl-md-3 pr-md-5 p-4 d-flex flex-column justify-content-center">
                                            <div class="row mb-4">
                                                <a href="<?php echo esc_url( get_the_permalink() ); ?>" class="title col-10">
                                                    <?php the_title(); ?>
                                                </a>
                                                <div class="post_like col-2 py-2 px-0">
                                                    <?php 
                                                        global $wpdb;
                                                        $l = 0;
                                                        $postid = get_the_id();
                                                        $clientip  = get_client_ip();
                                                        $row1 = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$postid' AND clientip = '$clientip'");
                                                        if(!empty($row1)){
                                                            $l = 1;
                                                        }
                                                        $totalrow1 = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$postid'");
                                                        $total_like1 = $wpdb->num_rows;
                                                    ?>
                                                    <a class="pp_like <?php if($l==1) {echo "likes text-danger "; } ?>d-flex flex-column justify-content-center align-items-center" id="pp_like_<?php echo get_the_id(); ?>" href="#" data-id="<?php echo get_the_id(); ?>">
                                                        <img src="<?php echo esc_url( get_template_directory_uri() . '/img/icone-likes.svg' ); ?>" alt="like">
                                                        <span><?php echo $total_like1; ?> <?php echo $total_like1 == 1 ? 'Like' : 'Likes'; ?></span>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="evento-info border-left pl-md-2">
                                                <h5 class="fw-bold mb-4 data">
                                                    <?php if ( $tipo_post === 'cortesias' ) : ?>
                                                        Ingressos gratuitos por ordem de inscrição, enquanto houver disponibilidade
                                                    <?php endif; ?>

                                                    <?php
                                                    if ( $tipo_post === 'sorteio' ) :
                                                        echo "Sorteio " . esc_html( obter_proxima_data_sorteio( get_the_ID(), false ) );
                                                    endif;
                                                    ?>
                                                </h5>
                                            
                                                <?php if ( $local_term && !is_wp_error( $local_term ) ) : ?>
                                                    <p class="mb-1">
                                                        <strong>Local:</strong> <?php echo esc_html( $local_term->name ); ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <?php if ( $tipo_evento === 'premio' ) : ?>
                                                    <p><strong> Prêmio: </strong>Consulte detalhes</p>
                                                    
                                                <?php elseif ( $tipo_evento === 'data' ) :
                                                    $datas_evento_info = get_field( 'evento_datas' );
                                                    $datas_evento = wp_list_pluck( $datas_evento_info, 'data' );
                                                    $datas_disponiveis = filtrar_ordenar_datas_futuras( $datas_evento );

                                                    if ( !empty( $datas_disponiveis ) ) {
                                                        $lista_datas = [];
                                                        $total = count( $datas_disponiveis );
                                                        $format = $total > 1 ? 'd/m' : 'd/m';
                                                        $label = _n( 'Data', 'Datas', $total );  
                                                    }
                                                    ?>
                                                    <?php if ( !empty( $datas_disponiveis ) ) : ?>

                                                        <?php
                                                        foreach ( array_chunk( $datas_disponiveis, 3 )[0] as $data ) {
                                                            $dt = new DateTime($data);

                                                            $data = $dt->format( $format );
                                                            $hora = $dt->format( 'H' );
                                                            $minuto = $dt->format( 'i' );
                                                            $hora_fomatada = $minuto == '00' ? "{$hora}h" : "{$hora}h{$minuto}";
                                                            
                                                            $data_formatada = "{$data} {$hora_fomatada}";
                                                            $lista_datas[] = $data_formatada;
                                                        }
                                                        ?>

                                                        <p class="datas-disponiveis">
                                                            <strong><?php echo esc_html( $label ); ?>:</strong>
                                                            <?php echo esc_html( implode( ' | ', $lista_datas ) ); ?>
                                                        </p>

                                                        <?php if ( $total >= 3 ) : ?>
                                                            <a href="<?php echo esc_url( get_the_permalink() ); ?>">
                                                                Ver todas as datas e horários
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php elseif ( $tipo_evento === 'periodo' ) :
                                                    $info_periodo_evento = get_field( 'evento_periodo' );
                                                    ?>
                                                    <p><strong>Periodo: </strong><?php echo esc_html( $info_periodo_evento['descricao'] ); ?></p>
                                                <?php endif; ?>
                                            </div>

                                            <div class="mt-3">
                                                <?php if ( $tipo_post === 'cortesias' ) : ?>
                                                    <span class="badge badge-cortesia px-3 py-2">
                                                        <i class="fa fa-bolt" aria-hidden="true"></i>
                                                        Ordem de Inscrição
                                                    </span>
                                                <?php endif; ?>

                                                <?php if ( $tipo_post === 'sorteio' ) : ?>
                                                    <span class="badge badge-sorteio px-3 py-2">
                                                        <i class="fa fa-cube" aria-hidden="true"></i>
                                                        Sorteio
                                                    </span>
                                                <?php endif; ?>

                                                <?php if ( check_usuario_inscrito_evento( get_the_ID() ) ) : ?>
                                                    <span class="badge badge-inscricao px-3 py-2">
                                                        <i class="fa fa-check-circle" aria-hidden="true"></i> Inscrito
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>

                    <div class="carro-destaques swiper-pagination"></div>
                </div>

                <button class="custom-prev">
                    <i class="fa fa-angle-left"></i>
                </button>

                <button class="custom-next">
                    <i class="fa fa-angle-right"></i>
                </button>

            </div>

        <?php endif; ?>
    </div>
</div>
