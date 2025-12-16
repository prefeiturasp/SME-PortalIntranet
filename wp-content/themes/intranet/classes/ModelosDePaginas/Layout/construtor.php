<?php

namespace Classes\ModelosDePaginas\Layout;

use Classes\Lib\Util;

function clean($string) {
	$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.										 
	return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

class Construtor extends Util
{
	
	public function __construct()
	{
		$this->ExecutaJquery();
		$this->montaHtmlConstrutor();

	}
	
	public function ExecutaJquery(){
		?>
		<script>
			jQuery(document).ready(function(){
				jQuery("table").addClass('table-responsive'); //ativa tabela responsiva
				jQuery(".nav-tabs li:first-child a").addClass('active'); //ativa a primeira aba
				jQuery(".tab-content div:first-child").addClass('active'); //ativa a primeira aba
				//jQuery("#collapse1").addClass('show'); //ativa sanfona 1
				//jQuery("#collapsea1").addClass('show'); //ativa sanfona 2
				//jQuery("#collapseb1").addClass('show'); //ativa sanfona 3
				jQuery(".card-header").on('click', function(){
					jQuery(".card-header").removeClass('bg-sanfona-active');
					jQuery(this).addClass('bg-sanfona-active');

					if(jQuery(this).find('.card-link').hasClass('collapsed')){
						jQuery(window).scrollTop( jQuery(this).offset().top );						
					}
				});

				
			});
		</script>
		<?php
	}

	public function montaHtmlConstrutor(){
		global $post;
		$post_slug = $post->post_name;
		
		if(get_field('fx_flex_habilitar_menu', $post->post_parent) != null){
			$parent = $post->post_parent;
		}
		if(get_field('fx_flex_habilitar_menu') != null || get_field('fx_flex_habilitar_menu', $parent) != null){
			$container = 'container';
		} else {
			$container = 'container-fluid p-0';
		}
		//echo $post->post_parent;
		?>
<div id="<?php echo $post_slug; ?>" class="<?php echo $container; ?>">
	
	<div class="row" id="conteudo">
		<?php if(get_field('fx_flex_habilitar_menu') != null || get_field('fx_flex_habilitar_menu', $parent) != null): ?>
			
			
			<div class="col-lg-12 col-xs-12">
				<h1 class="mb-4">
					<?php if($parent){
						echo get_the_title($parent);
					} else {
						the_title();
					} ?>
				</h1>
			</div>
			
			
			<div class="col-md-4">

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
											echo '<li><a href="' . get_the_permalink($page) . '">' . $campos['rotulo'] . '</a></li>';
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
		<?php endif; ?>
		
		<?php
			$coluna = 'col-md-12';
			$contentClass = '';
			if(get_field('fx_flex_habilitar_menu') != null || get_field('fx_flex_habilitar_menu', $parent) != null){
				$contentClass = 'content-margin';
				$coluna = 'col-md-8';
			}	
		?>
		
		<div class="<?php echo $coluna; ?>">

			<?php if($currentTitle): ?>
				<h2 class="submenu-title"><?php echo $currentTitle; ?></h2>
			<?php endif; ?>
			
			<?php
			//banner
			if(get_field('fx_flex_habilitar_banner') != null){
				$imagem_banner = get_field('fx_flex_banner');//Pega todos os valores da imagem no array
				echo '<div class="bn_fx_banner" style="background-image: url(' . $imagem_banner['url'] . ');">
					<div class="container"><h1>' . get_the_title() . '</h1></div>
				</div>';
			}
			
			if( have_rows('fx_flex_layout') ):
				while( have_rows('fx_flex_layout') ): the_row();
					
					
					////////////////////////////// Inicio 1 Coluna ///////////////////////////////
					if( get_row_layout() == 'fx_linha_coluna_1' ):
								//Personalização da coluna
								$background = get_sub_field('fx_fundo_da_coluna_1_1');
								$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
								$link = get_sub_field('fx_cor_do_link_coluna_1_1');
								$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
																
								//conteudo flexivel 1 coluna
								if( have_rows('fx_coluna_1_1') ):
									echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
									echo '<div class="container-fluid p-0">';//bootstrap container
									echo '<div class="row">';//bootstrap row
									echo '<div class="col-sm-12 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_1_1') ): the_row();
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_1_1' ):
												get_template_part( 'construtor/construtor', 'titulo_1_1' );

											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_1_1' ):
												get_template_part( 'construtor/construtor', 'editor_1_1' );
												
											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_1' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo_1_1' );
											
											//Loops sorteios por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_1_1' ):
												get_template_part( 'construtor/construtor', 'sorteios_1_1' );

											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_categ_1_1' ):
												get_template_part( 'construtor/construtor', 'noticias_1_1' );
											
											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_1_1' ): 
												get_template_part( 'construtor/construtor', 'video_1_1' );
											
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_1_1' ): 
												get_template_part( 'construtor/construtor', 'imagem_1_1' );
											
											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_1_1' ): 		
												get_template_part( 'construtor/construtor', 'abas_1_1' );
											
											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_1_1' ): 		
												get_template_part( 'construtor/construtor', 'sanfona_1_1' );

											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_1_1' ): 
												get_template_part( 'construtor/construtor', 'divisor_1_1' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_1_1' ): 
												get_template_part( 'construtor/construtor', 'botao_1_1' );
											
											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_1' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas_1_1' );
																			
											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_1_1' ):
												get_template_part( 'construtor/construtor', 'contato_individual_1_1' );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_1' ):
												get_template_part( 'construtor/construtor', 'slide_noticias_1_1' );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_1_1' ):
												get_template_part( 'construtor/construtor', 'slide_timer_1_1' );

											// Acesso Rapido
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_1' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido_1_1' );									

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_1' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_1_1' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_1' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer_1_1' );

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_1' ):
												get_template_part( 'construtor/construtor', 'bloco_rede_1_1' );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_1' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque_1_1' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_1' ):
												get_template_part( 'construtor/construtor', 'mais_lidas_1_1' );

											// Noticias mais lidas (Pagina)
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_pag_1_1' ):
												get_template_part( 'construtor/construtor', 'mais_lidas_pag_1_1' );

											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias_1_1' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres_1_1' );

											// Organograma DREs
											elseif( get_row_layout() == 'fx_cl1_organograma' ):
												get_template_part( 'construtor/construtor', 'organograma_1_1' );

											// Mural de Recados
											elseif( get_row_layout() == 'fx_cl1_mural_recados_1_1' ):
												get_template_part( 'construtor/construtor', 'mural_recados_1_1' );											

											// Portais e Sistemas
											elseif( get_row_layout() == 'fx_cl1_portais_sistemas_1_1' ):
												get_template_part( 'construtor/construtor', 'portais_sistemas_1_1' );

											// Portais e Sistemas - Carrossel
											elseif( get_row_layout() == 'fx_cl1_portais_sistemas_carrossel_1_1' ):
												get_template_part( 'construtor/construtor', 'portais_sistemas_carrossel_1_1' );

											// Cursos
											elseif( get_row_layout() == 'fx_cl1_cursos_1_1' ):
												get_template_part( 'construtor/construtor', 'cursos_1_1' );

											// Calendario Escolar
											elseif( get_row_layout() == 'fx_cl1_calendario_1_1' ):
												get_template_part( 'construtor/construtor', 'calendario_1_1' );

											// Agenda de Eventos
											elseif( get_row_layout() == 'agenda_eventos_1_1' ):
												get_template_part( 'construtor/construtor', 'agenda_eventos' );

											// FAQ
											elseif( get_row_layout() == 'fx_cl1_faq_1_1' ):
												get_template_part( 'construtor/construtor', 'faq_1_1' );

											// Empresas Parceiras
											elseif( get_row_layout() == 'fx_cl1_empresas_parceiras_1_1' ):
												get_template_part( 'construtor/construtor', 'empresas-parceiras' );

											// Card Subpaginas
											elseif( get_row_layout() == 'fx_cl1_card_subpaginas_1_1' ):
												get_template_part( 'construtor/construtor', 'card_subpaginas_1_1' );

											// Card Subpaginas  Internas
											elseif( get_row_layout() == 'fx_cl1_card_subpaginas_internas_1_1' ):
												get_template_part( 'construtor/construtor', 'card_subpaginas_internas_1_1' );

											// Ingressos Plateia
											elseif( get_row_layout() == 'fx_cl1_ingressos_plateia_1_1' ):
												get_template_part( 'construtor/construtor', 'ingressos_plateia_1_1' );

											// Certificados
											elseif( get_row_layout() == 'fx_cl1_certificados_1_1' ):
												get_template_part( 'construtor/construtor', 'certificados_1_1' );

											// SME Explica
											elseif( get_row_layout() == 'fx_cl1_sme_explica_1_1' ):
												get_template_part( 'construtor/construtor', 'sme_explica' );

											// Mural dos Professores
											elseif( get_row_layout() == 'fx_cl1_mural_prof_1_1' ):
												get_template_part( 'construtor/construtor', 'mural_prof' );

											// Mural dos Professores - Enviar Publicacao
											elseif( get_row_layout() == 'fx_cl1_mural_enviar_1_1' ):
												get_template_part( 'construtor/construtor', 'mural_enviar' );

											// Carrossel de sorteios
											elseif( get_row_layout() == 'fx_cl1_carro_sorteios_1_1' ):
												get_template_part( 'construtor/construtor', 'carro_sorteios' );

											// Carrossel de sorteios
											elseif( get_row_layout() == 'filtro_sorteios' ):
												get_template_part( 'construtor/construtor', 'filtro_sorteios' );

											// Integracao Pagina
											elseif( get_row_layout() == 'integrar_pagina' ):

												$page = get_sub_field('selecionar_paginas');

												// Pega o content padrao do WP
												foreach( $page as $post){
													$content_post = get_post($post);
													$content = $content_post->post_content;
													$content = apply_filters('the_content', $content);
													$content = str_replace(']]>', ']]&gt;', $content);
													echo "<div class='container'>";
													echo "<div class='row'>";
													echo "<div class='col-sm-12'>";
														echo $content;
													echo "</div>";
													echo "</div>";
													echo "</div>";
												}
												
												// Pega o conteudo do construtor
												foreach( $page as $post ) : setup_postdata( $post );										

													if( have_rows('fx_flex_layout', $post) ):
														while( have_rows('fx_flex_layout', $post) ): the_row();
														
																								
															if( get_row_layout() == 'fx_linha_coluna_1' ):
																//Personalização da coluna
																$background = get_sub_field('fx_fundo_da_coluna_1_1');
																$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
																$link = get_sub_field('fx_cor_do_link_coluna_1_1');
																$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
																
																//conteudo flexivel 1 coluna
																if( have_rows('fx_coluna_1_1', $post) ):
																	echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
																	echo '<div class="container">';//bootstrap container
																	echo '<div class="row">';//bootstrap row
																	echo '<div class="col-sm-12 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' p-0 ' . $contentClass . '">';//bootstrap col
																		while( have_rows('fx_coluna_1_1', $post) ): the_row();
																			
																			//titulo
																			if( get_row_layout() == 'fx_cl1_titulo_1_1' ):
																				get_template_part( 'construtor/construtor', 'titulo_1_1' );

																			//editor Wysiwyg
																			elseif( get_row_layout() == 'fx_cl1_editor_1_1' ):
																				get_template_part( 'construtor/construtor', 'editor_1_1' );
																				
																			//editor Wysiwyg com fundo
																			elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_1' ): 
																				get_template_part( 'construtor/construtor', 'editor_fundo_1_1' );
																			
																			//Loops noticias por categorias
																			elseif( get_row_layout() == 'fx_cl1_noticias_1_1' ):
																				get_template_part( 'construtor/construtor', 'noticias_categ_1_1' );
																			
																			//Video Responsivo
																			elseif( get_row_layout() == 'fx_cl1_video_1_1' ): 
																				get_template_part( 'construtor/construtor', 'video_1_1' );
																			
																			//imagem responsiva
																			elseif( get_row_layout() == 'fx_cl1_imagem_1_1' ): 
																				get_template_part( 'construtor/construtor', 'imagem_1_1' );
																			
																			//abas
																			elseif( get_row_layout() == 'fx_cl1_abas_1_1' ): 		
																				get_template_part( 'construtor/construtor', 'abas_1_1' );
																			
																			//Sanfona
																			elseif( get_row_layout() == 'fx_cl1_sanfona_1_1' ): 		
																				get_template_part( 'construtor/construtor', 'sanfona_1_1' );

																			//Divisor
																			elseif( get_row_layout() == 'fx_fl1_divisor_1_1' ): 
																				get_template_part( 'construtor/construtor', 'divisor_1_1' );
																			
																			//botão centralizado
																			elseif( get_row_layout() == 'fx_fl1_botao_1_1' ): 
																				get_template_part( 'construtor/construtor', 'botao_1_1' );
																			
																			//Contatos em camadas
																			elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_1' ): 
																				get_template_part( 'construtor/construtor', 'contato_camadas_1_1' );
																											
																			//Contato Individual
																			elseif( get_row_layout() == 'fx_cl1_contato_individual_1_1' ):
																				get_template_part( 'construtor/construtor', 'contato_individual_1_1' );

																			// Slide de Noticias
																			elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_1' ):
																				get_template_part( 'construtor/construtor', 'slide_noticias_1_1' );

																			// Acesso Rapido
																			elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_1' ):									
																				get_template_part( 'construtor/construtor', 'acesso_rapido_1_1' );									

																			// Bloco Noticias
																			elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_1' ):
																				get_template_part( 'construtor/construtor', 'bloco_noticias_1_1' );

																			// Bloco Redes Sociais
																			elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_1' ):
																				get_template_part( 'construtor/construtor', 'bloco_rede_1_1' );
																			// Noticias em destaque
																			elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_1' ):
																				get_template_part( 'construtor/construtor', 'noticias_destaque_1_1' );

																			// Noticias mais lidas
																			elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_1' ):
																				get_template_part( 'construtor/construtor', 'mais_lidas_1_1' );

																			// Noticias mais lidas (Pagina)
																			elseif( get_row_layout() == 'fx_cl1_mais_lidas_pag_1_1' ):
																				get_template_part( 'construtor/construtor', 'mais_lidas_pag_1_1' );

																			// Outras Noticias
																			elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																				get_template_part( 'construtor/construtor', 'outras_noticias_1_1' );

																			// Acesso Rapido
																			elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_2' ):									
																				get_template_part( 'construtor/construtor', 'acesso_rapido_1_2' );
																			
																			// Noticias DREs
																			elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																				get_template_part( 'construtor/construtor', 'noticias_dres_1_1' );
																			endif;

																		endwhile;
																	echo "</div>";
																	echo "</div>";
																	echo "</div>";
																	echo "</div>";
																endif;

															elseif( get_row_layout() == 'fx_linha_coluna_2' ):
																//Personalização da coluna
																$background = get_sub_field('fx_fundo_da_coluna_1_1');
																$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
																$link = get_sub_field('fx_cor_do_link_coluna_1_1');
																$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
																
																echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
																echo '<div class="container">';//bootstrap container
																echo '<div class="row">';//bootstrap row
																
																	//conteudo flexivel 2 colunas esquerda
																	if( have_rows('fx_coluna_1_2') ):
																		echo '<div class="col-sm-6 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_1_2') ): the_row();
																				
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_1_2' ):
																					get_template_part( 'construtor/construtor', 'titulo_1_2' );

																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_1_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_1_2' );
																				
																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '1_2' ) );
																				
																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_1_2' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '1_2' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_1_2' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '1_2' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_2' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '1_2' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_1_2' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_1_2' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '1_2' ) );
																				
																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_1_2' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '1_2' ) );
																				
																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_1_2' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '1_2' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_1_2' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_1_2' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '1_2' ) );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '1_2' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_2' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_1_2' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_2' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '1_2' ) );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_2' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_2' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '1_2' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;

																			endwhile;
																		echo '</div>';//bootstrap col

																	endif; // fx_coluna_1_2

																	//conteudo flexivel 2 colunas direita
																	if( have_rows('fx_coluna_2_2') ):
																		echo '<div class="col-sm-6 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_2_2') ): the_row();
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_2_2' ):
																					get_template_part( 'construtor/construtor', 'titulo_2_2' );

																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_2_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_2_2' );
																				
																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_2_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '2_2' ) );
																				
																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_2_2' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '2_2' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_2_2' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '2_2' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_2_2' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '2_2' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_2_2' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_2_2' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '2_2' ) );
																				
																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_2_2' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '2_2' ) );
																				
																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_2_2' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '2_2' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_2_2' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_2_2' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '2_2' ) );						

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_2_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '2_2' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_2_2' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_2_2' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_2_2' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '2_2' ) );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_2_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_2_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_2_2' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_2_2' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '2_2' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;
																			endwhile;
																		echo '</div>';//bootstrap col
																	endif; // fx_coluna_1_2

																echo '</div>';//bootstrap row
																echo '</div>';//bootstrap container
																echo '</div>';//fundo

															elseif( get_row_layout() == 'fx_linha_coluna_3' ):
																//Personalização da coluna
																$background = get_sub_field('fx_fundo_da_coluna_1_1');
																$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
																$link = get_sub_field('fx_cor_do_link_coluna_1_1');
																$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
																
																echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
																echo '<div class="container">';//bootstrap container
																echo '<div class="row">';//bootstrap row
																
																	//conteudo flexivel 3 colunas (primeira coluna)
																	if( have_rows('fx_coluna_1_3') ):
																		echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_1_3') ): the_row();
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_1_3' ):
																					get_template_part( 'construtor/construtor', 'titulo_3_1' );
																				
																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_1_3' ): 
																					get_template_part( 'construtor/construtor', 'editor_3_1' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_3' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '1_3' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_1_3' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '1_3' ) );

																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_1_3' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '1_3', 'size' => '1' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_3' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '1_3' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_1_3' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_1_3' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '1_3' ) );

																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_1_3' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '1_3' ) );

																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_1_3' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '1_3' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_1_3' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_1_3' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '1_3' ) );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_3' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '1_3' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_3' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_1_3' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_3' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_3' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_3' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '1_3' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_3' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_3' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '1_3' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );
																				
																				endif;
																			endwhile;
																		echo '</div>';
																	endif;

																	//conteudo flexivel 3 colunas (segunda coluna)
																	if( have_rows('fx_coluna_2_3') ):
																		echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_2_3') ): the_row();

																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_2_3' ):
																					get_template_part( 'construtor/construtor', 'titulo_3_2' );
																				
																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_2_3' ): 
																					get_template_part( 'construtor/construtor', 'editor_3_2' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_2_3' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '2_3' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_2_3' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '2_3' ) );

																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_2_3' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '2_3', 'size' => '1' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_2_3' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '2_3' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_2_3' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_2_3' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '2_3' ) );

																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_2_3' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '2_3' ) );

																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_2_3' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '2_3' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_2_3' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_2_3' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '2_3' ) );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_2_3' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '2_3' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_2_3' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_2_3' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_2_3' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_2_3' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_2_3' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '2_3' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_2_3' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_2_3' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '2_3' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;

																			endwhile;
																		echo '</div>';
																	endif;

																	//conteudo flexivel 3 colunas (terceira coluna)
																	if( have_rows('fx_coluna_3_3') ):
																		echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_3_3') ): the_row();
																				
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_3_3' ):
																					get_template_part( 'construtor/construtor', 'titulo_3_3' );
																				
																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_3_3' ): 
																					get_template_part( 'construtor/construtor', 'editor_3_3' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_3_3' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '3_3' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_3_3' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '3_3' ) );

																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_3_3' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '3_3', 'size' => '1' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_3_3' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '3_3' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_3_3' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_3_3' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '3_3' ) );

																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_3_3' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '3_3' ) );

																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_3_3' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '3_3' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_3_3' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_3_3' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '3_3' ) );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_3_3' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '3_3' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_3_3' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_3_3' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_3_3' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_3_3' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_3_3' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '3_3' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_3_3' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_3_3' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '3_3' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;

																			endwhile;
																		echo '</div>';
																	endif;
																
																echo '</div>';//bootstrap row
																echo '</div>';//bootstrap container
																echo '</div>';//fundo
																
															
															elseif( get_row_layout() == 'fx_linha_coluna_4' ):
																
																//Personalização da coluna
																$background = get_sub_field('fx_fundo_da_coluna_1_1');
																$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
																$link = get_sub_field('fx_cor_do_link_coluna_1_1');
																$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
																
																echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
																echo '<div class="container">';//bootstrap container
																echo '<div class="row">';//bootstrap row
																	
																	//conteudo flexivel 4 colunas (primeira coluna)
																	if( have_rows('fx_coluna_1_4') ):
																		echo '<div class="col-sm-3 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_1_4') ): the_row();

																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_1_4' ):
																					get_template_part( 'construtor/construtor', 'titulo_4_1' );									
																				
																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_1_4' ): 
																					get_template_part( 'construtor/construtor', 'editor_4_1' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_4' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '1_4' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_1_4' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '1_4' ) );

																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_1_4' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '1_4', 'size' => '1' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_4' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '1_4' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_1_4' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_1_4' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '1_4' ) );

																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_1_4' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '1_4' ) );

																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_1_4' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '1_4' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_1_4' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_1_4' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '1_4' ) );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_4' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '1_4' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_4' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_1_4' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '1_4' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_4' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_4' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '1_4' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;
																			
																			endwhile;
																		echo '</div>';
																	endif;

																	//conteudo flexivel 4 colunas (segunda coluna)
																	if( have_rows('fx_coluna_2_4') ):
																		echo '<div class="col-sm-3 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_2_4') ): the_row();
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_2_4' ):
																					get_template_part( 'construtor/construtor', 'titulo_4_2' );									
																				
																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_2_4' ): 
																					get_template_part( 'construtor/construtor', 'editor_4_2' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_2_4' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '2_4' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_2_4' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '2_4' ) );

																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_2_4' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '2_4', 'size' => '1' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_2_4' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '2_4' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_2_4' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_2_4' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '2_4' ) );

																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_2_4' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '2_4' ) );

																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_2_4' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '2_4' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_2_4' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_2_4' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '2_4' ) );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_2_4' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '2_4' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_2_4' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_2_4' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_2_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_2_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_2_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '2_4' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_2_4' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_2_4' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '2_4' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;
																			endwhile;
																		echo '</div>';
																	endif;

																	//conteudo flexivel 4 colunas (terceira coluna)
																	if( have_rows('fx_coluna_3_4') ):
																		echo '<div class="col-sm-3 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_3_4') ): the_row();
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_3_4' ):
																					get_template_part( 'construtor/construtor', 'titulo_4_3' );									
																				
																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_3_4' ): 
																					get_template_part( 'construtor/construtor', 'editor_4_3' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_3_4' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '3_4' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_3_4' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '3_4' ) );

																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_3_4' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '3_4', 'size' => '1' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_3_4' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '3_4' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_3_4' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_3_4' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '3_4' ) );

																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_3_4' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '3_4' ) );

																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_3_4' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '3_4' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_3_4' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_3_4' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '3_4' ) );
																				
																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_3_4' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '3_4' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_3_4' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_3_4' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_3_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_3_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_3_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '3_4' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_3_4' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_3_4' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '3_4' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;
																			endwhile;
																		echo '</div>';
																	endif;

																	//conteudo flexivel 4 colunas (quarta coluna)
																	if( have_rows('fx_coluna_4_4') ):
																		echo '<div class="col-sm-3 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_4_4') ): the_row();
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_4_4' ):
																					get_template_part( 'construtor/construtor', 'titulo_4_4' );									
																				
																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_4_4' ): 
																					get_template_part( 'construtor/construtor', 'editor_4_4' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_4_4' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '4_4' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_4_4' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '4_4' ) );

																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_4_4' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '4_4', 'size' => '1' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_4_4' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '4_4' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_4_4' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																												
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_4_4' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '4_4' ) );

																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_4_4' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '4_4' ) );

																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_4_4' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '4_4' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_4_4' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_4_4' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '4_4' ) );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_4_4' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '4_4' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_4_4' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_4_4' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_4_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_4_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_4_4' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '4_4' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_4_4' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_4_4' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '4_4' ) );
																				
																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;
																			endwhile;
																		echo '</div>';
																	endif;

																echo '</div>';//bootstrap row
																echo '</div>';//bootstrap container
																echo '</div>';//fundo

															elseif( get_row_layout() == 'fx_linha_coluna_1b3' ):
																//Personalização da coluna
																$background = get_sub_field('fx_fundo_da_coluna_1_1');
																$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
																$link = get_sub_field('fx_cor_do_link_coluna_1_1');
																$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
																
																echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
																echo '<div class="container">';//bootstrap container
																echo '<div class="row">';//bootstrap row
																
																	//conteudo flexivel 2 colunas esquerda
																	if( have_rows('fx_coluna_1_1b3') ):
												
																		echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_1_1b3') ): the_row();
																				
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_1_2' ):								
																					get_template_part( 'construtor/construtor', 'titulo_3b_1' );
																				
																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_1_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_3b_1' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '1_2' ) );
																				
																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_1_2' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '1_2', 'size' => '1' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_1_2' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '1_2' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_2' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '1_2' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_1_2' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_1_2' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '1_2' ) );
																				
																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_1_2' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '1_2' ) );
																				
																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_1_2' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '1_2' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_1_2' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_1_2' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '1_2' ) );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_2' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '1_2' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_2' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_1_2' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '1_2' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_2' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_2' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '1_2' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;

																			endwhile;
																		echo '</div>';

																	endif;

																	//conteudo flexivel 2 colunas direita
																	if( have_rows('fx_coluna_2_1b3') ):
																		echo '<div class="col-sm-8 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_2_1b3') ): the_row();
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_2_2' ):
																					get_template_part( 'construtor/construtor', 'titulo_1b_3' );

																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_2_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_1b_3' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_2_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '2_2' ) );
																				
																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_2_2' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '2_2' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_2_2' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '2_2' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_2_2' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '2_2' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_2_2' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_2_2' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '2_2' ) );
																				
																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_2_2' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '2_2' ) );
																				
																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_2_2' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '2_2' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_2_2' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_2_2' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '2_2' ) );
																				
																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_2_2' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '2_2' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_2_2' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_2_2' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_2_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_2_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_2_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '2_2' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_2_2' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_2_2' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '2_2' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;
																			endwhile;
																		echo '</div>';
																	endif;
																
																echo '</div>';//bootstrap row
																echo '</div>';//bootstrap container
																echo '</div>';//fundo

															elseif( get_row_layout() == 'fx_linha_coluna_3b1' ):
																//Personalização da coluna
																$background = get_sub_field('fx_fundo_da_coluna_1_1');
																$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
																$link = get_sub_field('fx_cor_do_link_coluna_1_1');
																$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
																
																echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
																echo '<div class="container">';//bootstrap container
																echo '<div class="row">';//bootstrap row
																	
																	//conteudo flexivel 2 colunas esquerda
																	if( have_rows('fx_coluna_1_3b1') ):
												
																		echo '<div class="col-sm-8 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_1_3b1') ): the_row();
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_1_2' ):
																					get_template_part( 'construtor/construtor', 'titulo_1_3b' );
																				
																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_1_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_1_3b' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '1_2' ) );
																				
																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_1_2' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '1_2', 'size' => '' ) );
																				
																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_1_2' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '1_2' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_2' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '1_2' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_1_2' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_1_2' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '1_2' ) );
																				
																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_1_2' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '1_2' ) );
																				
																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_1_2' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '1_2' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_1_2' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_1_2' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '1_2' ) );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_2' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '1_2' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_2' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_1_2' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '1_2' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_2' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_2' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '1_2' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;
																			endwhile;
																		echo '</div>';

																	endif;

																	//conteudo flexivel 2 colunas direita
																	if( have_rows('fx_coluna_2_3b1') ):
																		echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value'].' ' . $contentClass . '">';//bootstrap col
																			while( have_rows('fx_coluna_2_3b1') ): the_row();
																				
																				//titulo
																				if( get_row_layout() == 'fx_cl1_titulo_2_2' ):
																					get_template_part( 'construtor/construtor', 'titulo_3_1b' );
																				
																				//editor Wysiwyg
																				elseif( get_row_layout() == 'fx_cl1_editor_2_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_3_1b' );

																				//editor Wysiwyg com fundo
																				elseif( get_row_layout() == 'fx_cl1_editor_fundo_2_2' ): 
																					get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '2_2' ) );
																				
																				//Loops noticias por categorias
																				elseif( get_row_layout() == 'fx_cl1_noticias_2_2' ):
																					get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '2_2', 'size' => '1' ) );

																				//Video Responsivo
																				elseif( get_row_layout() == 'fx_cl1_video_2_2' ): 
																					get_template_part( 'construtor/construtor', 'video', array( 'key' => '2_2' ) );

																				// Slide de Noticias
																				elseif( get_row_layout() == 'fx_cl1_slide_noticias_2_2' ):
																					get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '2_2' ) );

																				// Slide de Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_slide_timer_2_2' ):
																					get_template_part( 'construtor/construtor', 'slide_timer' );
																				
																				//imagem responsiva
																				elseif( get_row_layout() == 'fx_cl1_imagem_2_2' ): 
																					get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '2_2' ) );
																				
																				//abas
																				elseif( get_row_layout() == 'fx_cl1_abas_2_2' ): 		
																					get_template_part( 'construtor/construtor', 'abas', array( 'key' => '2_2' ) );
																				
																				//Sanfona
																				elseif( get_row_layout() == 'fx_cl1_sanfona_2_2' ): 		
																					get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '2_2' ) );
																				
																				//Divisor
																				elseif( get_row_layout() == 'fx_fl1_divisor_2_2' ): 
																					get_template_part( 'construtor/construtor', 'divisor' );
																				
																				//botão centralizado
																				elseif( get_row_layout() == 'fx_fl1_botao_2_2' ): 
																					get_template_part( 'construtor/construtor', 'botao', array( 'key' => '2_2' ) );

																				// Acesso Rapido								
																				elseif( get_row_layout() == 'fx_cl1_acesso_rapido_2_2' ):									
																					get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '2_2' ) );

																				//Contatos em camadas
																				elseif( get_row_layout() == 'fx_cl1_contato_camadas_2_2' ): 
																					get_template_part( 'construtor/construtor', 'contato_camadas' );

																				//Contato Individual
																				elseif( get_row_layout() == 'fx_cl1_contato_individual_2_2' ):
																					get_template_part( 'construtor/construtor', 'contato_individual' );

																				// Bloco Noticias
																				elseif( get_row_layout() == 'fx_cl1_bloco_noticias_2_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias' );

																				// Bloco Noticias - Timer
																				elseif( get_row_layout() == 'fx_cl1_bloco_timer_2_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

																				// Bloco Redes Sociais
																				elseif( get_row_layout() == 'fx_fl1_bloco_rede_2_2' ):
																					get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '2_2' ) );

																				// Noticias em destaque
																				elseif( get_row_layout() == 'fx_cl1_noticias_destaque_2_2' ):
																					get_template_part( 'construtor/construtor', 'noticias_destaque' );

																				// Noticias mais lidas
																				elseif( get_row_layout() == 'fx_cl1_mais_lidas_2_2' ):
																					get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '2_2' ) );

																				// Outras Noticias
																				elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
																					get_template_part( 'construtor/construtor', 'outras_noticias' );

																				// Noticias DREs
																				elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
																					get_template_part( 'construtor/construtor', 'noticias_dres' );

																				endif;
																				
																			endwhile;
																		echo '</div>';

																	endif;

																echo '</div>';//bootstrap row
																echo '</div>';//bootstrap container
																echo '</div>';//fundo
															
															endif; // fx_linha_coluna_1b3
															
									
														endwhile;
													endif;
													
													
												endforeach;
												wp_reset_postdata();

												//echo get_the_content($page[0]);

											endif; //fx_fl1_bloco_rede_1_1

										endwhile;
									echo '</div>';//bootstrap col
									echo '</div>';//bootstrap row
									echo '</div>';//bootstrap container
									echo '</div>';//fundo
								endif;
					////////////////////////////// Final 1 Coluna///////////////////////////////

					////////////////////////////// Inicio Sanfona DRE ///////////////////////////////
					elseif( get_row_layout() == 'sanfona_dre' ):
								
									echo '<div class="container">';//bootstrap container
									echo '<div class="row">';//bootstrap row
									echo '<div class="col-sm-8">';
									echo '<img src="https://hom-educacao.sme.prefeitura.sp.gov.br/wp-content/uploads/2020/08/Mapa.png" width="100%">';
									echo '</div>';
									echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3">';//bootstrap col
											
											//sanfona DRE										
											get_template_part( 'construtor/construtor', 'sanfona_dre' );

									echo '</div>';//bootstrap col
									echo '</div>';//bootstrap row
									echo '</div>';//bootstrap container

					////////////////////////////// Final sanfona DRE///////////////////////////////
					
					////////////////////////////// Inicio 2 Colunas ///////////////////////////////
					elseif( get_row_layout() == 'fx_linha_coluna_2' ):
								//Personalização da coluna
								$background = get_sub_field('fx_fundo_da_coluna_1_1');
								$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
								$link = get_sub_field('fx_cor_do_link_coluna_1_1');
								$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');

								
								echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
								if($contentClass){
									echo '<div class="container p-0">';//bootstrap container
								} else {
									echo '<div class="container">';//bootstrap container
								}
								
								echo '<div class="row">';//bootstrap row
								
								//conteudo flexivel 2 colunas esquerda
								if( have_rows('fx_coluna_1_2') ):
									echo '<div class="col-sm-6 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_1_2') ): the_row();
											
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_1_2' ):
												get_template_part( 'construtor/construtor', 'titulo_1_2' );

											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_1_2' ): 
												get_template_part( 'construtor/construtor', 'editor_1_2' );
											
											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_2' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '1_2' ) );
											
											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_1_2' ):
												get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '1_2' ) );
											
											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_1_2' ): 
												get_template_part( 'construtor/construtor', 'video', array( 'key' => '1_2' ) );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_2' ):
												get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '1_2' ) );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_1_2' ):
												get_template_part( 'construtor/construtor', 'slide_timer' );
											
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_1_2' ): 
												get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '1_2' ) );
											
											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_1_2' ): 		
												get_template_part( 'construtor/construtor', 'abas', array( 'key' => '1_2' ) );
											
											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_1_2' ): 		
												get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '1_2' ) );
											
											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_1_2' ): 
												get_template_part( 'construtor/construtor', 'divisor' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_1_2' ): 
												get_template_part( 'construtor/construtor', 'botao', array( 'key' => '1_2' ) );

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_2' ):
												get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '1_2' ) );

											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_2' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas' );

											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_1_2' ):
												get_template_part( 'construtor/construtor', 'contato_individual' );

											// Acesso Rapido								
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_2' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '1_2' ) );

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_2' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_2' ):
												get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '1_2' ) );

											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres' );

											// Mural de Recados - Destaques
											elseif( get_row_layout() == 'fx_cl1_mural_recados_destaques_1_2' ):
												get_template_part( 'construtor/construtor', 'mural_recados_destaques_1_2' );

											// Newsletter
											elseif( get_row_layout() == 'fx_cl1_form_news_1_2' ):
												get_template_part( 'construtor/construtor', 'form_news_1_2' );

											// Cartao Educador
											elseif( get_row_layout() == 'fx_cl1_cartao_educador_1_2' ):
												get_template_part( 'construtor/construtor', 'cartao_educador', array( 'key' => '1_2' ) );

											// Card com Imagem
											elseif( get_row_layout() == 'fx_cl1_card_imagem_1_2' ):
												get_template_part( 'construtor/construtor', 'card_imagem', array( 'key' => '1_2' ) );

											// Banner Horizontal
											elseif( get_row_layout() == 'fx_cl1_banner_horiz_1_2' ):
												get_template_part( 'construtor/construtor', 'banner_horiz', array( 'key' => '1_2' ) );
											
											// SME Explica - Destaque
											elseif( get_row_layout() == 'fx_cl1_dest_sme_ex_1_2' ):
												get_template_part( 'construtor/construtor', 'sme_explica_dest', array( 'key' => '1_2' ) );

											endif;

										endwhile;
									echo '</div>';//bootstrap col

								endif;//-------------------------------------------------------------
					
								//conteudo flexivel 2 colunas direita
								if( have_rows('fx_coluna_2_2') ):
									echo '<div class="col-sm-6 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_2_2') ): the_row();
											
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_2_2' ):
												get_template_part( 'construtor/construtor', 'titulo_2_2' );

											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_2_2' ): 
												get_template_part( 'construtor/construtor', 'editor_2_2' );
											
											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_2_2' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '2_2' ) );
											
											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_2_2' ):
												get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '2_2' ) );
											
											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_2_2' ): 
												get_template_part( 'construtor/construtor', 'video', array( 'key' => '2_2' ) );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_2_2' ):
												get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '2_2' ) );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_2_2' ):
												get_template_part( 'construtor/construtor', 'slide_timer' );
											
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_2_2' ): 
												get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '2_2' ) );
											
											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_2_2' ): 		
												get_template_part( 'construtor/construtor', 'abas', array( 'key' => '2_2' ) );
											
											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_2_2' ): 		
												get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '2_2' ) );
											
											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_2_2' ): 
												get_template_part( 'construtor/construtor', 'divisor' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_2_2' ): 
												get_template_part( 'construtor/construtor', 'botao', array( 'key' => '2_2' ) );						

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_2_2' ):
												get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '2_2' ) );

											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_2_2' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas' );

											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_2_2' ):
												get_template_part( 'construtor/construtor', 'contato_individual' );

											// Acesso Rapido								
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_2_2' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '2_2' ) );

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_2_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_2_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_2_2' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_2_2' ):
												get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '2_2' ) );

											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres' );

											// Cursos - Destaques
											elseif( get_row_layout() == 'fx_cl1_cursos_destaques_2_2' ):
												get_template_part( 'construtor/construtor', 'cursos_destaques_2_2' );

											// Banner WhatsApp
											elseif( get_row_layout() == 'fx_cl1_banner_whatsapp_2_2' ):
												get_template_part( 'construtor/construtor', 'banner_whatsapp_2_2' );
											
											// Cartao Educador
											elseif( get_row_layout() == 'fx_cl1_cartao_educador_2_2' ):
												get_template_part( 'construtor/construtor', 'cartao_educador', array( 'key' => '2_2' ) );
											
											// Card com Imagem
											elseif( get_row_layout() == 'fx_cl1_card_imagem_2_2' ):
												get_template_part( 'construtor/construtor', 'card_imagem', array( 'key' => '2_2' ) );

											// Banner Horizontal
											elseif( get_row_layout() == 'fx_cl1_banner_horiz_2_2' ):
												get_template_part( 'construtor/construtor', 'banner_horiz', array( 'key' => '2_2' ) );

											endif;

										endwhile;
									echo '</div>';//bootstrap col

								endif;//-------------------------------------------------------------
					
									echo '</div>';//bootstrap row
									echo '</div>';//bootstrap container
									echo '</div>';//fundo
					////////////////////////////// Final 2 Colunas ///////////////////////////////
					
					////////////////////////////// Inicio 3 Colunas ///////////////////////////////
					elseif( get_row_layout() == 'fx_linha_coluna_3' ):
								//Personalização da coluna
								$background = get_sub_field('fx_fundo_da_coluna_1_1');
								$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
								$link = get_sub_field('fx_cor_do_link_coluna_1_1');
								$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
								
								echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
								if($contentClass){
									echo '<div class="container p-0">';//bootstrap container
								}else{
									echo '<div class="container">';//bootstrap container
								}
								echo '<div class="row">';//bootstrap row
								
									//conteudo flexivel 3 colunas (primeira coluna)
									if( have_rows('fx_coluna_1_3') ):
										echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
											while( have_rows('fx_coluna_1_3') ): the_row();
												
												//titulo
												if( get_row_layout() == 'fx_cl1_titulo_1_3' ):
													get_template_part( 'construtor/construtor', 'titulo_3_1' );
												
												//editor Wysiwyg
												elseif( get_row_layout() == 'fx_cl1_editor_1_3' ): 
													get_template_part( 'construtor/construtor', 'editor_3_1' );

												//editor Wysiwyg com fundo
												elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_3' ): 
													get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '1_3' ) );
												
												//Video Responsivo
												elseif( get_row_layout() == 'fx_cl1_video_1_3' ): 
													get_template_part( 'construtor/construtor', 'video', array( 'key' => '1_3' ) );

												//Loops noticias por categorias
												elseif( get_row_layout() == 'fx_cl1_noticias_1_3' ):
													get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '1_3', 'size' => '1' ) );

												// Slide de Noticias
												elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_3' ):
													get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '1_3' ) );

												// Slide de Noticias - Timer
												elseif( get_row_layout() == 'fx_cl1_slide_timer_1_3' ):
													get_template_part( 'construtor/construtor', 'slide_timer' );
												
												//imagem responsiva
												elseif( get_row_layout() == 'fx_cl1_imagem_1_3' ): 
													get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '1_3' ) );

												//abas
												elseif( get_row_layout() == 'fx_cl1_abas_1_3' ): 		
													get_template_part( 'construtor/construtor', 'abas', array( 'key' => '1_3' ) );

												//Sanfona
												elseif( get_row_layout() == 'fx_cl1_sanfona_1_3' ): 		
													get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '1_3' ) );
												
												//Divisor
												elseif( get_row_layout() == 'fx_fl1_divisor_1_3' ): 
													get_template_part( 'construtor/construtor', 'divisor' );
												
												//botão centralizado
												elseif( get_row_layout() == 'fx_fl1_botao_1_3' ): 
													get_template_part( 'construtor/construtor', 'botao', array( 'key' => '1_3' ) );

												// Acesso Rapido								
												elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_3' ):									
													get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '1_3' ) );

												//Contatos em camadas
												elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_3' ): 
													get_template_part( 'construtor/construtor', 'contato_camadas' );

												//Contato Individual
												elseif( get_row_layout() == 'fx_cl1_contato_individual_1_3' ):
													get_template_part( 'construtor/construtor', 'contato_individual' );

												// Bloco Noticias
												elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_3' ):
													get_template_part( 'construtor/construtor', 'bloco_noticias' );

												// Bloco Noticias - Timer
												elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_3' ):
													get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

												// Bloco Redes Sociais
												elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_3' ):
													get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '1_3' ) );

												// Noticias em destaque
												elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_3' ):
													get_template_part( 'construtor/construtor', 'noticias_destaque' );

												// Noticias mais lidas
												elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_3' ):
													get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '1_3' ) );

												// Outras Noticias
												elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
													get_template_part( 'construtor/construtor', 'outras_noticias' );

												// Noticias DREs
												elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
													get_template_part( 'construtor/construtor', 'noticias_dres' );
												
												endif;

											endwhile;
										echo '</div>';//bootstrap col

									endif;//-------------------------------------------------------------
						
									//conteudo flexivel 3 colunas (segunda coluna)
									if( have_rows('fx_coluna_2_3') ):
										echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
											while( have_rows('fx_coluna_2_3') ): the_row();
												
												//titulo
												if( get_row_layout() == 'fx_cl1_titulo_2_3' ):
													get_template_part( 'construtor/construtor', 'titulo_3_2' );
												
												//editor Wysiwyg
												elseif( get_row_layout() == 'fx_cl1_editor_2_3' ): 
													get_template_part( 'construtor/construtor', 'editor_3_2' );

												//editor Wysiwyg com fundo
												elseif( get_row_layout() == 'fx_cl1_editor_fundo_2_3' ): 
													get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '2_3' ) );
												
												//Video Responsivo
												elseif( get_row_layout() == 'fx_cl1_video_2_3' ): 
													get_template_part( 'construtor/construtor', 'video', array( 'key' => '2_3' ) );

												//Loops noticias por categorias
												elseif( get_row_layout() == 'fx_cl1_noticias_2_3' ):
													get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '2_3', 'size' => '1' ) );

												// Slide de Noticias
												elseif( get_row_layout() == 'fx_cl1_slide_noticias_2_3' ):
													get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '2_3' ) );

												// Slide de Noticias - Timer
												elseif( get_row_layout() == 'fx_cl1_slide_timer_2_3' ):
													get_template_part( 'construtor/construtor', 'slide_timer' );
												
												//imagem responsiva
												elseif( get_row_layout() == 'fx_cl1_imagem_2_3' ): 
													get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '2_3' ) );

												//abas
												elseif( get_row_layout() == 'fx_cl1_abas_2_3' ): 		
													get_template_part( 'construtor/construtor', 'abas', array( 'key' => '2_3' ) );

												//Sanfona
												elseif( get_row_layout() == 'fx_cl1_sanfona_2_3' ): 		
													get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '2_3' ) );
												
												//Divisor
												elseif( get_row_layout() == 'fx_fl1_divisor_2_3' ): 
													get_template_part( 'construtor/construtor', 'divisor' );
												
												//botão centralizado
												elseif( get_row_layout() == 'fx_fl1_botao_2_3' ): 
													get_template_part( 'construtor/construtor', 'botao', array( 'key' => '2_3' ) );

												// Acesso Rapido								
												elseif( get_row_layout() == 'fx_cl1_acesso_rapido_2_3' ):									
													get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '2_3' ) );

												//Contatos em camadas
												elseif( get_row_layout() == 'fx_cl1_contato_camadas_2_3' ): 
													get_template_part( 'construtor/construtor', 'contato_camadas' );

												//Contato Individual
												elseif( get_row_layout() == 'fx_cl1_contato_individual_2_3' ):
													get_template_part( 'construtor/construtor', 'contato_individual' );

												// Bloco Noticias
												elseif( get_row_layout() == 'fx_cl1_bloco_noticias_2_3' ):
													get_template_part( 'construtor/construtor', 'bloco_noticias' );

												// Bloco Noticias - Timer
												elseif( get_row_layout() == 'fx_cl1_bloco_timer_2_3' ):
													get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

												// Bloco Redes Sociais
												elseif( get_row_layout() == 'fx_fl1_bloco_rede_2_3' ):
													get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '2_3' ) );

												// Noticias em destaque
												elseif( get_row_layout() == 'fx_cl1_noticias_destaque_2_3' ):
													get_template_part( 'construtor/construtor', 'noticias_destaque' );

												// Noticias mais lidas
												elseif( get_row_layout() == 'fx_cl1_mais_lidas_2_3' ):
													get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '2_3' ) );

												// Outras Noticias
												elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
													get_template_part( 'construtor/construtor', 'outras_noticias' );

												// Noticias DREs
												elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
													get_template_part( 'construtor/construtor', 'noticias_dres' );

												endif;

											endwhile;
										echo '</div>';//bootstrap col

									endif;//-------------------------------------------------------------
						
						
									//conteudo flexivel 3 colunas (terceira coluna)
									if( have_rows('fx_coluna_3_3') ):
										echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
											while( have_rows('fx_coluna_3_3') ): the_row();
												
												//titulo
												if( get_row_layout() == 'fx_cl1_titulo_3_3' ):
													get_template_part( 'construtor/construtor', 'titulo_3_3' );
												
												//editor Wysiwyg
												elseif( get_row_layout() == 'fx_cl1_editor_3_3' ): 
													get_template_part( 'construtor/construtor', 'editor_3_3' );

												//editor Wysiwyg com fundo
												elseif( get_row_layout() == 'fx_cl1_editor_fundo_3_3' ): 
													get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '3_3' ) );
												
												//Video Responsivo
												elseif( get_row_layout() == 'fx_cl1_video_3_3' ): 
													get_template_part( 'construtor/construtor', 'video', array( 'key' => '3_3' ) );

												//Loops noticias por categorias
												elseif( get_row_layout() == 'fx_cl1_noticias_3_3' ):
													get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '3_3', 'size' => '1' ) );

												// Slide de Noticias
												elseif( get_row_layout() == 'fx_cl1_slide_noticias_3_3' ):
													get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '3_3' ) );

												// Slide de Noticias - Timer
												elseif( get_row_layout() == 'fx_cl1_slide_timer_3_3' ):
													get_template_part( 'construtor/construtor', 'slide_timer' );
												
												//imagem responsiva
												elseif( get_row_layout() == 'fx_cl1_imagem_3_3' ): 
													get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '3_3' ) );

												//abas
												elseif( get_row_layout() == 'fx_cl1_abas_3_3' ): 		
													get_template_part( 'construtor/construtor', 'abas', array( 'key' => '3_3' ) );

												//Sanfona
												elseif( get_row_layout() == 'fx_cl1_sanfona_3_3' ): 		
													get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '3_3' ) );
												
												//Divisor
												elseif( get_row_layout() == 'fx_fl1_divisor_3_3' ): 
													get_template_part( 'construtor/construtor', 'divisor' );
												
												//botão centralizado
												elseif( get_row_layout() == 'fx_fl1_botao_3_3' ): 
													get_template_part( 'construtor/construtor', 'botao', array( 'key' => '3_3' ) );

												// Acesso Rapido								
												elseif( get_row_layout() == 'fx_cl1_acesso_rapido_3_3' ):									
													get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '3_3' ) );

												//Contatos em camadas
												elseif( get_row_layout() == 'fx_cl1_contato_camadas_3_3' ): 
													get_template_part( 'construtor/construtor', 'contato_camadas' );

												//Contato Individual
												elseif( get_row_layout() == 'fx_cl1_contato_individual_3_3' ):
													get_template_part( 'construtor/construtor', 'contato_individual' );

												// Bloco Noticias
												elseif( get_row_layout() == 'fx_cl1_bloco_noticias_3_3' ):
													get_template_part( 'construtor/construtor', 'bloco_noticias' );

												// Bloco Noticias - Timer
												elseif( get_row_layout() == 'fx_cl1_bloco_timer_3_3' ):
													get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

												// Bloco Redes Sociais
												elseif( get_row_layout() == 'fx_fl1_bloco_rede_3_3' ):
													get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '3_3' ) );

												// Noticias em destaque
												elseif( get_row_layout() == 'fx_cl1_noticias_destaque_3_3' ):
													get_template_part( 'construtor/construtor', 'noticias_destaque' );

												// Noticias mais lidas
												elseif( get_row_layout() == 'fx_cl1_mais_lidas_3_3' ):
													get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '3_3' ) );

												// Outras Noticias
												elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
													get_template_part( 'construtor/construtor', 'outras_noticias' );

												// Noticias DREs
												elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
													get_template_part( 'construtor/construtor', 'noticias_dres' );

												endif;

											endwhile;
										echo '</div>';//bootstrap col

									endif;//-------------------------------------------------------------
					
								echo '</div>';//bootstrap row
								echo '</div>';//bootstrap container
								echo '</div>';//fundo

					////////////////////////////// Final 3 Colunas ///////////////////////////////
					
					////////////////////////////// Inicio 4 Colunas ///////////////////////////////
					elseif( get_row_layout() == 'fx_linha_coluna_4' ):
								//Personalização da coluna
								$background = get_sub_field('fx_fundo_da_coluna_1_1');
								$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
								$link = get_sub_field('fx_cor_do_link_coluna_1_1');
								$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
								
								echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
								if($contentClass){
									echo '<div class="container p-0">';//bootstrap container
								} else {
									echo '<div class="container">';//bootstrap container
								}			
								echo '<div class="row">';//bootstrap row
								
								//conteudo flexivel 4 colunas (primeira coluna)
								if( have_rows('fx_coluna_1_4') ):
									echo '<div class="col-sm-3 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_1_4') ): the_row();
											
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_1_4' ):
												get_template_part( 'construtor/construtor', 'titulo_4_1' );									
											
											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_1_4' ): 
												get_template_part( 'construtor/construtor', 'editor_4_1' );

											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_4' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '1_4' ) );
											
											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_1_4' ): 
												get_template_part( 'construtor/construtor', 'video', array( 'key' => '1_4' ) );

											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_1_4' ):
												get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '1_4', 'size' => '1' ) );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_4' ):
												get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '1_4' ) );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_1_4' ):
												get_template_part( 'construtor/construtor', 'slide_timer' );
											
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_1_4' ): 
												get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '1_4' ) );

											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_1_4' ): 		
												get_template_part( 'construtor/construtor', 'abas', array( 'key' => '1_4' ) );

											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_1_4' ): 		
												get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '1_4' ) );
											
											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_1_4' ): 
												get_template_part( 'construtor/construtor', 'divisor' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_1_4' ): 
												get_template_part( 'construtor/construtor', 'botao', array( 'key' => '1_4' ) );

											// Acesso Rapido								
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_4' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '1_4' ) );

											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_4' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas' );

											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_1_4' ):
												get_template_part( 'construtor/construtor', 'contato_individual' );

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_4' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_4' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_4' ):
												get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '1_4' ) );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_4' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_4' ):
												get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '1_4' ) );

											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres' );

											endif;

										endwhile;
									echo '</div>';//bootstrap col

								endif;//-------------------------------------------------------------
					
								//conteudo flexivel 4 colunas (segunda coluna)
								if( have_rows('fx_coluna_2_4') ):
									echo '<div class="col-sm-3 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_2_4') ): the_row();
											
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_2_4' ):
												get_template_part( 'construtor/construtor', 'titulo_4_2' );									
											
											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_2_4' ): 
												get_template_part( 'construtor/construtor', 'editor_4_2' );

											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_2_4' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '2_4' ) );
											
											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_2_4' ): 
												get_template_part( 'construtor/construtor', 'video', array( 'key' => '2_4' ) );

											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_2_4' ):
												get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '2_4', 'size' => '1' ) );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_2_4' ):
												get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '2_4' ) );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_2_4' ):
												get_template_part( 'construtor/construtor', 'slide_timer' );
											
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_2_4' ): 
												get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '2_4' ) );

											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_2_4' ): 		
												get_template_part( 'construtor/construtor', 'abas', array( 'key' => '2_4' ) );

											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_2_4' ): 		
												get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '2_4' ) );
											
											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_2_4' ): 
												get_template_part( 'construtor/construtor', 'divisor' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_2_4' ): 
												get_template_part( 'construtor/construtor', 'botao', array( 'key' => '2_4' ) );

											// Acesso Rapido								
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_2_4' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '2_4' ) );

											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_2_4' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas' );

											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_2_4' ):
												get_template_part( 'construtor/construtor', 'contato_individual' );

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_2_4' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_2_4' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_2_4' ):
												get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '2_4' ) );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_2_4' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_2_4' ):
												get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '2_4' ) );

											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres' );

											endif;	

										endwhile;
									echo '</div>';//bootstrap col

								endif;//-------------------------------------------------------------
					
					
								//conteudo flexivel 4 colunas (terceira coluna)
								if( have_rows('fx_coluna_3_4') ):
									echo '<div class="col-sm-3 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_3_4') ): the_row();
											
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_3_4' ):
												get_template_part( 'construtor/construtor', 'titulo_4_3' );									
											
											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_3_4' ): 
												get_template_part( 'construtor/construtor', 'editor_4_3' );

											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_3_4' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '3_4' ) );
											
											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_3_4' ): 
												get_template_part( 'construtor/construtor', 'video', array( 'key' => '3_4' ) );

											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_3_4' ):
												get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '3_4', 'size' => '1' ) );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_3_4' ):
												get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '3_4' ) );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_3_4' ):
												get_template_part( 'construtor/construtor', 'slide_timer' );
											
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_3_4' ): 
												get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '3_4' ) );

											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_3_4' ): 		
												get_template_part( 'construtor/construtor', 'abas', array( 'key' => '3_4' ) );

											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_3_4' ): 		
												get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '3_4' ) );
											
											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_3_4' ): 
												get_template_part( 'construtor/construtor', 'divisor' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_3_4' ): 
												get_template_part( 'construtor/construtor', 'botao', array( 'key' => '3_4' ) );
											
											// Acesso Rapido								
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_3_4' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '3_4' ) );

											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_3_4' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas' );

											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_3_4' ):
												get_template_part( 'construtor/construtor', 'contato_individual' );

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_3_4' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_3_4' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_3_4' ):
												get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '3_4' ) );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_3_4' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_3_4' ):
												get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '3_4' ) );

											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres' );

											endif;	

										endwhile;
									echo '</div>';//bootstrap col

								endif;//-------------------------------------------------------------
					
								//conteudo flexivel 4 colunas (quarta coluna)
								if( have_rows('fx_coluna_4_4') ):
									echo '<div class="col-sm-3 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_4_4') ): the_row();
											
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_4_4' ):
												get_template_part( 'construtor/construtor', 'titulo_4_4' );									
											
											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_4_4' ): 
												get_template_part( 'construtor/construtor', 'editor_4_4' );

											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_4_4' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '4_4' ) );
											
											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_4_4' ): 
												get_template_part( 'construtor/construtor', 'video', array( 'key' => '4_4' ) );

											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_4_4' ):
												get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '4_4', 'size' => '1' ) );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_4_4' ):
												get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '4_4' ) );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_4_4' ):
												get_template_part( 'construtor/construtor', 'slide_timer' );
																			
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_4_4' ): 
												get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '4_4' ) );

											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_4_4' ): 		
												get_template_part( 'construtor/construtor', 'abas', array( 'key' => '4_4' ) );

											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_4_4' ): 		
												get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '4_4' ) );
											
											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_4_4' ): 
												get_template_part( 'construtor/construtor', 'divisor' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_4_4' ): 
												get_template_part( 'construtor/construtor', 'botao', array( 'key' => '4_4' ) );

											// Acesso Rapido								
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_4_4' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '4_4' ) );

											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_4_4' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas' );

											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_4_4' ):
												get_template_part( 'construtor/construtor', 'contato_individual' );

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_4_4' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_4_4' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_4_4' ):
												get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '4_4' ) );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_4_4' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_4_4' ):
												get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '4_4' ) );
											
											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres' );

											endif;	

										endwhile;
									echo '</div>';//bootstrap col

								endif;//-------------------------------------------------------------
					
									echo '</div>';//bootstrap row
									echo '</div>';//bootstrap container
									echo '</div>';//fundo
					////////////////////////////// Final 3 Colunas ///////////////////////////////

					////////////////////////////// Inicio 1/3 Colunas ///////////////////////////////
					elseif( get_row_layout() == 'fx_linha_coluna_1b3' ):
								//Personalização da coluna
								$background = get_sub_field('fx_fundo_da_coluna_1_1');
								$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
								$link = get_sub_field('fx_cor_do_link_coluna_1_1');
								$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
								
								echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
								if($contentClass){
									echo '<div class="container p-0">';//bootstrap container
								} else {
									echo '<div class="container">';//bootstrap container
								}
								echo '<div class="row">';//bootstrap row
								//conteudo flexivel 2 colunas esquerda
								if( have_rows('fx_coluna_1_1b3') ):

									echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_1_1b3') ): the_row();
											
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_1_2' ):								
												get_template_part( 'construtor/construtor', 'titulo_3b_1' );
											
											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_1_2' ): 
												get_template_part( 'construtor/construtor', 'editor_3b_1' );

											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_2' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '1_2' ) );
											
											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_1_2' ):
												get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '1_2', 'size' => '1' ) );
											
											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_1_2' ): 
												get_template_part( 'construtor/construtor', 'video', array( 'key' => '1_2' ) );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_2' ):
												get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '1_2' ) );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_1_2' ):
												get_template_part( 'construtor/construtor', 'slide_timer' );
											
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_1_2' ): 
												get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '1_2' ) );
											
											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_1_2' ): 		
												get_template_part( 'construtor/construtor', 'abas', array( 'key' => '1_2' ) );
											
											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_1_2' ): 		
												get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '1_2' ) );
											
											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_1_2' ): 
												get_template_part( 'construtor/construtor', 'divisor' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_1_2' ): 
												get_template_part( 'construtor/construtor', 'botao', array( 'key' => '1_2' ) );

											// Acesso Rapido								
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_2' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '1_2' ) );

											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_2' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas' );

											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_1_2' ):
												get_template_part( 'construtor/construtor', 'contato_individual' );

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_2' ):
												get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '1_2' ) );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_2' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_2' ):
												get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '1_2' ) );

											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres' );

											endif;

										endwhile;
									echo '</div>';//bootstrap col

								endif;//-------------------------------------------------------------
					
								//conteudo flexivel 2 colunas direita
								if( have_rows('fx_coluna_2_1b3') ):
									echo '<div class="col-sm-8 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_2_1b3') ): the_row();
											
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_2_2' ):
												get_template_part( 'construtor/construtor', 'titulo_1b_3' );

											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_2_2' ): 
												get_template_part( 'construtor/construtor', 'editor_1b_3' );

											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_2_2' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '2_2' ) );
											
											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_2_2' ):
												get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '2_2' ) );
											
											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_2_2' ): 
												get_template_part( 'construtor/construtor', 'video', array( 'key' => '2_2' ) );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_2_2' ):
												get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '2_2' ) );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_2_2' ):
												get_template_part( 'construtor/construtor', 'slide_timer' );
											
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_2_2' ): 
												get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '2_2' ) );
											
											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_2_2' ): 		
												get_template_part( 'construtor/construtor', 'abas', array( 'key' => '2_2' ) );
											
											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_2_2' ): 		
												get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '2_2' ) );
											
											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_2_2' ): 
												get_template_part( 'construtor/construtor', 'divisor' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_2_2' ): 
												get_template_part( 'construtor/construtor', 'botao', array( 'key' => '2_2' ) );
											
											// Acesso Rapido								
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_2_2' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '2_2' ) );

											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_2_2' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas' );

											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_2_2' ):
												get_template_part( 'construtor/construtor', 'contato_individual' );

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_2_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_2_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_2_2' ):
												get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '2_2' ) );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_2_2' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_2_2' ):
												get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '2_2' ) );

											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres' );

											endif;

										endwhile;
									echo '</div>';//bootstrap col

								endif;//-------------------------------------------------------------
					
								echo '</div>';//bootstrap row
								echo '</div>';//bootstrap container
								echo '</div>';//fundo
					////////////////////////////// Final 1/3 Colunas ///////////////////////////////

					////////////////////////////// Inicio 3/1 Colunas ///////////////////////////////
					elseif( get_row_layout() == 'fx_linha_coluna_3b1' ):
								//Personalização da coluna
								$background = get_sub_field('fx_fundo_da_coluna_1_1');
								$color = get_sub_field('fx_cor_do_texto_coluna_1_1');
								$link = get_sub_field('fx_cor_do_link_coluna_1_1');
								$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
								
								echo '<div class="bg_fx_'.$background['value'].' lk_fx_'.$link['value'].' fx_all">';//fundo e link
								if($contentClass){
									echo '<div class="container p-0">';//bootstrap container
								} else {
									echo '<div class="container">';//bootstrap container
								}
								echo '<div class="row">';//bootstrap row
								//conteudo flexivel 2 colunas esquerda
								if( have_rows('fx_coluna_1_3b1') ):

									echo '<div class="col-sm-8 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_1_3b1') ): the_row();
											
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_1_2' ):
												get_template_part( 'construtor/construtor', 'titulo_1_3b' );
											
											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_1_2' ): 
												get_template_part( 'construtor/construtor', 'editor_1_3b' );

											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_1_2' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '1_2' ) );
											
											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_1_2' ):
												get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '1_2', 'size' => '' ) );
											
											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_1_2' ): 
												get_template_part( 'construtor/construtor', 'video', array( 'key' => '1_2' ) );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_1_2' ):
												get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '1_2' ) );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_1_2' ):
												get_template_part( 'construtor/construtor', 'slide_timer' );
											
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_1_2' ): 
												get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '1_2' ) );
											
											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_1_2' ): 		
												get_template_part( 'construtor/construtor', 'abas', array( 'key' => '1_2' ) );
											
											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_1_2' ): 		
												get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '1_2' ) );
											
											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_1_2' ): 
												get_template_part( 'construtor/construtor', 'divisor' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_1_2' ): 
												get_template_part( 'construtor/construtor', 'botao', array( 'key' => '1_2' ) );

											// Acesso Rapido								
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_1_2' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '1_2' ) );

											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_1_2' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas' );

											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_1_2' ):
												get_template_part( 'construtor/construtor', 'contato_individual' );

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_1_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_1_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_1_2' ):
												get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '1_2' ) );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_1_2' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_1_2' ):
												get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '1_2' ) );

											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres' );

											endif;

										endwhile;
									echo '</div>';//bootstrap col

								endif;//-------------------------------------------------------------
					
								//conteudo flexivel 2 colunas direita
								if( have_rows('fx_coluna_2_3b1') ):
									echo '<div class="col-sm-4 tx_fx_'.$color['value'].'  mt-3 col-bt-'.$colorbtn['value']. ' ' . $contentClass . '">';//bootstrap col
										while( have_rows('fx_coluna_2_3b1') ): the_row();
											
											//titulo
											if( get_row_layout() == 'fx_cl1_titulo_2_2' ):
												get_template_part( 'construtor/construtor', 'titulo_3_1b' );
											
											//editor Wysiwyg
											elseif( get_row_layout() == 'fx_cl1_editor_2_2' ): 
												get_template_part( 'construtor/construtor', 'editor_3_1b' );

											//editor Wysiwyg com fundo
											elseif( get_row_layout() == 'fx_cl1_editor_fundo_2_2' ): 
												get_template_part( 'construtor/construtor', 'editor_fundo', array( 'key' => '2_2' ) );
											
											//Loops noticias por categorias
											elseif( get_row_layout() == 'fx_cl1_noticias_2_2' ):
												get_template_part( 'construtor/construtor', 'noticias', array( 'key' => '2_2', 'size' => '1' ) );

											//Video Responsivo
											elseif( get_row_layout() == 'fx_cl1_video_2_2' ): 
												get_template_part( 'construtor/construtor', 'video', array( 'key' => '2_2' ) );

											// Slide de Noticias
											elseif( get_row_layout() == 'fx_cl1_slide_noticias_2_2' ):
												get_template_part( 'construtor/construtor', 'slide_noticias', array( 'key' => '2_2' ) );

											// Slide de Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_slide_timer_2_2' ):
												get_template_part( 'construtor/construtor', 'slide_timer' );
											
											//imagem responsiva
											elseif( get_row_layout() == 'fx_cl1_imagem_2_2' ): 
												get_template_part( 'construtor/construtor', 'imagem', array( 'key' => '2_2' ) );
											
											//abas
											elseif( get_row_layout() == 'fx_cl1_abas_2_2' ): 		
												get_template_part( 'construtor/construtor', 'abas', array( 'key' => '2_2' ) );
											
											//Sanfona
											elseif( get_row_layout() == 'fx_cl1_sanfona_2_2' ): 		
												get_template_part( 'construtor/construtor', 'sanfona', array( 'key' => '2_2' ) );
											
											//Divisor
											elseif( get_row_layout() == 'fx_fl1_divisor_2_2' ): 
												get_template_part( 'construtor/construtor', 'divisor' );
											
											//botão centralizado
											elseif( get_row_layout() == 'fx_fl1_botao_2_2' ): 
												get_template_part( 'construtor/construtor', 'botao', array( 'key' => '2_2' ) );

											// Acesso Rapido								
											elseif( get_row_layout() == 'fx_cl1_acesso_rapido_2_2' ):									
												get_template_part( 'construtor/construtor', 'acesso_rapido', array( 'key' => '2_2' ) );

											//Contatos em camadas
											elseif( get_row_layout() == 'fx_cl1_contato_camadas_2_2' ): 
												get_template_part( 'construtor/construtor', 'contato_camadas' );

											//Contato Individual
											elseif( get_row_layout() == 'fx_cl1_contato_individual_2_2' ):
												get_template_part( 'construtor/construtor', 'contato_individual' );

											// Bloco Noticias
											elseif( get_row_layout() == 'fx_cl1_bloco_noticias_2_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias' );

											// Bloco Noticias - Timer
											elseif( get_row_layout() == 'fx_cl1_bloco_timer_2_2' ):
												get_template_part( 'construtor/construtor', 'bloco_noticias_timer' );

											// Bloco Redes Sociais
											elseif( get_row_layout() == 'fx_fl1_bloco_rede_2_2' ):
												get_template_part( 'construtor/construtor', 'bloco_rede', array( 'key' => '2_2' ) );

											// Noticias em destaque
											elseif( get_row_layout() == 'fx_cl1_noticias_destaque_2_2' ):
												get_template_part( 'construtor/construtor', 'noticias_destaque' );

											// Noticias mais lidas
											elseif( get_row_layout() == 'fx_cl1_mais_lidas_2_2' ):
												get_template_part( 'construtor/construtor', 'mais_lidas', array( 'key' => '2_2' ) );

											// Outras Noticias
											elseif( get_row_layout() == 'fx_cl1_outras_noticias' ):
												get_template_part( 'construtor/construtor', 'outras_noticias' );

											// Noticias DREs
											elseif( get_row_layout() == 'fx_cl1_noticias_dres' ):
												get_template_part( 'construtor/construtor', 'noticias_dres' );

											endif;

										endwhile;
									echo '</div>';//bootstrap col

								endif;//-------------------------------------------------------------
					
									echo '</div>';//bootstrap row
									echo '</div>';//bootstrap container
									echo '</div>';//fundo
					////////////////////////////// Final 1/3 Colunas ///////////////////////////////


					endif;
				endwhile;
			endif;
		
?>		</div>
	</div>
	
</div>
		<?php
	}
}