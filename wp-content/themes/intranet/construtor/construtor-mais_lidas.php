<?php
$chave = 'lidas_titulo_' . $args['key'];
$titulo = get_sub_field($chave); // titulo
$link = get_sub_field('lidas_ver_tudo'); // link
$qtd = get_sub_field('lidas_quantidade'); // quantidade
$colunas = get_sub_field('colunas'); // colunas

?>
    <div class="pt-3">
        <div class="row">
            <?php if($titulo && $titulo != ''){
                echo "<div class='col-sm-8 title-lidas'><p>" . $titulo . "</p></div>";
            }?>

            <?php if($link && $link != ''){
                echo "<div class='col-sm-4 link-more'><p class='text-right'><a href='" . $link . "'>ver tudo</a></p></div>";
            }?>
        </div>
    </div>
<?php

$args = array(
    'posts_per_page'=> $qtd,
    // Meta Key criada no functions.php
    'meta_key'=>'popular_posts',
    'orderby'=>'meta_value_num',
    'order'=>'DESC',
);

// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
    echo '<div class="container p-0">';
    echo '<div class="row">';
        while ( $the_query->have_posts() ) :
            $the_query->the_post();
            // Busca a imagem destaca / primeira imagem / imagem padrao -- functions.php
            $thumbs = get_thumb(get_the_ID(), 'thumbnail'); 
                
    ?>
        
        <div class="col-sm-<?php echo $colunas; ?> news-more">
            <a href="<?php echo get_the_permalink(); ?>">
                <img src="<?php echo $thumbs[0]; ?>" alt="<?php echo $thumbs[1]; ?>" class="img-fluid">
                <p><?php echo get_the_title(); ?></p>
            </a>
        </div>
    
    <?php                                
        endwhile;
    echo '</div>'; // row
    echo '</div>'; // container
} else {
    // no posts found
}
/* Restore original Post Data */
wp_reset_postdata();