<div class="container">
    <div class="row">
        <?php

        $args = array(
            'post_type'      => 'page',
            'posts_per_page' => -1,
            'post_parent'    => $post->ID,
        );

        $order = get_sub_field('ordenacao');

        if($order == 'title'){
            $args['order'] = 'ASC';
            $args['orderby'] = 'title';
        } else {
            $args['order'] = 'DESC';
            $args['orderby'] = 'date';
        }

        $parent = new WP_Query( $args );

        if ( $parent->have_posts() ) : ?>

            <?php while ( $parent->have_posts() ) : $parent->the_post(); ?>

                <div class="col-12 col-md-<?=get_sub_field('colunas'); ?>">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                        <div class="card-subpagina">
                            <img src="<?= get_the_post_thumbnail_url(); ?>" class="img-fluid" alt="Imagem ilustrativa para a página <?= get_the_title(); ?>">
                            <h2><?php the_title(); ?></h2>                    
                        </div>
                    </a>
                </div>

            <?php endwhile; ?>

        <?php endif; wp_reset_postdata(); ?>
    </div>
</div>