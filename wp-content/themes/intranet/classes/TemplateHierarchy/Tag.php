<?php

namespace Classes\TemplateHierarchy;

class PaginaTag{
	
	public function __construct(){
		$this->montaHTMLTags();
	}
	
	public function montaHTMLTags() {
		$term_id        = get_query_var('tag_id');
		$arg           = 'include=' . $term_id;
		$terms          = get_terms( 'post_tag', $arg );
		$get_term_name  = $terms[0]->name;
		$paged = ( get_query_var( 'page' ) ) ?  get_query_var( 'page' ) : 1;
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'taxonomy'	=> 'post_tag',
			'tag__in' => $term_id,
			'posts_per_page' => 12,
			'paged' => $paged,
		);
		$the_query = new \WP_Query( $args );
		?>
		<div class="container mt-4 mb-4">
			<section class="row container-post-categorias">
		<?php
		while ( $the_query->have_posts() ) : $the_query->the_post();
		?>
				<article class='col-12 col-md-4 mt-4 mb-4'>
					<?php if (has_post_thumbnail()) { ?>
						<figure>
							<img alt="<?= $image_alt ?>" class="img-fluid aligncenter img-thumbnail" src="<?= get_the_post_thumbnail_url() ?>"/>
						</figure>
					<?php }else{ ?>
						<figure>
							<img alt="SME Prefeitura" class="img-fluid aligncenter img-thumbnail" src="https://educacao.sme.prefeitura.sp.gov.br/wp-content/uploads/2020/06/placeholder-sme.jpg"/>
						</figure>
					<?php } ?>
					<h3 class="titulo-post-categorias"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<?php the_excerpt(); ?>

				</article>
		<?php
		endwhile;
		?>
		</section>
		</div>	
		<?php	
		wp_reset_postdata();
		?>
		<div class="paginacao-atual">
			<?php
			echo paginate_links( array(
				'base' => @add_query_arg('page','%#%'),
				'current' => $paged,
				'total'   => $the_query->max_num_pages,
				'end_size'  => 1,
				'mid_size'  => 2,
				'show_all' => false,
				'prev_next' => true,
				'prev_text' => __('<<'),
				'next_text' => __('>>'),
			) );
			?>
		</div>
		<?php
	}
	
}
?>