<?php 
    $page_id = get_the_ID();
    $categoriasDest = get_terms('categorias-destaque', array('hide_empty' => '1'));
    $countBusca = 0;

    if($_GET['busca'] && $_GET['busca'] != '')
        $countBusca++;                

    if($_GET['categoria'] && $_GET['categoria'] != '')
        $countBusca++;
        
    if($_GET['tag'] && $_GET['tag'] != '')
        $countBusca++;

    if($_GET['date-ini'] && $_GET['date-ini'] != '')
        $countBusca++;

    if($_GET['date-end'] && $_GET['date-end'] != '')
        $countBusca++;
?>
<div class="container">

    <form class="form-recados d-none d-md-block">
        <div class="row">

            <div class="col-12">
                <div class="form-group">
                    <label for="busca">Filtrar por termo</label>
                    <input type="text" value="<?= $_GET['busca']; ?>" class="form-control" id="busca" name="busca" placeholder="Busque por título ou palavra-chave">
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="categoria">Filtrar por categoria de destaque</label>
                    <select class="form-control" id="categoria" name="categoria">
                        <option value="" disabled selected>Selecione uma categoria</option>
                        <?php
                            if($categoriasDest){
                                foreach($categoriasDest as $categoria){
                                    $selected = '';
                                    if($_GET['categoria'] == $categoria->term_id)
                                        $selected = 'selected';
                                    echo '<option value="' . $categoria->term_id . '" ' . $selected . '>' . $categoria->name . '</option>';
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
                    <button type="button" class="btn btn-outline-primary mr-3" id="limpar" onclick="window.location.href='<?= get_the_permalink($page_id); ?>'">Limpar filtros</button>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>

        </div>
    </form>

    <div class="row">

        <div class="col-12 d-md-none">
            <button type="button" class="btn btn-outline-primary btn-avanc-f btn-avanc btn-avanc-m mb-4" data-toggle="modal" data-target="#filtroBusca">
                <i class="fa fa-filter" aria-hidden="true"></i> Filtrar 
                <?php if($countBusca > 0): ?>
                    <span class="badge badge-primary"><?php echo $countBusca; ?></span>
                <?php endif; ?>
            </button>
        </div>

    </div>

    <div class="row">
        <div class="col-12">

            <?php

            $paged = 1;
            if ( get_query_var('paged') ) $paged = get_query_var('paged');
            if ( get_query_var('page') ) $paged = get_query_var('page');
            
            // the query
            $args = array(
                'post_type' => 'destaque',
                'posts_per_page' => get_sub_field('quantidade'),
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
                        'taxonomy' => 'categorias-destaque',
                        'field' => 'term_id',
                        'terms' => $_GET['categoria'],
                    )
                );
                
            if($_GET['tag'] && $_GET['tag'] != '')
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'tags-destaque',
                        'field' => 'term_id',
                        'terms' => $_GET['tag'],
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
                    <div class="recado">
                        <div class="row">
                            <div class="col-3 col-md-2 img-column">
                                <?php 
                                    if($categorias)
                                        $image = get_field('imagem_principal', 'categorias-destaque_' . $categorias[0]->term_id);
                                        $i = 0;
                                ?>
                                <?php if($image): ?>
                                    <img src="<?= $image['url']; ?>" class="img-fluid rounded d-none d-sm-none d-md-block" alt="Imagem de ilustração categoria">
                                    <img src="<?= $image['sizes']['thumbnail']; ?>" class="img-fluid rounded d-md-none" alt="Imagem de ilustração categoria">
                                <?php else: ?>
                                    <img src="<?= get_template_directory_uri(); ?>/img/categ-destaques.jpg" class="img-fluid rounded" alt="Imagem de ilustração categoria">
                                <?php endif; ?>
                            </div>
                            <?php 
                                //echo "<pre>";
                                //print_r($image);
                                //echo "</pre>";
                            ?>
                            <div class="col-9 col-md-10">

                                <p class="data"><?= getDay(get_the_date('w')); ?>, <?= get_the_date('M d') ?> às <?= get_the_date('H\hi\m\i\n') ?></p>
                                
                                <?php if($tags): ?>
                                    <div class="tags-recados">
                                        <?php 
                                            foreach($tags as $tag){
                                                $cor = get_field('cor_principal', 'tags-destaque_' . $tag->term_id);
                                                echo '<a href="' . get_the_permalink($page_id) . '?tag=' . $tag->term_id . '" style="background: ' . $cor . '">' . firstLetter($tag->name) . '</a> ';
                                            }
                                        ?>
                                    </div>
                                <?php endif; ?>

                                
                                <h2><?= get_the_title(); ?></h2>
                                <?php
                                    $subtitulo = get_field('insira_o_subtitulo');
                                    if($subtitulo && $subtitulo != '')
                                        echo '<p>' . $subtitulo . '</p>';
                                ?> 
                                
                                <?php if($categorias): ?>
                                    <p class="categs">
                                        <?php 
                                            foreach($categorias as $term){
                                                if($i == 0){
                                                    echo '<a href="' . get_the_permalink($page_id) . 'categoria=' . $term->term_id . '">' . $term->name . '</a>';
                                                } else {
                                                    echo ', <a href="' . get_the_permalink($page_id) . 'categoria=' . $term->term_id . '">' . $term->name . '</a>';
                                                }
                                                $i++;
                                            }                                        
                                        ?>
                                    </p>
                                <?php endif; ?>
                                                              
                                <hr>
                                <a class="btn-collapse collapsed" data-toggle="collapse" href="#collapse<?= get_the_ID(); ?>" role="button" aria-expanded="false" aria-controls="collapse<?= get_the_ID(); ?>">
                                    <span class="button-more">ver mais <i class="fa fa-chevron-down" aria-hidden="true"></i></span><span class="button-less">ver menos <i class="fa fa-chevron-up" aria-hidden="true"></i></span>
                                </a>                        
                            </div>
                        </div>

                        <div class="collapse" id="collapse<?= get_the_ID(); ?>">
                            <div class="recado-content">
                                <?php the_content(); ?>
                                <?php if( get_field('insira_o_link') ): ?>
                                    <p class="link-externo"><a href="<?= get_field('insira_o_link'); ?>">Ver link externo</a></p>
                                <?php endif; ?>
                            </div>
                            <?php if(get_field('url_do_video')): ?>
                                <div class="recado-video">
                                    <div class="embed-container">
                                        <?php the_field('url_do_video'); ?>
                                    </div>                                    
                                </div>
                            <?php endif; ?>

                            <?php if(get_field('selecione_imagem')): ?>
                                <div class="recado-video">                                    
                                    <?php $imagem = get_field('selecione_imagem'); ?>
                                    <img src="<?= $imagem['url']; ?>" alt="<?= $imagem['alt']; ?>">
                                </div>
                            <?php endif; ?>
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

<!-- Modal -->
<div class="modal right fade" id="filtroBusca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<p class="modal-title" id="myModalLabel2">Filtrar por:</p>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>				
			</div>

			<div class="modal-body">
				<div class="acord-busca my-2">
					<form method="get" class="text-left" action="<?= get_the_permalink(); ?>">
						
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="busca">Filtrar por termo</label>
                                    <input type="text" value="<?= $_GET['busca']; ?>" class="form-control" id="busca" name="busca" placeholder="Busque por título ou palavra-chave">
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                
                                <div class="form-group">
                                    <label for="categoria">Filtrar por categoria de destaque</label>
                                    <select class="form-control" id="categoria" name="categoria">
                                        <option value="" disabled selected>Selecione uma categoria</option>
                                        <?php
                                            
                                            if($categoriasDest){
                                                foreach($categoriasDest as $categoria){
                                                    $selected = '';
                                                    if($_GET['categoria'] == $categoria->term_id)
                                                        $selected = 'selected';
                                                    echo '<option value="' . $categoria->term_id . '" ' . $selected . '>' . $categoria->name . '</option>';
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
                                    <label for="data-end">até</label>
                                    <input type="date" id="data-end" name="date-end" value="<?= $_GET['date-end']; ?>" max="<?= date("Y-m-d"); ?>">
                                </div>
                            </div>

                            <div class="col-12 btn-filtro">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-primary mr-3" id="limpar" onclick="window.location.href='<?= get_the_permalink($page_id); ?>'">Limpar filtros</button>
                                    <button type="submit" class="btn btn-primary" id="filtrar">Filtrar</button>
                                </div>
                            </div>

                        </div>

					</form>
				</div>	
			</div>

		</div><!-- modal-content -->
	</div><!-- modal-dialog -->
</div><!-- modal -->