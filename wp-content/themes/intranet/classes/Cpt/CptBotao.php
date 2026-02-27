<?php

namespace Classes\Cpt;


class CptBotao extends Cpt
{
	public function __construct(){
		$this->cptSlug = self::getCptSlugExtend();
		$this->name = self::getNameExtend();
		$this->todosOsItens = self::getTodosOsItensExtend();
		$this->dashborarIcon = self::getDashborarIconExtendExtend();

		add_action('init', array($this, 'removePostTypeSupport'));

		add_action('init', array($this, 'register'));
	}

	public function removePostTypeSupport(){
		remove_post_type_support( $this->cptSlug, 'editor' );
	}

	/**
	 * Alterando as configurações que vem por padrão na classe CPT (Adicionando)
	 */
	public function register()
	{

		$labels = array(
			'name' => _x($this->name, 'post type general name'),
			'singular_name' => _x($this->name, 'post type singular name'),
			'all_items' => _x( $this->todosOsItens, 'Admin Menu todos os itens'),
			//'add_new' => _x('Adicionar novo '.$this->name, 'Novo item'),
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
			'rewrite' => array( 'with_front' => false ),
			'capability_type' => array('botao','botoes'),
			'capabilities' => array(
				'edit_post' => 'edit_botao',
				'edit_posts' => 'edit_botoes',
				'edit_published_posts ' => 'edit_published_botoes',
				'read_post' => 'read_botao',
				'read_private_posts' => 'read_private_botoes',
				'delete_post' => 'delete_botao',
				'delete_published_posts' => 'delete_published_botoes',
			),
			'map_meta_cap'        => true,
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => 10,
			'menu_icon'   => $this->dashborarIcon,
			'exclude_from_search' => true,
			'show_in_rest' => true,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports' => array('title'),
		);

		register_post_type($this->cptSlug, $args);
		flush_rewrite_rules();

		register_taxonomy(
			'categorias-botao',
			$this->cptSlug,
			array(
				"hierarchical" => true,
				"label" => 'Categorias de Botões',
				"singular_label" => 'Categoria de Botão',
				'map_meta_cap'        => true,
				// Definido as capacidades para a taxonomia tag. Se torna uma Tag porque o 'hierarchical'  => false,
				'capabilities' => array(
					'manage_terms'=>'manage_botoes',
					'edit_terms'=>'edit_botoes',
					'delete_terms'=>'delete_botoes',
					'assign_terms'=>'assign_botoes',
				)
			)
		);
	}

}