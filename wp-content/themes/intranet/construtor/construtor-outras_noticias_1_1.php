<div class="container" id="outrasNoticias">
             
        <?php

            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $qtd = get_sub_field('quantidade');
            $categorias = get_sub_field('categoria');            
            $args = array(
                'post_type' => 'noticia',
                'posts_per_page'=> $qtd,
                'paged'=> $paged,
            );

            if($categorias){
                $args['cat'] = $categorias;
            }

            // The Query
            $the_query = new WP_Query( $args );
            
            // The Loop
            if ( $the_query->have_posts() ) {
               
                while ( $the_query->have_posts() ) :
                    $the_query->the_post();
                ?>                    
                    <div class="recado noticia">
                        <div class="row">
                            <div class="col-3 col-md-3 img-column">
                                <?php $imagem = get_thumb(get_the_ID()); ?>
                                <img src="<?= $imagem[0]; ?>" class="img-fluid rounded" alt="<?= $imagem[1]; ?>">
                            </div>
                            
                            <div class="col-9 col-md-9">

                            <p class="data"><?= getDay(get_the_date('w')); ?>, <?= get_the_date('M d') ?> às <?= get_the_date('H\hi\m\i\n') ?> 
                                <?php 
                                    $term_obj_list = get_the_terms( get_the_ID(), 'category' );
                                    $i = 0;
                                    if($term_obj_list){
                                        echo " - em ";
                                        foreach($term_obj_list as $categoria){
                                            if($i == 0){
                                                echo "<span>" . $categoria->name . "</span>";
                                                //echo "<a href='" . $urlPage . "?categoria=" . $categoria->slug . "'>" . $categoria->name . "</a>";
                                            } else {
                                                echo ", <span>" . $categoria->name . "</span>";
                                                //echo ", <a href='" . $urlPage . "?categoria=" . $categoria->slug . "'>" . $categoria->name . "</a>";
                                            }
                                            $i++;
                                        }                                        
                                    }
                                ?>
                            </p>
                                
                                
                                <h2><?= get_the_title(); ?></h2>
                                <?php
                                    $subtitulo = get_field('insira_o_subtitulo');
                                    if($subtitulo && $subtitulo != '')
                                        echo '<p>' . $subtitulo . '</p>';
                                ?>                       
                                <div class="d-flex justify-content-between">
                                    <div class="likes">

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

                                        <div class="post_like">
                                            <a class="pp_like <?php if($l==1) {echo "likes"; } ?>" id="pp_like_<?php echo get_the_id(); ?>" href="#" data-id="<?php echo get_the_id(); ?>"><i class="fa fa-heart" aria-hidden="true"></i></i> <span><?php echo $total_like1; ?> <?php echo $total_like1 == 1 ? 'like' : 'likes'; ?></span></a>	
                                        </div>
                                        
                                    </div>
                                    <div class="link">
                                        <a href="<?= get_the_permalink(); ?>">Ver mais</a>
                                    </div>
                                </div>

                            </div>
                        </div>

                        
                    </div>
                <?php
                    
                endwhile;
                
                ?>
                <div class="container mt-4">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pagination-prog text-center">
                                <?php wp_pagenavi( array( 'query' => $the_query ) ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            
                <?php wp_reset_postdata();

            } else {
                // no posts found
            }
            
        ?>
    
</div>