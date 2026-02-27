<?php
namespace Classes\TemplateHierarchy\LoopNoticias;


class LoopNoticiasMaisRecentes extends LoopNoticias
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
		$link = get_field('pag_noticias', 'conf-lateral');
		echo '<div class="col-lg-4 col-sm-12 news-recents">';
			echo '<div class="recados-destaques noticias-recentes">';
				echo '<div class="recados-title d-flex justify-content-between align-items-center">';
					echo '<h3>MAIS RECENTES</h3>';
					if($link){
						echo '<a href="' . get_permalink($link) . '">Ver todos</a>';
					}
				echo '</div>';

				
				$current_date = date('Ymd');
				$args = array(
					'post_type' => 'noticia',
					'posts_per_page' => 5,
					'post__not_in' => array($this->id_post_atual),
					'ignore_sticky_posts' => 1,
				);

				$the_query = new \WP_Query( $args );

				// The Loop.
				if ( $the_query->have_posts() ) {
					
					while ( $the_query->have_posts() ) {
						$the_query->the_post();

						?>
							<div class="recado">
								<div class="row">
									<div class="col-3 pr-0">
										<?php $imagem = get_thumb(get_the_ID(), 'thumbnail'); ?>
										<img src="<?= $imagem[0]; ?>" class="img-fluid rounded" alt="<?= $imagem[1]; ?>">
									</div>
									<div class="col-9">										
										<p class="data"><?= getDay(get_the_date('w')); ?>, <?= get_the_date('M d') ?> às <?= get_the_date('H\hi\m\i\n') ?> 
											<?php 
												$term_obj_list = get_the_terms( get_the_ID(), 'category' );
												$i = 0;
												if($term_obj_list){
													echo " - em ";
													foreach($term_obj_list as $categoria){
														if($i == 0){
															echo "<span>" . $categoria->name . "</span>";
															//echo "<a href='" . $urlPage . "?categoria=" . $categoria->slug . "'>" . $categoria->name . "</a>";
														} else {
															echo ", <span>" . $categoria->name . "</span>";
															//echo ", <a href='" . $urlPage . "?categoria=" . $categoria->slug . "'>" . $categoria->name . "</a>";
														}
														$i++;
													}                                        
												}
											?>
										</p>										
										<a href="<?= get_the_permalink(); ?>" class="link-modal"><h2><?= get_the_title(); ?></h2></a>

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
										</div>
															
									</div>                    
								</div>
								<hr>					              
							</div>
						<?php						
					}
					
				} else {
					esc_html_e( 'Sorry, no posts matched your criteria.' );
				}
				// Restore original Post Data.
				wp_reset_postdata();
				
			echo '</div>';
		echo '</div>';
		wp_reset_query();
	}

	public function getCategory($id_post){
		$categoria = get_the_category($id_post);
		return $categoria[0]->name;
	}

}