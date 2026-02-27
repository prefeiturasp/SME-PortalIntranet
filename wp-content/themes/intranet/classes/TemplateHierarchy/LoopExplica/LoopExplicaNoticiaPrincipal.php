<?php
namespace Classes\TemplateHierarchy\LoopExplica;
class LoopExplicaNoticiaPrincipal extends LoopExplica
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
				echo '<article class="col-12 content-article content-explica">';
				$this->getDataPublicacaoAlteracao();
				echo '<h2 class="titulo-noticia-principal mb-3" id="'.get_post_field( 'post_name', get_post() ).'">'.get_the_title().'</h2>';
				//echo $this->getSubtitulo(get_the_ID(), 'h3');
				echo '<h3 class="sub-titulo">';
					if(get_field('insira_o_subtitulo', get_the_ID()) != ''){
						the_field('insira_o_subtitulo', get_the_ID());
					}else if (get_field('insira_o_subtitulo', get_the_ID()) == ''){
						 echo get_the_excerpt(get_the_ID()); 
					}
				echo '</h3>';				
				echo "<hr><br>";

				$destaque = get_field('habilitar_destaque');
				$tipo = get_field('tipo_de_destaque');

				$image = get_the_post_thumbnail( $post_id, 'img-dest', array( 'class' => 'img-fluid mb-4 d-block mx-auto my-0' ) );
				if( $image && $tipo != 'video' && !$destaque ) :
					echo $image;
				elseif($image && $tipo == 'audio' && $destaque):
					echo $image;
				elseif($image && !$destaque):
					echo $image;
				endif;
				
				
				if($tipo == 'audio' && $destaque && $destaque){
					$audio = get_field('audio');
				}

				if($tipo == 'audio' && $audio != '' && $destaque){
					echo do_shortcode('[audio mp3=' . $audio . ']');
				}

				if($tipo == 'video' && $destaque){
					$tipo_video = get_field('tipo_de_video', $informativo);
        			$video_file = get_field('video', $informativo);
        			$video_embed = get_field('video_embed', $informativo);
					$video_format = pathinfo($video_file, PATHINFO_EXTENSION);

					if($tipo_video && $video_file != ''){
						echo do_shortcode( '[video ' . $video_format . '=' . $video_file . ']' );
					} elseif($video_embed) {
						echo '<div class="video-container">';
							echo $video_embed;
						echo '</div>';
					}
				}
				
				the_content();				
				$this->includeCarosel();
				$this->getMidiasSociais();
				//$this->getArquivosAnexos();
				//$this->getCategorias(get_the_ID());
				the_tags( '<div class="custom-tags-noticias">', '', '</div>' );
				echo '</article>';
				echo '<div>'; // container
			endwhile;
		endif;
		wp_reset_query();
	}
	public function getDataPublicacaoAlteracao(){
		//padrão de horario G\hi
		echo '<p class="data">Publicado em: ' . getDay(get_the_date('w')) . ', ' . get_the_date('M d') . ' às ' . get_the_date('H\hi\m\i\n');
		 
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
		
		echo '</p>';
	}

	public function includeCarosel(){

		$showGaleria = get_field('habilitar_galeria');
		$galeria = get_field('galeria');

		if($galeria && $galeria != '' && $showGaleria){
			wp_enqueue_style('slick_css');
			wp_enqueue_style('slick_theme_css');		
			wp_enqueue_script('slick_min_js');
			wp_enqueue_script('slick_func_js');
			wp_enqueue_script('lightbox_js');

			echo '<section class="regular slider">';
				foreach($galeria as $imagem){
					echo '<div>';
						echo '<a href="' . $imagem . '" rel="lightbox"><img src="' . $imagem . '"></a>';
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
