<?php

$blocoNoticias = get_sub_field('lidas_quantidade');

$args = array(
    'posts_per_page'=> 3,
    // Meta Key criada no functions.php
    'meta_key'=>'popular_posts',
    'orderby'=>'meta_value_num',
    'order'=>'DESC',
);

// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
    echo '<div class="container lista-noticias lidas-page mb-5">';
    echo '<div class="row">';
        while ( $the_query->have_posts() ) :
            $the_query->the_post();
            // Busca a imagem destaca / primeira imagem / imagem padrao -- functions.php
            $thumbs = get_thumb(get_the_ID()); 
                
    ?>
        
        <div class="col-sm-4 lista-noticia">
            <a href="<?php echo get_the_permalink(); ?>">
                <img src="<?php echo $thumbs[0]; ?>" alt="<?php echo $thumbs[1]; ?>" class="img-fluid">
                <p><?php echo get_the_title(); ?></p>
            </a>
        </div>
    
    <?php                                
        endwhile;
        echo '<div class="col-sm-12"><div class="border-bottom"></div></div>';
    echo '</div>'; // row
    echo '</div>'; // container
} else {
    // no posts found
}
/* Restore original Post Data */
wp_reset_postdata();

$qtd = $blocoNoticias - 3;

$args2 = array(
    'posts_per_page'=> $qtd,
    // Meta Key criada no functions.php
    'meta_key'=>'popular_posts',
    'orderby'=>'meta_value_num',
    'order'=>'DESC',
    'offset' => 3
);

// The Query
$the_query = new WP_Query( $args2 );

// The Loop
if ( $the_query->have_posts() ) {
    echo '<div class="container lista-noticias">';
   
        while ( $the_query->have_posts() ) :
            $the_query->the_post();
            // Busca a imagem destaca / primeira imagem / imagem padrao -- functions.php
            $thumbs = get_thumb(get_the_ID()); 
                
    ?>
        
        <section class="row mb-5">
            <article class="col-lg-10 col-sm-12">
                <?php

                // Busca a imagem destaca / primeira imagem / imagem padrao -- functions.php
                $thumbs = get_thumb(get_the_ID(), 'default-image');
        
                if ($thumbs){
                    echo '<figure class=" m-0">';
                    echo '<img src="'.$thumbs[0].'" class="img-fluid float-left mr-4 w-25" alt="'.$thumbs[1].'"/>';
                    echo '</figure>';
                }
                ?>
                <div class="grid-noticias news-align">
                <h4 class="fonte-dezoito font-weight-bold mb-2">
                    <a class="text-decoration-none text-dark" href="<?php echo get_the_permalink($query->ID); ?>">
                        <?php echo get_the_title(); ?>
                    </a>
                </h4>
                <?php
                //echo $this->getSubtitulo($query->ID, 'p', 'fonte-dezesseis mb-2')
                ?>
                    <?php
                        if(get_field('insira_o_subtitulo', get_the_ID()) != ''){
                            the_field('insira_o_subtitulo', get_the_ID());
                        }else if (get_field('insira_o_subtitulo', get_the_ID()) == ''){
                            echo get_the_excerpt(get_the_ID()); 
                        }
                    ?>
                
                <?php 
                    $dt_post = get_the_date('d/m/Y g\hi', get_the_ID());		
                    $categoria = get_the_category(get_the_ID())[0]->name;
            
                    echo '<p class="fonte-doze font-italic mb-0 news-date">Publicado em: '.$dt_post.' - em '.$categoria.'</p>';
                ?>

                </div>
            </article>
        </section>
    
    <?php                                
        endwhile;
    
    echo '</div>'; // container
} else {
    // no posts found
}
/* Restore original Post Data */
wp_reset_postdata();