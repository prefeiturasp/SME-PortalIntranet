<?php 
    $chave = 'fx_noticias_' . $args['key']; 
    $coluna = $args['size'];
    if($coluna ==  1){
        $classe = 'col-sm-12';
    } else {
        $classe = 'col-md-6 col-sm-12';
    }
?>
<div  id="noticias_fx" class="row overflow-auto">
    <?php query_posts(array(
        'cat' => get_sub_field($chave),
        'post_per_page' => -1
    )); ?>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <div class="<?php echo $classe; ?> mt-3 mb-3 text-center">
            <?php
                $thumbs = get_thumb(get_the_id(), 'default-image'); 
            ?>
            <img src="<?php echo $thumbs[0]; ?>" width="100%" alt="<?php echo $thumbs[1]; ?>">
            <p><a href="<?php echo get_permalink(); ?>"><h3><?php the_title(); ?></h3></a></p>
        </div>
    <?php endwhile; endif; ?>
    <?php wp_reset_query(); ?>
</div>