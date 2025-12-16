<?php

namespace Classes\TemplateHierarchy\LoopMural;


class LoopMuralRelacionadas extends LoopMural
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
			'post_type' => 'mural-professores',
			'posts_per_page' => 3,
			'post__not_in' => array($this->id_post_atual),
		);

		$active_rels = get_field('select_rel');
		$relacionados = get_field('relacionados');

		if($active_rels && $relacionados[0] != ''){
			$args['post__in'] = $relacionados;
		}

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ) :
		
			echo '<div class="col-12 rel-infos rel-profs mt-5 px-0 pb-4" id="outrasNoticias">';
				echo '<div class="rel-title rel-mural d-flex justify-content-between align-items-center">
						<h2>Você também pode gostar</h2>
						<a href="' . get_home_url() . '/sme-explica">Ver mais</a>
					</div>
				';
					echo "<div class='row'>";
					while ( $the_query->have_posts() ) : $the_query->the_post();						  
					?>
						
						<div class="col-12 col-md-4 mb-4">
                            <div class="mural sme-informe p-0 d-flex">
                                <div class="row m-0">
                                    <div class="col-12 img-column mb-3 p-0">
                                        <?php 
                                            $image = get_the_post_thumbnail( $post_id, 'default-image', array( 'class' => 'img-fluid' ) );
                                        ?>
                                        <?php if($image): ?>
                                            <?= $image; ?>
                                        <?php else: ?>
                                            <img src="<?= get_template_directory_uri(); ?>/img/categ-destaques.jpg" class="img-fluid rounded" alt="Imagem de ilustração categoria">
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-12">
                                        <p class="data">
                                            <?= get_the_date('d/m/Y') ?> às <?= get_the_date('H\hi\m\i\n') ?>
                                        </p>
                                    </div>

                                    <div class="col-12 col-md-9">
                                        
                                        <h2><a href="<?= get_the_permalink(); ?>"><?= get_the_title(); ?></a></h2>
                                                                                                 
                                        <div class="d-flex justify-content-between">
                                            
                                            <div class="autor">
                                                <?php
													$nome = get_field('nome');
													if($nome){
														echo 'Por: ' . $nome;
													} else {
														echo 'Por: ' . get_the_author();
													}
												?>
                                            </div>
                                        </div>
                                                            
                                    </div>

                                    <div class="col-12 col-md-3">
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
                                                <a class="pp_like <?php if($l==1) {echo "likes"; } ?>" id="pp_like_<?php echo get_the_id(); ?>" href="#" data-id="<?php echo get_the_id(); ?>"><span><?php echo $total_like1; ?> <?php echo $total_like1 == 1 ? 'like' : 'likes'; ?></span><br><i class="fa fa-heart" aria-hidden="true"></i></a>	
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                
                            </div>
                        </div>
						
					<?php
					endwhile;
					echo '</div>';
				
			echo '</div>';
		
		
			wp_reset_postdata();

		endif;
		
	}

}