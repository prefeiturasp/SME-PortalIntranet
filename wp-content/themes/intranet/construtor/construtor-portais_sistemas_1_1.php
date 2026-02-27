<?php 
    $page_id = get_the_ID();
    $categorias = get_sub_field('categorias');
    $ativarCategorias = get_sub_field('ativar_categorias');
?>

<div class="container">

    <form class="form-recados">
        <div class="row">

            <div class="col-12">
                <div class="form-group">
                    <label for="busca">Filtrar por termo</label>
                    <input type="text" value="<?= $_GET['busca']; ?>" class="form-control" id="busca" name="busca" placeholder="Busque por título ou palavra-chave">
                </div>
            </div>

            <div class="col-12">
                <div class="form-group d-flex justify-content-end">
                    <input type="hidden" name="filter" value="1">
                    <button type="button" class="btn btn-outline-primary mr-md-3" id="limpar" onclick="window.location.href='<?= get_the_permalink($page_id); ?>'">Limpar filtros</button>
                    <button type="submit" class="btn btn-primary" id="filtrar">Filtrar</button>
                </div>
            </div>

        </div>
    </form>
    
    <?php if($ativarCategorias): ?>
        <?php
            //echo "<pre>";
            //print_r($categorias);
            //echo "</pre>";
        ?>
        <?php if($categorias): ?>
            <?php foreach($categorias as $categoria): ?>
                <div class="lista-portais">
                    <div class="row">
                        <div class="col-12">
                            <h2><?= $categoria->name; ?></h2>
                        </div>                    

                        <?php                    
                        
                        // the query
                        $args = array(
                            'post_type' => 'portais',
                            'posts_per_page' => -1, //get_sub_field('quantidade'),
                            'paged' => $paged,                
                        );

                        if($_GET['busca'] && $_GET['busca'] != '')
                            $args['s'] = $_GET['busca'];                     

                        
                        $args['tax_query'] = array(
                            array(
                                'taxonomy' => 'categorias-portais',
                                'field' => 'term_id',
                                'terms' => $categoria->term_id,
                            )
                        );

                        $the_query = new WP_Query( $args ); ?>
                        
                        <?php if ( $the_query->have_posts() ) : ?>
                        
                            <!-- pagination here -->
                        
                            <!-- the loop -->
                            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                                <?php 
                                    $categorias = get_the_terms(get_the_ID(), 'categorias-destaque');
                                ?>
                                <div class="col-md-4">
                                    <div class="portal">
                                        <?php
                                            $imagem = get_field('imagem_destacada');
                                            $imagemPadrao = get_template_directory_uri() . '/img/categ-portais.jpg';
                                            if($imagem['sizes']['admin-list-thumb'])
                                                $imagemPadrao = $imagem['sizes']['admin-list-thumb'];
                                        ?>

                                        <a href="<?= get_field('insira_link'); ?>" target="_blank"><img src="<?= $imagemPadrao; ?>" alt="" srcset=""></a>
                                        <h3><a href="<?= get_field('insira_link'); ?>" target="_blank"><?= get_the_title(); ?></a></h3>
                                        <hr>
                                        <?php the_content(); ?>                            
                                    </div>
                                </div>

                            <?php endwhile; ?>
                            <!-- end of the loop -->
                        
                            <?php wp_reset_postdata(); ?>
                        
                        <?php else : ?>
                            <div class="col-12">
                                <p><?php _e( 'Não há nenhum Portal ou Sistema na categoria ' . $categoria->name . '.' ); ?></p>
                            </div>                            
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; // categorias ?>
    <?php else: ?>
        <div class="lista-portais">
            <div class="row">            

                <?php

                $paged = 1;
                if ( get_query_var('paged') ) $paged = get_query_var('paged');
                if ( get_query_var('page') ) $paged = get_query_var('page');
                
                // the query
                $args = array(
                    'post_type' => 'portais',
                    'posts_per_page' => -1, //get_sub_field('quantidade'),
                    'paged' => $paged,                
                );

                if($_GET['busca'] && $_GET['busca'] != '')
                    $args['s'] = $_GET['busca']; 

                $the_query = new WP_Query( $args ); ?>
                
                <?php if ( $the_query->have_posts() ) : ?>
                
                    <!-- pagination here -->
                
                    <!-- the loop -->
                    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                        <?php 
                            $categorias = get_the_terms(get_the_ID(), 'categorias-destaque');
                        ?>
                        <div class="col-md-4">
                            <div class="portal">
                                <?php
                                    $imagem = get_field('imagem_destacada');
                                    $imagemPadrao = get_template_directory_uri() . '/img/categ-portais.jpg';
                                    if($imagem['sizes']['admin-list-thumb'])
                                        $imagemPadrao = $imagem['sizes']['admin-list-thumb'];
                                ?>

                                <a href="<?= get_field('insira_link'); ?>"><img src="<?= $imagemPadrao; ?>" alt="" srcset=""></a>
                                <h3><a href="<?= get_field('insira_link'); ?>"><?= get_the_title(); ?></a></h3>
                                <hr>
                                <?php the_content(); ?>                            
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
    <?php endif; ?>
</div>