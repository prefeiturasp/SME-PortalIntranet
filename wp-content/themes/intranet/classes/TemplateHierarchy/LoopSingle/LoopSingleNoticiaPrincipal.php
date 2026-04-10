<?php
namespace Classes\TemplateHierarchy\LoopSingle;
class LoopSingleNoticiaPrincipal extends LoopSingle
{
	private $tipo_evento;
	private $form_inscricao;

	public function __construct()
	{
		// Tipo do evento (datas, periodo, premiacao)
		$this->tipo_evento = get_field( 'tipo_evento', get_the_ID() );
		$this->form_inscricao = new LoopSingleFormInscricao();
		
		$this->init();
	}
	public function init()
	{
		$this->montaHtmlNoticiaPrincipal();
	}

	public function montaHtmlNoticiaPrincipal(){
		if (have_posts()):
			while (have_posts()): the_post();
			echo "<article class='col-12 col-lg-8 content-article content-explica news-content content-sorteio order-1' data-tipo-evento='{$this->tipo_evento}'>";

			$current_date = date('Ymd');

			// Obtendo o valor da data de encerramento
			$enc_inscri = get_field('enc_inscri');

			// Verificando se a data de encerramento é menor que a data atual
			$status_prefix = ($enc_inscri < $current_date) ? '<div class="overlay-encerrado"></div>' : '';
			$texto_subtitulo = ($enc_inscri < $current_date) ? 'Sorteio' : 'Sorteio será realizado';

			echo '<div class="infos-topo-noticia">';

				echo '<div class="row">';
					echo '<div class="col-10">';
						echo '<h2 class="titulo-noticia-principal mb-3" id="'.get_post_field( 'post_name', get_post() ).'">' . get_the_title().'</h2>';

						$dataSorteio = ($enc_inscri < $current_date) ? obter_ultima_data_sorteio( get_the_ID() ) : obter_proxima_data_sorteio( get_the_ID() );
						if($dataSorteio){
							echo '<h3>' . $texto_subtitulo . ' ' . $dataSorteio . '.</h3>';	
						}
					echo '</div>';
					echo '<div class="col-2 pl-0">';
						$this->getPostLikes();
					echo '</div>';
				echo '</div>';
				$this->getDataPublicacaoAlteracao();
				
				
				$image = get_the_post_thumbnail( get_the_ID(), 'default-image', array( 'class' => 'img-fluid mx-auto' ) );
				if($image) :
					echo '<div class="image-wrapper">';
						echo $image;
						echo $status_prefix;
					echo '</div>';
				else :
					$image = get_field( 'sorteios_cortesias_placeholder', 'options' );
					if($image)
						echo '<div class="image-wrapper">';
							echo '<img src="' . $image . '" class="img-fluid mx-auto" alt="Logo da Secretaria Municipal de Educação de São Paulo">';
							echo $status_prefix;
						echo '</div>';
				endif;			

			echo '</div>';

			echo '<div class="infos-noticia">';

				

				$content = get_the_content();
				if (trim(wp_strip_all_tags($content)) !== '') {
					echo "<hr><br>";
					the_content();	
				}

				$this->getInfoVisita();
				
				// Formulário de inscrição
				echo '<div class="col-12 mt-5 p-0 order-3" id="form-wrapper">';
					$this->form_inscricao->getFormInscri();
				echo '</div>';

			echo '</div>';

			echo '</article>';
			endwhile;
		endif;
		wp_reset_query();
	}
	public function getDataPublicacaoAlteracao(){
		//padrão de horario G\hi
		echo '<p class="data">Publicado em: '.get_the_date('d/m/Y G\hi').' | Atualizado em: '.get_the_modified_date('d/m/Y');
		 
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
		
		echo '</p>';
	}

	public function getInfoVisita(){		

		$regras_info = get_field('regras_info');

		if($regras_info){
			echo '<p class="title-info">Informações importantes:</p>';
			echo $regras_info;
		}

		//EXIBE LISTA DE CONTEMPLADOS DO SORTEIO
		echo do_shortcode('[exibe_tab_resultado_pagina]');
		
	}

	public function getPostLikes(){
		echo '<div class="d-flex justify-content-end align-items-center">';
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
					echo '<a class="pp_like ' . $likes . '" id="pp_like_' . get_the_id() . '" href="#" data-id="' . get_the_id() . '"><div class="icon-like"></div><span>' . $total_like1 . ' ' . $text_total . '</span></a>';
				echo '</div>';
				
			echo '</div>';			
		echo '</div>';
	}
	
	public function getCategorias($id_post){
		$categorias = get_the_category($id_post);
		foreach ($categorias as $categoria){
			$category_link = get_category_link( $categoria->term_id );
			echo '<a href="'.$category_link.'"><span class="badge badge-pill badge-light border p-2 m-2 font-weight-normal">ir para '.$categoria->name.'</span></a>';
		}
	}
}
