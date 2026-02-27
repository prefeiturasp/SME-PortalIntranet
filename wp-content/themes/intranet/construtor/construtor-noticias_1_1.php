<div class="container">
    <div class="row">
        <div class="col-12">

            <?php
            global $has_posts;
            $urlPage = get_the_permalink();
            $paged = 1;
            if ( get_query_var('paged') ) $paged = get_query_var('paged');
            if ( get_query_var('page') ) $paged = get_query_var('page');
            
            $sticky = get_option( 'sticky_posts' );
            $categorias = get_sub_field('fx_noticias_1_1');
                                               
            $args_for_query1 = array(
                'post_type' => 'noticia',
                'fields' => 'ids',
                'posts_per_page' => -1,
                'paged' => $paged,                
                'post__in'  => $sticky,     
            );

            if (!isset($args_for_query1['tax_query'])) {
                $args_for_query1['tax_query'] = array();
            }

            // Filtro por CATEGORIA (se existir)
            if ($categorias) {
                $args_for_query1['tax_query'][] = array(
                    'taxonomy' => 'categorias-noticias',
                    'field'    => 'term_id',
                    'terms'    => $categorias,
                );
            }

            // Filtro por TAG (se passado via $_GET)
            if (isset($_GET['local']) && !empty($_GET['local'])) {
                $tag_id = intval($_GET['local']); 
                
                $args_for_query1['tax_query'][] = array(
                    'taxonomy' => 'post_tag',
                    'field'    => 'term_id',
                    'terms'    => $tag_id,
                );
            }
            
            if (count($args_for_query1['tax_query']) > 1) {
                $args_for_query1['tax_query']['relation'] = 'AND';
            }

            $args_for_query2 = array(
                'post_type' => 'noticia',
                'fields' => 'ids',
                'posts_per_page' => -1,
                'paged' => $paged,
                'post__not_in' => $sticky,   
            );

            if (!isset($args_for_query2['tax_query'])) {
                $args_for_query2['tax_query'] = array();
            }

            // Filtro por CATEGORIA (se existir)
            if ($categorias) {
                $args_for_query2['tax_query'][] = array(
                    'taxonomy' => 'categorias-noticias',
                    'field'    => 'term_id',
                    'terms'    => $categorias,
                );
            }
            
            if (count($args_for_query2['tax_query']) > 1) {
                $args_for_query2['tax_query']['relation'] = 'AND';
            }

            //setup your queries as you already do
            $query1 = new WP_Query($args_for_query1);
            $query2 = new WP_Query($args_for_query2);

            $allTheIDs = array_merge($query1->posts,$query2->posts);

            //create new empty query and populate it with the other two
            $the_query = new WP_Query(array(
                'post_type' => 'noticia',
                'post__in' => $allTheIDs,
                'posts_per_page' => get_sub_field('qtd'),
                'paged' => $paged,
                'orderby' => 'post__in',
                'ignore_sticky_posts' => 1
            ));
            
            ?>
           

                <?php if ( $the_query->have_posts() ) : ?>
                    <?php $has_posts = true; ?>
                    <!-- pagination here -->
                    <div class="row">
                        <!-- the loop -->
                        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                        
                            <div class="col-12 col-md-4 mb-4">
                                <div class="mural sme-informe p-0 d-flex">
                                    <div class="row m-0">
                                        <div class="col-12 img-column mb-3 p-0">
                                            <?php 
                                                $image = get_the_post_thumbnail( $post_id, 'default-image', array( 'class' => 'img-fluid' ) );
                                            ?>
                                            <?php if($image): ?>
                                                <?= $image; ?>
                                            <?php else: ?>
                                                <img src="<?= get_template_directory_uri(); ?>/img/categ-destaques.jpg" class="img-fluid rounded" alt="Imagem de ilustração categoria">
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-12">
                                        <p class="data">
                                            <?php
                                                if(get_field('insira_o_subtitulo', get_the_ID()) != ''){
                                                    the_field('insira_o_subtitulo', get_the_ID());
                                                }
                                            ?>
                                        </p>
                                        </div>

                                        <div class="col-12 col-md-9 mb-2">                                        
                                            <h2><a href="<?= get_the_permalink(); ?>"><?= get_the_title(); ?></a></h2>                                                            
                                        </div>

                                        <div class="col-12 col-md-3 mb-2">
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
                                                    <a class="pp_like <?php if($l==1) {echo "likes"; } ?>" id="pp_like_<?php echo get_the_id(); ?>" href="#" data-id="<?php echo get_the_id(); ?>"><span><?php echo $total_like1; ?> <?php echo $total_like1 == 1 ? 'like' : 'likes'; ?></span><br><i class="fa fa-heart" aria-hidden="true"></i></a>	
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                    
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <!-- end of the loop -->
                    </div>
                
                    <div class="container mt-4">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="pagination-prog text-center">
                                    <?php wp_pagenavi( array( 'query' => $the_query ) ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <?php wp_reset_postdata(); ?>
                
                <?php else : ?>
                    <div class="no-results">
                        <h2 class="search-title">
                            <span class="azul-claro-acervo"><strong>0</strong></span><strong> 
                                resultados</strong>
                        </h2>
                        <img src="https://educacao.sme.prefeitura.sp.gov.br/wp-content/themes/sme-portal-institucional/img/search-empty.png" alt="Imagem ilustrativa para nenhum resultado de busca encontrado" class="img-fluid">
                        <p>Nenhuma notícia encontrada</p>
                    </div>
                <?php endif; ?>
            
            
        </div>
    </div>
</div>