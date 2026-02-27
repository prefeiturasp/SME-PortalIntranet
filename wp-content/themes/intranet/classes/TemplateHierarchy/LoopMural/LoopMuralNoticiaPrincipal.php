<?php
namespace Classes\TemplateHierarchy\LoopMural;
class LoopMuralNoticiaPrincipal extends LoopMural
{
	public function __construct()
	{
		$this->init();
	}
	public function init()
	{
		$this->montaHtmlNoticiaPrincipal();
	}
	public function montaHtmlNoticiaPrincipal(){
		if (have_posts()):
			while (have_posts()): the_post();
				echo '<div class="container mt-5">';
				echo '<article class="col-12 content-article content-explica content-mural">';
				$this->getDataPublicacaoAlteracao();
				echo '<h2 class="titulo-noticia-principal mb-3" id="'.get_post_field( 'post_name', get_post() ).'">'.get_the_title().'</h2>';
						
				echo "<hr><br>";

				$image = get_the_post_thumbnail( $post_id, 'img-dest', array( 'class' => 'img-fluid mb-4 d-block mx-auto my-0' ) );
				echo $image;
				
				
				the_content();
				
				$link = get_field('link', $post_id);
				if($link)
					echo "<p><a href='" . $link . "'>Acesse o link para mais informações</a></p>";				
				
				$this->includeCarosel();
				echo "<hr><br>";
				$this->getMidiasSociais();
				//$this->getArquivosAnexos();
				//$this->getCategorias(get_the_ID());
				
				echo '</article>';
				echo '<div>'; // container
			endwhile;
		endif;
		wp_reset_query();
	}
	public function getDataPublicacaoAlteracao(){
		//padrão de horario G\hi
		echo '<p class="data">Publicado em: ' . getDay(get_the_date('w')) . ', ' . get_the_date('M d') . ' às ' . get_the_date('H\hi\m\i\n');
		$nome = get_field('nome');
		if($nome){
			echo ' por: ' . $nome;
		} else {
			echo ' por: ' . get_the_author();
		}
		
		echo '</p>';
	}

	public function includeCarosel(){
		
		$galeria = get_field('galeria');

		//echo "<pre>";
		//print_r($galeria);
		//echo "</pre>";

		if($galeria && $galeria != '' ){
			wp_enqueue_style('slick_css');
			wp_enqueue_style('slick_theme_css');		
			wp_enqueue_script('slick_min_js');
			wp_enqueue_script('slick_func_js');
			wp_enqueue_script('lightbox_js');

			echo '<section class="regular slider">';
				foreach($galeria as $imagem){
					echo '<div>';
						echo '<a href="' . $imagem['url'] . '" rel="lightbox"><img src="' . $imagem['sizes']['default-image'] . '" alt="' . $imagem['alt'] . '"></a>';
					echo '</div>';
				}
			echo '</section>';
		}
		
	}

	public function getMidiasSociais(){		

		echo '<div class="d-flex justify-content-between">';
			echo '<div class="likes">';
			
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
			
				$likes = '';
				
				if($l == 1){
					$likes = 'likes';
				}

				if($total_like1 == 1){
					$text_total = 'like';
				} else {
					$text_total = 'likes';
				}

				echo '<div class="post_like">';
					echo '<a class="pp_like ' . $likes . '" id="pp_like_' . get_the_id() . '" href="#" data-id="' . get_the_id() . '"><i class="fa fa-heart" aria-hidden="true"></i></i> <span>' . $total_like1 . ' ' . $text_total . '</span></a>';
				echo '</div>';
				
			echo '</div>';
			echo '<div class="link">';
				/*Utilizando as classes de personalização do Plugin AddToAny*/
				echo do_shortcode('[addtoany]');
			echo '</div>';
		echo '</div>';
	}
	public function getArquivosAnexos(){
		$unsupported_mimes  = array( 'image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/tiff', 'image/x-icon' );
		$all_mimes          = get_allowed_mime_types();
		$accepted_mimes     = array_diff( $all_mimes, $unsupported_mimes );

		$attachments = get_posts( array(
			'post_type' => 'attachment',
			'post_mime_type'    => $accepted_mimes,
			'posts_per_page' => -1,
			'post_parent' => get_the_ID(),
			'orderby'	=> 'ID',
			'order'	=> 'ASC',
			'exclude'     => get_post_thumbnail_id()
		) );
		if ( $attachments ) {
			echo '<section id="arquivos-anexos">';
			echo '<h2>Arquivos Anexos</h2>';
			foreach ( $attachments as $attachment ) {
				echo '<article>';
				echo '<p><a target="_blank" style="font-size:26px" href="'.$attachment->guid.'"><i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i> Ir para '. $attachment->post_title.'</a></p>';
				echo '<article>';
			}
			echo '</section>';
		}
	}
	public function getCategorias($id_post){
		$categorias = get_the_category($id_post);
		foreach ($categorias as $categoria){
			$category_link = get_category_link( $categoria->term_id );
			echo '<a href="'.$category_link.'"><span class="badge badge-pill badge-light border p-2 m-2 font-weight-normal">ir para '.$categoria->name.'</span></a>';
		}
	}
}
