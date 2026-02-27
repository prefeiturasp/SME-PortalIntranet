<div id="outrasNoticias">
    
        
        <div class="row w-100 my-4">
            <div class="col-sm-12">
                <p class="outrasTitle">
                    Outras notícias
                </p>
            </div>
        </div>
        
        <?php

            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

            $qtd = get_sub_field('quantidade');
            $args = array(
                'post_type' => 'noticia',
                'posts_per_page'=> $qtd,
                'paged'=> $paged,
            );

            // The Query
            $the_query = new WP_Query( $args );
            
            // The Loop
            if ( $the_query->have_posts() ) {
               
                while ( $the_query->have_posts() ) :
                    $the_query->the_post();
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
                

                $published_posts = wp_count_posts()->publish;
                $posts_per_page = 5;
                $page_number_max = ceil($published_posts / $posts_per_page);
                $pages = paginate_links( [
                        'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                        'format'       => '?paged=%#%',
                        'current'      => max( 1, get_query_var( 'paged' ) ),
                        'total'        => $page_number_max,
                        'type'         => 'array',
                        'show_all'     => false,
                        'end_size'     => 0,
                        'mid_size'     => 2,
                        'prev_next'    => true,
                        'prev_text'    => __( '«' ),
                        'next_text'    => __( '»' ),
                        'add_args'     => false,
                        'add_fragment' => '#outrasNoticias'
                    ]
                );

                echo '<div class="row w-100">';
                echo '<div class="col-sm-12">';
                $pagination = '<div class="pag-noticias"><ul class="pag-noticias-ul">';

                foreach ($pages as $page) {
                    $pagination .= '<li class="pag-noticias-li page-item' . (strpos($page, 'current') !== false ? ' active' : '') . '"> ' . str_replace('page-numbers', 'space-noticia page-link', $page) . '</li>';
                }

                $pagination .= '</ul></div>';			
				echo $pagination;
                echo '</div>';
                echo '</div>';

            } else {
                // no posts found
            }
            /* Restore original Post Data */
            wp_reset_postdata();
        ?>
    
</div>