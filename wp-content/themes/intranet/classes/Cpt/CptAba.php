<?php

namespace Classes\Cpt;


class CptAba extends Cpt
{

	public function __construct()
	{
		$this->cptSlug = self::getCptSlugExtend();
		$this->name = self::getNameExtend();
		$this->todosOsItens = self::getTodosOsItensExtend();
		$this->dashborarIcon = self::getDashborarIconExtendExtend();

		add_action('init', array($this, 'register'));
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
			'add_new' => _x('Adicionar nova ', 'Novo item'),
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
			'capability_type' => array('aba','abas'),
			'capabilities' => array(
				'edit_post' => 'edit_aba',
				'edit_posts' => 'edit_abas',
				'edit_published_posts ' => 'edit_published_abas',
				'read_post' => 'read_aba',
				'read_private_posts' => 'read_private_abas',
				'delete_post' => 'delete_aba',
				'delete_published_posts' => 'delete_published_abas',
			),
			'map_meta_cap'        => true,
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => 10,
			'menu_icon'   => $this->dashborarIcon,
			'exclude_from_search' => true,
			'show_in_rest' => true,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports' => array('title', 'editor', 'excerpt',  'author'),
		);

		register_post_type($this->cptSlug, $args);
		flush_rewrite_rules();

		register_taxonomy(
			'categorias-aba',
			$this->cptSlug,
			array(
				"hierarchical" => true,
				"label" => 'Categorias de Abas',
				"singular_label" => 'Categoria de Aba',
				'map_meta_cap'        => true,
				// Definido as capacidades para a taxonomia tag. Se torna uma Tag porque o 'hierarchical'  => false,
				'capabilities' => array(
					'manage_terms'=>'manage_abas',
					'edit_terms'=>'edit_abas',
					'delete_terms'=>'delete_abas',
					'assign_terms'=>'assign_abas',
				)
			)
		);
	}



}