<?php

namespace Classes\Cpt;


class CptContato extends Cpt
{
	public function __construct(){
		$this->cptSlug = self::getCptSlugExtend();
		$this->name = self::getNameExtend();
		$this->todosOsItens = self::getTodosOsItensExtend();
		$this->dashborarIcon = self::getDashborarIconExtendExtend();

		add_action('init', array($this, 'register'));

		//Alterando e Exibindo as colunas no Dashboard que vem por padrão na classe CPT
		add_filter('manage_contato_posts_columns', array($this, 'exibe_cols_contatos'), 10, 2);
		add_action( 'manage_contato_posts_custom_column' , array($this, 'cols_content_contato'), 10, 2 );

	}

	//Exibindo as colunas no Dashboard
	public function exibe_cols_contatos($cols) {
		
		if( current_user_can('editor') || current_user_can('administrator') ) {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => 'Title',
				'date' => 'Date',					
				'grupo' => 'Grupo',
			);
		} else {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => 'Title',				
				'date' => 'Date',	
			);
		}		
		
		return $columns;
	}

	public function cols_content_contato($column) {	
		
		

		switch ( $column ) {			

			case 'grupo':
				$localizacao = get_the_ID();

				$paginas = get_posts(array(
					'post_type' => 'editores_portal',
					'orderby' => 'title',
    				'order'   => 'ASC',
					'post_status'    => 'publish',
					'meta_query' => array(
						array(
							'key' => 'contatos_sme', // name of custom field
							'value' => '"' . $localizacao . '"', // matches exaclty "123", not just 123. This prevents a match for "1234"
							'compare' => 'LIKE'
						)
					)
				));

				
				if($paginas && $paginas != ''){
					$a = 0;
					foreach($paginas as $pagina){
						if($a == 0){
							echo "<a href='" . admin_url('edit.php?post_type=contato&filter=grupo&grupo_id=' . $pagina->ID) . "'>" . get_the_title($pagina->ID) . "</a>";
							
						} else {
							echo ", <a href='" . admin_url('edit.php?post_type=contato&filter=grupo&grupo_id=' . $pagina->ID) . "'>" . get_the_title($pagina->ID) . "</a>";
						}
						
						$a++;
					}
				} else {
					if($_GET['grupo_id'] && $_GET['grupo_id'] != ''){
						echo "<a href='" . admin_url('edit.php?post_type=contato&filter=grupo&grupo_id=' . $_GET['grupo_id']) . "'>" . get_the_title($_GET['grupo_id']) . "</a>";
					}
				}

				//echo "Aqui: " . $localizacao . "<br>";
				//print_r($posts);
				break;

		}
	}

	/**
	 * Alterando as configurações que vem por padrão na classe CPT (Adicionando suporte a thumbnail)
	 */
	public function register()
	{
		$labels = array(
			'name' => _x($this->name, 'post type general name'),
			'singular_name' => _x($this->name, 'post type singular name'),
			'all_items' => _x( $this->todosOsItens, 'Admin Menu todos os itens'),
			'add_new' => _x('Adicionar contato ', 'Novo item'),
			'add_new_item' => __('Novo Item'),
			'edit_item' => __('Editar Item'),
			'new_item' => __('Novo Item'),
			'view_item' => __('Ver Item'),
			'search_items' => __('Procurar Itens'),
			'not_found' => __('Nenhum registro encontrado'),
			'not_found_in_trash' => __('Nenhum registro encontrado na lixeira'),
			'parent_item_colon' => '',
			'menu_name' => $this->name
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'public_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => array('contato','contatos'),
			'capabilities' => array(
				'edit_post' => 'edit_contato',
				'edit_posts' => 'edit_contatos',
				'edit_published_posts ' => 'edit_published_contatos',
				'read_post' => 'read_contato',
				'read_private_posts' => 'read_private_contatos',
				'delete_post' => 'delete_contato',
				'delete_published_posts' => 'delete_published_contatos',
			),
			'map_meta_cap'        => true,
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => 10,
			'menu_icon'   => $this->dashborarIcon,
			'exclude_from_search' => true,
			'show_in_rest' => true,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports' => array(),
		);

		register_post_type($this->cptSlug, $args);

		remove_post_type_support( $this->cptSlug, 'editor' );

		flush_rewrite_rules();

		register_taxonomy(
			'categorias-contato',
			$this->cptSlug,
			array(
				"hierarchical" => true,
				"label" => 'Categorias de Contatos',
				"singular_label" => 'Categoria de Contato',
				'map_meta_cap'        => true,
				// Definido as capacidades para a taxonomia tag. Se torna uma Tag porque o 'hierarchical'  => false,
				'capabilities' => array(
					'manage_terms'=>'manage_contatos',
					'edit_terms'=>'edit_contatos',
					'delete_terms'=>'delete_contatos',
					'assign_terms'=>'assign_contatos',
				)
			)
		);

	}

}