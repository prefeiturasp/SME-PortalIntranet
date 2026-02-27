<div  id="noticias_fx" class="row overflow-auto aa">
    <?php query_posts(array(
        'cat' => get_sub_field('fx_noticias_1_1'),
        'post_per_page' => -1
    )); ?>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <div class="col-sm-4 text-center">
            <?php
                $thumbs = get_thumb(get_the_id(), 'default-image'); 
            ?>
            <img src="<?php echo $thumbs[0]; ?>" width="100%" alt="<?php echo $thumbs[1]; ?>">
            <p><a href="<?php echo get_permalink(); ?>"><h3><?php the_title(); ?></h3></a></p>
        </div>
    <?php endwhile; endif; ?>
    <?php wp_reset_query(); ?>
</div>