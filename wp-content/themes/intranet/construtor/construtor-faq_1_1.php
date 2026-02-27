<?php 
    $chamada = get_sub_field('chamada');
    $ordenacao = get_sub_field('ordenacao');
    
?>

<div class="container">
    
    <div class="row">
        <div class="col-12">
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
        </div>
    </div>

    <?php if($chamada): ?>
        <div class="row">
            <div class="col-12 chamada-faq mb-4">
                <h2><?= $chamada; ?></h2>
            </div>
        </div>
    <?php endif; ?>


    <div class="accordion faq-accord" id="accordionFaq">                    

        <?php
                    
        // the query
        $args = array(
            'post_type' => 'intranet-faq',
            'posts_per_page' => -1, //get_sub_field('quantidade'),       
        );        

        if($ordenacao == 'date'){
            $args['order'] = 'DESC';
            $args['orderby'] = 'date';
        } else {
            $args['order'] = 'ASC';
            $args['orderby'] = 'post_title';
        }

        if($_GET['busca'] && $_GET['busca'] != '')
            $args['s'] = $_GET['busca']; 

        $the_query = new WP_Query( $args ); ?>
        
        <?php if ( $the_query->have_posts() ) : ?>
        
            <!-- pagination here -->
        
            <!-- the loop -->
            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

            <div class="card">
                <div class="card-header" id="heading<?= get_the_ID(); ?>">
                    <h2 class="mb-0 d-flex justify-content-between">
                        <button class="btn btn-link text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse<?= get_the_ID(); ?>" aria-expanded="false" aria-controls="collapse<?= get_the_ID(); ?>">
                            <?= get_the_title(); ?>
                        </button>

                        <button class="btn btn-chevron btn-link text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse<?= get_the_ID(); ?>" aria-expanded="false" aria-controls="collapse<?= get_the_ID(); ?>">
                            <i class="fa fa-chevron-up" aria-hidden="true"></i>
                        </button>
                    </h2>
                </div>

                <div id="collapse<?= get_the_ID(); ?>" class="collapse" aria-labelledby="heading<?= get_the_ID(); ?>" data-parent="#accordionFaq">
                    <div class="card-body">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>
            <!-- end of the loop -->
        
            <?php wp_reset_postdata(); ?>
        
        <?php else : ?>
            <p><?php _e( 'Não há nenhuma Dúvida Frequente encontrada.' ); ?></p>
        <?php endif; ?>
        
    </div>
</div>