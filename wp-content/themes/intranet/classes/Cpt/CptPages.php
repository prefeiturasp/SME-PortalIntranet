<?php

namespace Classes\Cpt;


class CptPages extends Cpt
{
	public function __construct()
	{
		//add_filter( 'manage_pages_columns' , array($this, 'exibe_cols' ));
		add_filter('manage_pages_columns', array($this, 'exibe_cols_pages'), 10, 2);
		add_action( 'manage_pages_custom_column' , array($this, 'cols_content_pages'), 10, 2 );
	}

	// add featured thumbnail to admin post columns
	public function exibe_cols_pages($cols) {
		
		if( current_user_can('editor') || current_user_can('administrator') ) {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => 'Title',
				'author' => 'Author',
				'modified' => 'Modificado por',
				'featured_thumb' => 'Thumbnail',
				'modelo' => 'Modelo',
				//'grupo' => 'Grupo',
				'date' => 'Date',	
			);
		} else {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => 'Title',
				'author' => 'Author',
				'featured_thumb' => 'Thumbnail',
				'date' => 'Date',	
			);
		}		
		
		return $columns;
	}

	public function cols_content_pages($column) {	
		
		

		switch ( $column ) {
			case 'featured_thumb':
				echo '<a href="' . get_edit_post_link() . '">';
				echo the_post_thumbnail( 'admin-list-thumb' );
				echo '</a>';
				break;

			case 'grupo':
				$localizacao = get_the_ID();

				$paginas = get_posts(array(
					'post_type' => 'editores_portal',
					'orderby' => 'title',
    				'order'   => 'ASC',
					'post_status'    => 'publish',
					'meta_query' => array(
						array(
							'key' => 'selecionar_paginas', // name of custom field
							'value' => '"' . $localizacao . '"', // matches exaclty "123", not just 123. This prevents a match for "1234"
							'compare' => 'LIKE'
						)
					)
				));

				
				if($paginas && $paginas != ''){
					$a = 0;
					foreach($paginas as $pagina){
						if($a == 0){
							echo "<a href='" . admin_url('edit.php?post_type=page&filter=grupo&grupo_id=' . $pagina->ID) . "'>" . get_the_title($pagina->ID) . "</a>";
							
						} else {
							echo ", <a href='" . admin_url('edit.php?post_type=page&filter=grupo&grupo_id=' . $pagina->ID) . "'>" . get_the_title($pagina->ID) . "</a>";
						}
						
						$a++;
					}
				} else {
					if($_GET['grupo_id'] && $_GET['grupo_id'] != ''){
						echo "<a href='https://educacao.sme.prefeitura.sp.gov.br/wp-admin/edit.php?post_type=page&filter=grupo&grupo_id=" . $_GET['grupo_id'] . "'>". get_the_title($_GET['grupo_id']) . "</a>";
					}
				}
				
				break;

			case 'modified':
				
				$last_id = get_post_meta( get_the_ID(), '_edit_last', true );							
				echo "<a href='" . get_home_url() . "/wp-admin/user-edit.php?user_id=" . $last_id . "'>" . get_the_modified_author() . "</a>";
				
				//echo "Aqui: " . $localizacao . "<br>";
				//print_r($posts);
				break;

				//echo "Aqui: " . $localizacao . "<br>";
				//print_r($posts);
				//break;

			case 'modelo':
				$template_path = get_post_meta(get_the_ID(), '_wp_page_template', true);
				$templates = wp_get_theme()->get_page_templates();
				echo $templates[$template_path];
				if(!$templates[$template_path]){
					echo "Modelo Padrão";
				}
				break;

		}
	}


}