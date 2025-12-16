<?php

namespace Classes\TemplateHierarchy\LoopCortesias;


class LoopCortesiasRelacionadas extends LoopCortesias
{
	private $id_post_atual;
	protected $args_relacionadas;
	protected $query_relacionadas;

	public function __construct($id_post_atual)
	{
		$this->id_post_atual = $id_post_atual;
		$this->init();
		//$this->my_related_posts();
	}

	public function init(){
		echo '<div class="col-12 mt-5 mb-3 news-comment">
			<div class="rel-infos pb-3">
				<div class="rel-title d-flex justify-content-between align-items-center">
					<h2>Comentários</h2>
				</div>';
				comments_template();
			echo '</div>';
		echo '</div>';
	}
	
	public function getComplementosRelacionadas($id_post){
		$dt_post = get_the_date('d/m/Y g\hi');
		$categoria = get_the_category($id_post)[0]->name;

		return '<p class="fonte-doze font-italic mb-0">Publicado em: '.$dt_post.' - em '.$categoria.'</p>';


	}
	
	public function my_related_posts() {
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args = array(
			'posts_per_page' => 5,
			'post_in' => get_the_tag_list(),
			'paged' => $paged
		);
		
		$the_query = new \WP_Query( $args );
		echo '<div class="container">';
		echo '<div class="row mt-4">';
		echo '<div class="col-sm-8" id="outrasNoticias"><h2>Relacionadas</h2>';
		echo '<div class="row">';
		while ( $the_query->have_posts() ) : $the_query->the_post();

			// Busca a imagem destaca / primeira imagem / imagem padrao -- functions.php
			$thumbs = get_thumb(get_the_ID());   
		?>
			
			<div class="col-sm-4 mb-4">
				<img src="<?php echo $thumbs[0]; ?>" alt="<?php echo $thumbs[1]; ?>" class="img-fluid rounded">			
			</div>
			<div class="col-sm-8 mb-4">
				<h3 class="fonte-dezoito font-weight-bold mb-2 aaa">
					<a class="text-decoration-none text-dark" href="<?php echo get_the_permalink(get_the_ID()); ?>">
						<?php the_title(); ?>
					</a>
				</h3>
				<?php
				echo $this->getSubtitulo($query->ID, 'p', 'fonte-dezesseis mb-2')
				?>
				<?= $this->getComplementosRelacionadas($query->ID); ?>
				
			</div>
			
		<?php
		endwhile;
			
		echo '</div></div></div></div>';
		
		wp_reset_postdata();
		
		?>
		<div class="paginacao-atual">
			<?php
			echo paginate_links( array(
				'base' => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
				'current' => $paged,
				'total'   => $the_query->max_num_pages,
				'end_size'  => 1,
				'mid_size'  => 2,
				'show_all' => false,
				'prev_next' => true,
				'prev_text' => __('<<'),
				'next_text' => __('>>'),
				'add_fragment' => '#outrasNoticias'
			) );
			?>
		</div>
		<?php
		
		//paginacao2($the_query);
	}
	

}