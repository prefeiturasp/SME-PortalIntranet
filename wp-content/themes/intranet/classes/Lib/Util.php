<?php

namespace Classes\Lib;


use Classes\TemplateHierarchy\ArchiveContato\ArchiveContato;

class Util
{
	protected $page_id;
	protected $deseja_exibir_subtitulo;
	protected $insira_o_subtitulo;
	protected $valor_campo_personalizado;

	public function __construct($page_id){
		$this->page_id = $page_id;
		$this->page_slug = get_queried_object()->post_name;

	}

	public function montaHtmlLoopPadrao()
	{

		global $post;
		$post_slug = $post->post_name;
		
		if(get_field('fx_flex_habilitar_menu', $post->post_parent) != null){
			$parent = $post->post_parent;
		}

		//echo $post->post_parent;
		
		echo '<section class="container">';
		if (have_posts()) : while (have_posts()) : the_post();
			?>

			<?php if(get_field('fx_flex_habilitar_menu') != null || get_field('fx_flex_habilitar_menu', $parent) != null): ?>
				<article class="row">
					<div class="col-lg-12 col-xs-12">
						<h1 class="mb-4">
							<?php if($parent){
								echo get_the_title($parent);
							} else {
								the_title();
							} ?>
						</h1>
					</div>
					<div class="col-lg-4">
						<button type="button" class="btn-submenu d-lg-none d-xl-none b-0" data-toggle="modal" data-target="#filtroBusca">
							<i class="fa fa-ellipsis-v" aria-hidden="true"></i> <span>Subpáginas</span>					
						</button>

						<hr class='d-lg-none d-xl-none'>

						<!-- Modal -->
						<div class="modal left fade" id="filtroBusca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
							<div class="modal-dialog" role="document">
								<div class="modal-content">

									<div class="modal-header">
										<p class="modal-title" id="myModalLabel2">Subpáginas</p>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>				
									</div>

									<div class="modal-body">
										<ul class="nav flex-column vertical-menu-mobile">					
						
											<?php
												if($parent){
													$campos = get_field('menu_lateral_item_principal', $parent);	
												} else {
													$campos = get_field('menu_lateral_item_principal');	
												}
												

												if($campos['rotulo'] != '' && $campos['menu_lateral_principal'][0] != ''){
													$page = $campos['menu_lateral_principal'][0];
													if($page == get_the_ID()){
														$classe = 'active';
													}
													echo '<li><a href="' . get_the_permalink($page) . '" class="' . $classe . '">' . $campos['rotulo'] . '</a></li>';
												} elseif(!$campos['rotulo'] && $campos['menu_lateral_principal'][0] != ''){
													$page = $campos['menu_lateral_principal'][0];
													if($page == get_the_ID()){
														$classe = 'active';
													}
													echo '<li><a href="' . get_the_permalink($page) . '" class="' . $classe . '">' . get_the_title($page) . '</a></li>';
													$classe = '';
												}

												$outrasPages = $campos['menu_lateral_outros_itens'];
												$currentTitle = '';

												if($outrasPages){
													
													foreach($outrasPages as $page){
														if($page['nome_do_rotulo'] != ''){
															if($page['outros_pagina'][0] == get_the_ID()){
																$currentTitle = $page['nome_do_rotulo'];
																$classe = 'active';
															}
															echo '<li><a href="' . get_the_permalink($page['outros_pagina'][0]) . '" class="' . $classe . '">' . $page['nome_do_rotulo'] . '</a></li>';
															$classe = '';
														} else {
															if($page['outros_pagina'][0] == get_the_ID()){
																$currentTitle = get_the_title($page['outros_pagina'][0]);
																$classe = 'active';
															}
															echo '<li><a href="' . get_the_permalink($page['outros_pagina'][0]) . '" class="' . $classe . '">' . get_the_title($page['outros_pagina'][0]) . '</a></li>';
															$classe = '';
														}
													}
												}	

												//echo "<pre>";
												//print_r($campos);
												//echo "</pre>";
											?>
										</ul>
									</div>

								</div><!-- modal-content -->
							</div><!-- modal-dialog -->
						</div><!-- modal -->

						<ul class="nav flex-column vertical-menu d-none d-lg-block d-xl-block">					
						
							<?php
								if($parent){
									$campos = get_field('menu_lateral_item_principal', $parent);	
								} else {
									$campos = get_field('menu_lateral_item_principal');	
								}
								

								if($campos['rotulo'] != '' && $campos['menu_lateral_principal'][0] != ''){
									$page = $campos['menu_lateral_principal'][0];
									if($page == get_the_ID()){
										$classe = 'active';
									}
									echo '<li><a href="' . get_the_permalink($page) . '" class="' . $classe . '">' . $campos['rotulo'] . '</a></li>';
									$classe = '';
								} elseif(!$campos['rotulo'] && $campos['menu_lateral_principal'][0] != ''){
									$page = $campos['menu_lateral_principal'][0];
									if($page == get_the_ID()){
										$classe = 'active';
									}
									echo '<li><a href="' . get_the_permalink($page) . '" class="' . $classe . '">' . get_the_title($page) . '</a></li>';
									$classe = '';
								}

								$outrasPages = $campos['menu_lateral_outros_itens'];
								$currentTitle = '';

								if($outrasPages){
									
									foreach($outrasPages as $page){
										if($page['nome_do_rotulo'] != ''){
											if($page['outros_pagina'][0] == get_the_ID()){
												$currentTitle = $page['nome_do_rotulo'];
												$classe = 'active';
											}
											echo '<li><a href="' . get_the_permalink($page['outros_pagina'][0]) . '" class="' . $classe . '">' . $page['nome_do_rotulo'] . '</a></li>';
											$classe = '';
										} else {
											if($page['outros_pagina'][0] == get_the_ID()){
												$currentTitle = get_the_title($page['outros_pagina'][0]);
												$classe = 'active';
											}
											echo '<li><a href="' . get_the_permalink($page['outros_pagina'][0]) . '" class="' . $classe . '">' . get_the_title($page['outros_pagina'][0]) . '</a></li>';
											$classe = '';
										}
									}
								}
								
								if($campos['rotulo'] != '' && $campos['menu_lateral_principal'][0] == get_the_ID()){
									$currentTitle = $campos['rotulo'];
								} elseif(!$campos['rotulo'] && $campos['menu_lateral_principal'][0] == get_the_ID()) {
									$currentTitle = get_the_title();
								} else {
									$currentTitle = get_the_title();
								}
								
							?>
						</ul>
					</div>

					<div class="col-lg-8">
						<?php if($currentTitle): ?>
							<h2 class="submenu-title"><?php echo $currentTitle; ?></h2>
						<?php endif; ?>

						<div class="my-3" id="conteudo">
							<?php the_content(); ?>
						</div>						
					</div>
				</article>
			<?php else: ?>
				<article class="row">
					<article class="col-lg-12 col-xs-12">
						<h1 class="mb-4" id="<?= $this->page_slug ?>"><?php the_title(); ?></h1>
					</article>
				</article>


				<article class="row" id="conteudo">
					<article class="col-lg-9 col-xs-12">
						<?php echo $this->getSubtitulo($this->page_id)?>
						<?php the_content(); ?>
					</article>
				</article>
			<?php endif; ?>
		<?php
		endwhile;
		endif;
		wp_reset_query();
		echo '</section>'; //container
	}

