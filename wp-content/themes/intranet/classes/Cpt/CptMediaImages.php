<?php

namespace Classes\Cpt;


class CptMediaImages
{
	private $taxonomy;
	private $cptSlug;
	public function __construct()
	{
		$this->taxonomy = 'categorias-imagem';
		$this->cptSlug = 'attachment';

		// add categories for attachments
		add_action('init', array($this, 'register'));
		//Por padrão, a consulta principal do WordPress não inclui anexos.
		add_action('parse_query', array($this, 'incluirAnexosNaQuery'));

		// Adicionando o Filtro de Categorias, somente possível no modo List
		add_action('restrict_manage_posts', array($this, 'my_restrict_manage_posts'));
	}

	public function register(){

		register_taxonomy( 'categorias-imagem', 'attachment',
			array(
				'labels' =>  array(
					'name'              => 'Categoria de Imagens',
					'singular_name'     => 'Categoria de Imagem',
					'search_items'      => 'Procurar Imagem',
					'all_items'         => 'Todas as Imagens',
					'edit_item'         => 'Editar Imagem',
					'update_item'       => 'Atualizar Imagem',
					'add_new_item'      => 'Nova Categoria de Imagem',
					'new_item_name'     => 'Nova Imagem',
					'menu_name'         => 'Categoria de Imagens',
				),


				'rest_controller_class' => 'WP_REST_Terms_Controller',

				'show_ui' => true,
				'query_var' => true,
				'show_in_rest' => true,
				'hierarchical' => true,
				'sort' => true,
				'show_admin_column' => true,
				'capability_type' => array('imagem','imagens'),
				'map_meta_cap'        => true,
				'capabilities' => array(
					'manage_terms'=>'manage_imagens',
					'edit_terms'=>'edit_imagens',
					'delete_terms'=>'delete_imagens',
					'assign_terms'=>'assign_imagens',
				)
			)
		);

	}

	public function incluirAnexosNaQuery(){
		global $wp_query;
		if ( is_tax( array( $this->taxonomy) ) ) {
			$wp_query->query_vars['post_type'] =  array( $this->cptSlug );
			$wp_query->query_vars['post_status'] =  array( null );

			return $wp_query;
		}
	}

	// Funções necessária para exibir o filtro de categorias nos produtos no Dashboard
	public function my_restrict_manage_posts(){

		global $typenow;

		$taxonomy = $this->taxonomy;
		if ($typenow == $this->cptSlug) {
			$filters = array($taxonomy);
			foreach ($filters as $tax_slug) {
				$terms = get_terms( array(
					'taxonomy' => $tax_slug,
					'hide_empty' => false,
				) );
				echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
				echo "<option value=''>Ver todas as categorias</option>";
				foreach ($terms as $term) {
					echo '<option value=' . $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '', '>' . $term->name . '</option>';
				}
				echo "</select>";
			}
		}
	}
}