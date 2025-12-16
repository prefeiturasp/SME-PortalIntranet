<?php

namespace Classes\TemplateHierarchy\LoopExplica;


class LoopExplicaRelacionadas extends LoopExplica
{
	private $id_post_atual;
	protected $args_relacionadas;
	protected $query_relacionadas;

	public function __construct($id_post_atual)
	{
		$this->id_post_atual = $id_post_atual;
		//$this->init();
		$this->my_related_posts();
	}
	
	public function my_related_posts() {
		// the query
		$args = array(
			'post_type' => 'info-sme-explica',
			'posts_per_page' => 3,
			'post__not_in' => array($this->id_post_atual),
		);
		
		$categorias = get_the_terms($this->id_post_atual, 'categorias-explica');
		$categFiltro = array();

		if($categorias && $categorias != ''){

			foreach($categorias as $categoria){
				$categFiltro[] = $categoria->term_id;
			}			

			$args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'categorias-explica',
					'field' => 'term_id',
					'terms' => $categFiltro,
				)
			);
		}

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ) :
		
			echo '<div class="col-12 rel-infos mt-5 px-0 pb-4" id="outrasNoticias">';
				echo '<div class="rel-title d-flex justify-content-between align-items-center">
						<h2>Você também pode gostar</h2>
						<a href="' . get_home_url() . '/sme-explica">Ver mais</a>
					</div>
				';
				
					while ( $the_query->have_posts() ) : $the_query->the_post();						  
					?>
						
						<div class="recado sme-informe mx-5 <?= $this->id_post_atual; ?>">
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
						
					<?php
					endwhile;
					
				
			echo '</div>';
		
		
			wp_reset_postdata();

		else :

			// the query
			$argsNew = array(
				'post_type' => 'info-sme-explica',
				'posts_per_page' => 3,
				'post__not_in' => array($this->id_post_atual),
			);

			$the_query2 = new \WP_Query( $argsNew );

			if ( $the_query2->have_posts() ) :
		
				echo '<div class="col-12 rel-infos mt-5 px-0 pb-4" id="outrasNoticias">';
					echo '<div class="rel-title d-flex justify-content-between align-items-center">
							<h2>Você também pode gostar</h2>
							<a href="' . get_home_url() . '/index.php/sme-explica/">Ver mais</a>
						</div>
					';
					
						while ( $the_query2->have_posts() ) : $the_query2->the_post();						  
						?>
							
							<div class="recado sme-informe mx-5 <?= $this->id_post_atual; ?>">
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
							
						<?php
						endwhile;
						
					
				echo '</div>';
			
			
				wp_reset_postdata();

			endif; // query2

		endif;
		
	}
	

}