	public function montaHtmlLoopPadraoCard()
	{
		echo '<section class="container">';

		if (have_posts()) : while (have_posts()) : the_post();
			?>
            <article class="row">
                <article class="col-lg-12 col-xs-12">
                    <h1 class="mb-4" id="<?= $this->page_slug ?>"><?php the_title(); ?></h1>
                    <p>Atualizado em: <?php the_modified_date('d/m/Y'); ?></p>
                </article>
            </article>

            <article class="row">
                <article class="col-lg-12 col-xs-12 mb-5">
					<?php echo $this->getSubtitulo($this->page_id)?>
					<?php the_content(); ?>
                </article>
            </article>

		<?php
		endwhile;
		endif;
		wp_reset_query();
		echo '</section>'; //container
	}

	public function getSubtitulo($page_id, $tag_html = 'h2', $tag_css=null){
		//echo get_field('deseja_exibir_subtitulo', $page_id);
		$this->deseja_exibir_subtitulo = get_field('deseja_exibir_subtitulo', $page_id);
		$this->insira_o_subtitulo = get_field('insira_o_subtitulo', $page_id);

		if ($this->deseja_exibir_subtitulo == 'sim' && trim($this->insira_o_subtitulo != '')){
			return '<'.$tag_html.' class="mb-3 '.$tag_css.'">'.$this->insira_o_subtitulo.'</'.$tag_html.'>';
		}
		
		/*if($this->insira_o_subtitulo != ''){
			return '<'.$tag_html.' class="mb-3 '.$tag_css.'">'.$this->insira_o_subtitulo.'</'.$tag_html.'>';
		}*/
		
		if($this->deseja_exibir_subtitulo == 'nao'){
			return false;
		}

	}

	public function getCamposPersonalizados($nome_do_campo){
		$this->valor_campo_personalizado = get_field($nome_do_campo, $this->page_id);

		return $this->valor_campo_personalizado;

	}

	public function abreContainer(array $tags, array $css){

		foreach ($tags as $index => $tag){
			$array_tags[] = $tag.'_'.$index;
		}

		foreach ($css as $classe){
			$array_css[] = $classe;
		}

		$array_tags_e_css = array_combine($array_tags, $array_css);

		foreach ($array_tags_e_css as $index => $valor){
			$posicao = strpos($index, "_");
			$tag = substr($index,0,$posicao);

			echo '<'.$tag.' class="'.$valor.'" id="conteudo">';
		}
	}

	public function fechaContainer($tags){
		foreach ($tags as $index => $tag){
			echo '</'.$tag.'>';
		}

	}

	public function montaHtmlBotaoMaisNoticias(){
		?>

        <section class="row">
            <article class="col-lg-12 col-xs-12 container-btn-mais-noticias">
                <form>
                    <fieldset>
                        <legend>Ir para notícias</legend>
                        <a href="<?= STM_URL.'/noticias' ?>" class="btn btn-primary btn-sm btn-block bg-azul-escuro font-weight-bold text-white">Notícias</a>
                    </fieldset>
                </form>
            </article>
        </section>

		<?php
	}




	public static function randString($size){
		//Essa função gera um valor de String aleatório do tamanho recebendo por parametros
		//String com valor possíveis do resultado, os caracteres pode ser adicionado ou retirados conforme sua necessidade
		$basic = 'abcdefghijklmnopqrstuvwxyz0123456789';

		$return= "";

		for($count= 0; $size > $count; $count++){
			//Gera um caracter aleatorio
			$return.= $basic[rand(0, strlen($basic) - 1)];
		}

		return $return;
	}

}