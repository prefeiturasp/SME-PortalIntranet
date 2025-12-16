<?php
// Slick
wp_register_style('slick', STM_THEME_URL . 'classes/assets/css/slick.css', null, null, 'all');
wp_enqueue_style('slick');

wp_register_style('slick-theme', STM_THEME_URL . 'classes/assets/css/slick-theme.css', null, null, 'all');
wp_enqueue_style('slick-theme');


?>

<div class="container">
    <div class="portais-destaques">
        <div class="portais-title d-flex justify-content-between">
            <h3>Portais e Sistemas</h3>
            <?php
                $ver_mais = get_sub_field('link_ver_mais');
                if($ver_mais)
                    echo '<p><a href="' . $ver_mais . '">Ver mais</a></p>';
            ?>
        </div>
        <div class="row">
            <div class="col-10 offset-1">
                <div class="slider portais">
                    <?php

                    // the query
                    $args = array(
                        'post_type' => 'portais',
                        'posts_per_page' => get_sub_field('quantidade'),
                        'meta_query' => array(
                            array(
                                'key'   => 'pagina_principal',
                                'value' => '1',
                            )
                        )
                    );

                    
                    $the_query = new WP_Query( $args ); ?>

                    <?php if ( $the_query->have_posts() ) : ?>

                        <!-- pagination here -->

                        <!-- the loop -->
                        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                            <div class="portais-sistemas">
                                <?php
                                    $imagem = get_field('imagem_destacada');
                                    $imagemPadrao = get_template_directory_uri() . '/img/categ-portais.jpg';
                                    if($imagem['sizes']['admin-list-thumb'])
                                        $imagemPadrao = $imagem['sizes']['admin-list-thumb'];
                                ?>

                                <a href="<?= get_field('insira_link'); ?>" target="_blank"><img src="<?= $imagemPadrao; ?>" alt="" srcset=""></a>
                                <h3><a href="<?= get_field('insira_link'); ?>" target="_blank"><?= get_the_title(); ?></a></h3>
                                
                            </div>

                        <?php endwhile; ?>
                        

                        <?php wp_reset_postdata(); ?>

                    <?php else : ?>
                        <p><?php _e( 'Não há nenhuma publicação encontrada.' ); ?></p>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>



<?php
// Portais e Sistemas

wp_enqueue_script('slick');
?>

<script>
    var $s = jQuery.noConflict();
    $s(document).ready(function(){
        $s('.portais').slick({
            slidesToShow: <?= get_sub_field('colunas'); ?>,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 5000,
            prevArrow:'<span class="slick-arrow arrow-left"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>',
            nextArrow:'<span class="slick-arrow arrow-right"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>',
            responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 4,
        infinite: true,
        dots: true
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
        });
    });
</script>