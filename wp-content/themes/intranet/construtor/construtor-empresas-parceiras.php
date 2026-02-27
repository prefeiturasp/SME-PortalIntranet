<?php 
    $page_id = get_the_ID();
    $categoriasDest = get_terms('categorias-parceiros', array('hide_empty' => '1'));
    $countBusca = 0;

    if($_GET['busca'] && $_GET['busca'] != '')
        $countBusca++;                

    if($_GET['categoria'] && $_GET['categoria'] != '')
        $countBusca++;
        
    
?>
<div class="container">

    <form class="form-recados d-none d-md-flex">
        

            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="busca">Filtrar por nome</label>
                    <input type="text" value="<?= $_GET['busca']; ?>" class="form-control" id="busca" name="busca" placeholder="Busque por nome da livraria">
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="categoria">Filtrar por bairro</label>
                    <select class="form-control" id="categoria" name="categoria">
                        <option value="" disabled selected>Selecione um bairro</option>
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

            <div class="col-12">
                <div class="form-group d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-primary mr-3" id="limpar" onclick="window.location.href='<?= get_the_permalink($page_id); ?>'">Limpar filtros</button>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>

        
    </form>

    

        <div class="col-12 d-md-none">
            <button type="button" class="btn btn-outline-primary btn-avanc-f btn-avanc btn-avanc-m mb-4" data-toggle="modal" data-target="#filtroBusca">
                <i class="fa fa-filter" aria-hidden="true"></i> Filtrar 
                <?php if($countBusca > 0): ?>
                    <span class="badge badge-primary"><?php echo $countBusca; ?></span>
                <?php endif; ?>
            </button>
        </div>

    

    <div class="row">
        

        <?php

        $paged = 1;
        if ( get_query_var('paged') ) $paged = get_query_var('paged');
        if ( get_query_var('page') ) $paged = get_query_var('page');
        
        // the query
        $args = array(
            'post_type' => 'parceiros',
            'posts_per_page' => get_sub_field('quantidade'),
            'paged' => $paged,                
        );

        $order = get_sub_field('ordenacao');

        if($order == 'title'){
            $args['order'] = 'ASC';
            $args['orderby'] = 'title';
        } else {
            $args['order'] = 'DESC';
            $args['orderby'] = 'date';
        }

        if($_GET['busca'] && $_GET['busca'] != '')
            $args['s'] = $_GET['busca'];                     

        if($_GET['categoria'] && $_GET['categoria'] != '')
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'categorias-parceiros',
                    'field' => 'term_id',
                    'terms' => $_GET['categoria'],
                )
            );

        $the_query = new WP_Query( $args ); ?>
        
        <?php if ( $the_query->have_posts() ) : ?>
        
            <!-- pagination here -->
        
            <!-- the loop -->
            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                <?php $categorias = get_the_terms(get_the_ID(), 'categorias-parceiros'); ?>
                
                <div class="col-12 col-md-<?=get_sub_field('colunas'); ?> mb-4">
                    <div class="recado parceiro">
                        <?php
                            $endereco = get_field('endereco');
                            $bairro = get_term( $endereco['bairro'] );
                            $contatos = get_field('contatos');                       
                        ?>
                        <?php if($contatos['link_do_site']): ?>                                                   
                            <h2><a href="<?= $contatos['link_do_site']; ?>"><?= get_the_title(); ?></a></h2>
                        <?php else: ?>
                            <h2><?= get_the_title(); ?></h2>
                        <?php endif; ?>
                        <hr>                        
                        <p>
                            <?= $endereco['logradouro'] ?>, <?= $endereco['numero'] ?><br>
                            <?= $bairro->name; ?> - <?= $endereco['cidade'] ?> - <?= $endereco['estado'] ?>
                        </p>                        
                        <?php if($contatos['telefone']): ?>
                            <p class="mb-0">Tel. <?= $contatos['telefone']; ?></p>
                        <?php endif; ?>
                        <?php if($contatos['link_do_site']): ?>
                            <p class="mb-0"><a href="<?= $contatos['link_do_site']; ?>" target="_blank"><?= $contatos['link_do_site']; ?></a></p>
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
            <p><?php _e( 'Não há nenhum parceiro cadastrado.' ); ?></p>
        <?php endif; ?>
        
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