<?php

namespace Classes\Cpt;


class CptAgendaSecretarioNew extends Cpt
{
	public function __construct(){
		$this->cptSlug = self::getCptSlugExtend();
		$this->name = self::getNameExtend();
		$this->todosOsItens = self::getTodosOsItensExtend();
		$this->dashborarIcon = self::getDashborarIconExtendExtend();

		add_action('init', array($this, 'register'));

		//Alterando e Exibindo as colunas no Dashboard que vem por padrão na classe CPT
		add_filter('manage_posts_columns', array($this, 'exibe_cols'), 10, 2);
		add_action('manage_' . $this->cptSlug . '_posts_custom_column', array($this, 'cols_content'));
		add_filter('manage_edit-' . $this->cptSlug . '_sortable_columns', array($this, 'cols_sort'));
		add_filter('request', array($this, 'orderby'));
	}

	//Exibindo as colunas no Dashboard
	public function exibe_cols($cols, $post_type)
	{

		if ($post_type == $this->cptSlug) {
			unset($cols['tags'],$cols['categories'],$cols['comments'], $cols['categoria'], $cols['featured_thumb'], $cols['wpseo-links']);
			$cols['author'] = 'Autor';
			$cols['data_evento'] = 'Data do Evento';
			$cols['qtd_evento'] = 'Quantidade de eventos';
			//$cols['imagem'] = 'Imagem';
		}
		return $cols;
	}

	//Exibindo as informações correspondentes de cada coluna
	public function cols_content($col)
	{
		global $post;
		switch ($col) {
			case 'data_evento':
				$data_do_evento = get_field('data_do_evento', $post->ID);				
				echo $data_do_evento;

				break;

			case 'qtd_evento':
				$qtd_evento = get_field('eventos_do_dia', $post->ID);
				//print_r($qtd_evento);
				if (is_array($qtd_evento) || $qtd_evento instanceof Countable) {
					$qtd = count($qtd_evento);
				} else {
					$qtd = 0; // Valor padrão caso não seja contável
				}
				if( !isset($_GET['orderby']) || $_GET['orderby'] == 'data_evento'){
					$qtd = $qtd_evento;
				}
				if($qtd == 1){
					echo $qtd . ' evento';
				} elseif($qtd > 1) {
					echo $qtd . ' eventos';
				} else {
					echo ' - ';
				}
				
				break;
			
		}
	}

	// Permitindo a ordenação das colunas exibidas no Dashboard
	function cols_sort($cols)
	{
		$cols['data_evento'] = 'data_evento';
		$cols['hora_evento'] = 'Hora do Evento';
		$cols['local_evento'] = 'Local do Evento';
		return $cols;
	}

	function orderby($vars)
	{
		if (is_admin()) {
			if (isset($vars['orderby']) && $vars['orderby'] == 'data_evento') {
				$vars['orderby'] = 'meta_value_num date';
				$vars['meta_key'] = 'data_do_evento';
			}

			if (isset($vars['orderby']) && $vars['orderby'] == 'hora_evento') {
				$vars['orderby'] = 'menu_order';
			}

			if (isset($vars['orderby']) && $vars['orderby'] == 'local_evento') {
				$vars['orderby'] = 'menu_order';
			}
		}
		return $vars;
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
			'add_new' => _x('Adicionar evento ', 'Novo item'),
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
			'rewrite' => array( 'slug' => 'agenda', 'with_front' => false ),
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => 10,
			'menu_icon'   => $this->dashborarIcon,
			'exclude_from_search' => true,
			'show_in_rest' => true,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports' => array('revisions'),
		);

		register_post_type($this->cptSlug, $args);

		remove_post_type_support( $this->cptSlug, 'editor' );
		//remove_post_type_support( $this->cptSlug, 'title' );

		flush_rewrite_rules();

		// Categorias Compromissos
		register_taxonomy(
			'compromisso',
			'agendanew',
			array(
				"label" => 'Compromissos',
				"singular_label" => 'Compromisso',
				"hierarchical" => true,
				'show_ui' => true,
				'meta_box_cb' => false,
				'query_var' => true,
				'show_in_rest' => true,
				'show_in_quick_edit' => false,
				'rest_controller_class' => 'WP_REST_Terms_Controller',
			)
		);

		// Categorias Enderecos
		register_taxonomy(
			'endereco',
			'agendanew',
			array(
				"label" => 'Endereços',
				"singular_label" => 'Endereço',
				"hierarchical" => true,
				'show_ui' => true,
				'meta_box_cb' => false,
				'query_var' => true,
				'show_in_rest' => true,
				'show_in_quick_edit' => false,
				'rest_controller_class' => 'WP_REST_Terms_Controller',
			)
		);
	}
}