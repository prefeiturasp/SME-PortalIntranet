<?php
namespace Classes\TemplateHierarchy\LoopSingle;


class LoopSingleMaisRecentes
{
	private $id_post_atual;
	private $args_mais_recentes;
	private $query_mais_recentes;

	public function __construct($id_post_atual)
	{
		$this->id_post_atual = $id_post_atual;
		$this->init();
	}

	public function init(){		
		$this->montaHtmlMaisRecentes();
	}

	public function montaHtmlMaisRecentes(){		

		wp_register_style('slick', STM_THEME_URL . 'classes/assets/css/slick.css', null, null, 'all');
		wp_enqueue_style('slick');

		wp_register_style('slick-theme', STM_THEME_URL . 'classes/assets/css/slick-theme.css', null, null, 'all');
		wp_enqueue_style('slick-theme');

		wp_enqueue_script('slick');

		$link = get_field('pag_sorteios', 'conf-lateral');

		echo '<div class="container">';
			echo '<div class="row">';
				echo '<div class="col-lg-12 col-sm-12 news-recents order-5">';
					
					echo '<div class="recentes-title d-flex justify-content-between align-items-center">';
						echo '<h3>Eventos Recentes</h3>';
						echo '<div class="recentes-nav">';
							if($link){
								echo '<a href="' . get_permalink($link) . '">Ver todos</a>';
							}

							echo '<button class="recentes-nav-prev btn"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>';
							echo '<button class="recentes-nav-next btn"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>';
						echo '</div>';
					echo '</div>';

					
					$current_date = date('Ymd');
					$args = array(
						'post_type' => ['post', 'cortesias'],
						'posts_per_page' => 12,
						'post__not_in' => array($this->id_post_atual),
						'ignore_sticky_posts' => 1,
					);

					$the_query = new \WP_Query( $args );

					// The Loop.
					if ( $the_query->have_posts() ) {
						echo '<div class="recent-posts-slider">';
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							$post_type = get_post_type_label( get_the_ID() );

							// Obtendo o valor da data de encerramento
							$enc_inscri = get_field('enc_inscri');

							// Verificando se a data de encerramento é menor que a data atual
							$exibicao = ($enc_inscri < $current_date) ? 'encerrados' : 'ativos';
							$post_type = get_post_type_label( get_the_ID() );
							$tipo_evento = get_field( 'tipo_evento' );
							$local = get_field('local');
							$local_term =  get_term( $local ) ?: false;
							$image = get_the_post_thumbnail_url( get_the_ID(), 'default-image' );
							$image_bg = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );

							?>
								<div class="carrosel-sorteio">
									<div class="item-sorteio item-ativos">
										<div class="row h-100 m-0">
											<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="col-12 col-md-6 p-0 image-wrapper">
												<?php 
													
												?>
												<?php if($image): ?>
													<div class="event-thumbnail">
														<div class="bg" style="background-image: url('<?php echo esc_url( $image_bg ); ?>');"></div>
														<img src="<?php echo esc_url( $image ); ?>" class="img-fluid">
													</div>
												<?php else: ?>
													<div class="event-thumbnail">
														<?php $imagem_padrao = get_field( 'sorteios_cortesias_placeholder', 'options' ); ?>
														
														<div class="bg" style="background-image: url('<?php echo esc_url( $imagem_padrao ); ?>');"></div>
														<img src="<?php echo esc_url( $imagem_padrao ); ?>" class="img-fluid rounded" alt="Imagem de ilustração categoria">
													</div>
												<?php endif; ?>
												<?php if ( $exibicao === 'encerrados' ) : ?>
													<div class="overlay-encerrado"></div>
												<?php endif; ?>
											</a>

											<div class="col-12 col-md-6 mt-md-0 pl-md-2 mt-2 pl-0">
												<div class="row h-100">
													<div class="col-12 col-md-10 d-flex flex-column pr-0">
														<h3><a href="<?= get_the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
														
														<div class="infos-evento my-2">
															<?php if ( $post_type === 'sorteio' ) : ?>
																<p class="data">
																	<?php
																		$dataSorteio = get_field('data_sorteio', get_the_ID());
																		$dataSorteio =  $exibicao === 'encerrados' ? obter_ultima_data_sorteio( get_the_ID(), false ) : obter_proxima_data_sorteio( get_the_ID(), false );
																		if($dataSorteio){
																			$texto_subtitulo = $exibicao === 'encerrados' ? 'Sorteio' : 'Sorteio';
																			echo $texto_subtitulo . ' ' . $dataSorteio;	
																		}
																	?>
																</p>
															<?php endif; ?>
															<?php if ( $post_type === 'cortesias' ) : ?>
																<?php if ( $exibicao === 'encerrados' ) : ?>
																	<p class="data">
																		Evento encerrado. Consulte mais detalhes na notícia
																	</p>
																<?php else : ?>
																	<p class="data">
																		Ingressos gratuitos por ordem de inscrição, enquanto houver disponibilidade
																	</p>
																<?php endif; ?>
															<?php endif; ?>
															<?php if ( $local_term && !is_wp_error( $local_term ) ) : ?>
																<p><strong>Local: </strong><?php echo esc_html( $local_term->name ); ?></p>	
															<?php endif; ?>

															<?php if ( $exibicao != 'encerrados' ) : ?>
																<?php
																if( $tipo_evento == 'premio' ) : ?>
																	<p><strong>Prêmio:</strong> Consulte detalhes</p>
																	<?php
																elseif ($tipo_evento == 'data') :

																	$datas_evento_info = get_field( 'evento_datas' );
																	$datas_evento = wp_list_pluck( $datas_evento_info, 'data' );
																	$datas_disponiveis = filtrar_ordenar_datas_futuras( $datas_evento );

																	if ( !empty( $datas_disponiveis ) ) {
																		$lista_datas = [];
																		$total = count( $datas_disponiveis );
																		$label = _n( 'Data', 'Datas', $total );
																		$format = ( $total > 1 ) ? 'd/m' : 'd/m/Y';
																	}
																	?>
																	<?php if ( !empty( $datas_disponiveis ) ) : ?>
																		<?php
																		foreach ( array_chunk( $datas_disponiveis, 3 )[0] as $data) {
																			$dt = new \DateTime($data);
																			$data = $dt->format( $format );

																			$hora = $dt->format( 'H' );
																			$minuto = $dt->format( 'i' );
																			$hora_fomatada = $minuto == '00' ? "{$hora}h" : "{$hora}h{$minuto}";

																			$data_formatada = "{$data} {$hora_fomatada}";
																			$lista_datas[] = $data_formatada;
																		}
																		?>
																		<p class="datas-disponiveis">
																			<strong><?php echo esc_html( $label ); ?>:</strong>
																			<?php echo esc_html( implode( ' | ', $lista_datas ) ); ?>
																		</p>
																		<?php if ( $total >= 3 ) : ?>
																			<a href="<?php echo esc_url( get_the_permalink() ); ?>">
																				Ver todas as datas e horários
																			</a>
																		<?php endif; ?>
																	<?php endif; ?>
																	<?php
																elseif ($tipo_evento == 'periodo') :
																	$info_periodo_evento = get_field( 'evento_periodo' );
																	?>
																		<p><strong>Periodo: </strong><?php echo esc_html( $info_periodo_evento['descricao'] ); ?></p>
																	<?php
																endif;
																?>
															<?php endif; ?>
														</div>
														<div class="mt-auto d-flex">
															<?php
															if ( $post_type ) : 
																	if($post_type == 'cortesias'){
																		$class_tag = 'cortesia-tag';
																		$label_tag = 'Ordem de Inscrição';
																		$label_icon = 'fa fa-bolt';
																	} else {
																		$class_tag = '';
																		$label_tag = 'Sorteio';
																		$label_icon = 'fa fa-cube';
																	}
																?>
																<span class="post-type-tag <?= $class_tag ?? '' ?> mt-auto">
																	<i class="<?php echo esc_html( $label_icon ); ?>" aria-hidden="true"></i>
																	<?= esc_html( $label_tag ); ?>
																</span>
																<?php
															endif;
															?>

															<?php if ( check_usuario_inscrito_evento( get_the_ID() ) ) : ?>
																<span class="post-type-tag inscricao-tag p-2 ml-2">
																	<i class="fa fa-check-circle" aria-hidden="true"></i> Inscrito
																</span>
															<?php endif; ?>
														</div>
													</div>
																
													<div class="col-12 col-md-2 mt-2 p-0">
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
															<a class="pp_like <?php if($l==1) {echo "likes "; } ?>d-flex flex-column justify-content-center align-items-center" id="pp_like_<?php echo get_the_id(); ?>" href="#" data-id="<?php echo get_the_id(); ?>">
																<img src="<?php echo esc_url( get_template_directory_uri() . '/img/icone-likes.svg' ); ?>" alt="like">
																<span><?php echo $total_like1; ?> <?php echo $total_like1 == 1 ? 'Like' : 'Likes'; ?></span>
															</a>
														</div> 
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php						
						}

						echo '</div>';
						
					} else {
						esc_html_e( 'Sorry, no posts matched your criteria.' );
					}
					// Restore original Post Data.
					wp_reset_postdata();
						
					
				echo '</div>';
			echo '</div>';
		echo '</div>';

		wp_reset_query();
		
	}

	public function getCategory($id_post){
		$categoria = get_the_category($id_post);
		return $categoria[0]->name;
	}

}