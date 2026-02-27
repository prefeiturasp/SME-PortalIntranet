<?php
    // Quantidade por pagina
    $qtd = get_sub_field('quantidade'); // link
    if(!$qtd && $qtd != ''){
        $qtd = 10;
    }

    // Categorias
    $args = array(
               'taxonomy' => 'categorias-explica',
               'orderby' => 'name',
               'order'   => 'ASC'
           );

   $categorias = get_categories($args);
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <form class="form-recados" action="<?= get_the_permalink(); ?>">
                <div class="row">

                    <div class="col-12">
                        <div class="form-group">
                            <label for="busca">Filtrar por termo</label>
                            <input type="text" value="<?= $_GET['busca']; ?>" class="form-control" id="busca" name="busca" placeholder="Busque pelo título ou palavra-chave">
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="categoria">Filtrar por categoria</label>
                            <select class="form-control" id="categoria" name="categoria">
                                <option value="" selected>Selecione uma categoria</option>
                                <?php
                                    if($categorias){
                                        foreach($categorias as $categoria){
                                            $selected = '';
                                            if($_GET['categoria'] == $categoria->slug)
                                                $selected = 'selected';
                                            echo '<option value="' . $categoria->slug . '" ' . $selected . '>' . $categoria->name . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label for="data-ini">Filtrar por intervalo de datas</label>
                            <input type="date" id="data-ini" name="date-ini" value="<?= $_GET['date-ini']; ?>" max="<?= date("Y-m-d"); ?>">
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label for="data-end">&nbsp;</label>
                            <input type="date" id="data-end" name="date-end" value="<?= $_GET['date-end']; ?>" max="<?= date("Y-m-d"); ?>">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group d-flex justify-content-end">
                            <input type="hidden" name="filter" value="1">
                            <button type="button" class="btn btn-outline-primary mr-3" id="limpar" onclick="window.location.href='<?= get_the_permalink($page_id); ?>'">Limpar filtros</button>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-12">

            <?php

            //global $_wp_additional_image_sizes; 
            //print '<pre>'; 
            //print_r( $_wp_additional_image_sizes ); 
            //print '</pre>'; 

            $urlPage = get_the_permalink();
            $paged = 1;
            if ( get_query_var('paged') ) $paged = get_query_var('paged');
            if ( get_query_var('page') ) $paged = get_query_var('page');
            
            // the query
            $args = array(
                'post_type' => 'info-sme-explica',
                'posts_per_page' => $qtd,
                'paged' => $paged,
                'date_query' => array(
                    array(
                        'after'     => $_GET['date-ini'],
                        'before'    => $_GET['date-end'],
                        'inclusive' => true,
                    ),
                ),
            );

            if($_GET['busca'] && $_GET['busca'] != '')
                $args['s'] = $_GET['busca'];                     

            if($_GET['categoria'] && $_GET['categoria'] != '')
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'categorias-explica',
                        'field' => 'slug',
                        'terms' => $_GET['categoria'],
                    )
                );
            
            
            $the_query = new WP_Query( $args ); ?>
            
            <?php if ( $the_query->have_posts() ) : ?>
            
                <!-- pagination here -->
            
                <!-- the loop -->
                <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                    <?php 
                        $categorias = get_the_terms(get_the_ID(), 'categorias-destaque');
                        $tags = get_the_terms(get_the_ID(), 'tags-destaque');
                    ?>
                    <div class="recado sme-informe">
                        <div class="row">
                            <div class="col-12 col-md-3 img-column mb-3">
                                <?php 
                                    $image = get_the_post_thumbnail( $post_id, 'medium', array( 'class' => 'img-fluid rounded' ) );
                                ?>
                                <?php if($image): ?>
                                    <?= $image; ?>
                                <?php else: ?>
                                    <img src="<?= get_template_directory_uri(); ?>/img/categ-destaques.jpg" class="img-fluid rounded" alt="Imagem de ilustração categoria">
                                <?php endif; ?>
                            </div>
                            <?php 
                                //echo "<pre>";
                                //print_r($image);
                                //echo "</pre>";
                            ?>
                            <div class="col-12 col-md-9">

                                <p class="data"><?= getDay(get_the_date('w')); ?>, <?= get_the_date('M d') ?> às <?= get_the_date('H\hi\m\i\n') ?> 
                                <?php 
                                    $term_obj_list = get_the_terms( get_the_ID(), 'categorias-explica' );
                                    $i = 0;
                                    if($term_obj_list){
                                        echo " - em ";
                                        foreach($term_obj_list as $categoria){
                                            if($i == 0){
                                                echo "<a href='" . $urlPage . "?categoria=" . $categoria->slug . "'>" . $categoria->name . "</a>";
                                            } else {
                                                echo ", <a href='" . $urlPage . "?categoria=" . $categoria->slug . "'>" . $categoria->name . "</a>";
                                            }
                                            $i++;
                                        }                                        
                                    }
                                ?>
                                </p>
                                
                                <h2><a href="<?= get_the_permalink(); ?>"><?= get_the_title(); ?></a></h2>
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
                <?php endwhile; ?>
                <!-- end of the loop -->
            
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
                <p><?php _e( 'Não há nenhuma publicação encontrada.' ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>