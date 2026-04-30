<?php

namespace Classes\Cpt;


class CptOportunidades extends Cpt
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
			'all_items' => _x( 'Oportunidades', 'Admin Menu todos os itens'),
			'add_new' => _x('Add Oportunidades ', 'Novo item'),
			'add_new_item' => __('Add Oportunidade'),
			'edit_item' => __('Editar Oportunidade'),
			'new_item' => __('Add Oportunidade'),
			'view_item' => __('Ver Oportunidade'),
			'search_items' => __('Procurar Oportunidades'),
			'not_found' => __('Nenhuma oportunidade encontrada'),
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
			'capability_type' => array('oportunidade','oportunidades'),
			'capabilities' => array(
				'edit_post' => 'edit_oportunidade',
				'edit_posts' => 'edit_oportunidades',
				'edit_published_posts ' => 'edit_published_oportunidades',
				'read_post' => 'read_oportunidade',
				'read_private_posts' => 'read_private_oportunidades',
				'delete_post' => 'delete_oportunidade',
				'delete_published_posts' => 'delete_published_oportunidades',
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
            'locais',
            'oportunidade',
            array(
                'hierarchical' => false,

                'labels' => array(
                    'name'              => 'Locais',
                    'singular_name'     => 'Local',
                    'search_items'      => 'Buscar Locais',
                    'all_items'         => 'Todos os Locais',
                    'parent_item'       => 'Local Pai',
                    'parent_item_colon' => 'Local Pai:',
                    'edit_item'         => 'Editar Local',
                    'update_item'       => 'Atualizar Local',
                    'add_new_item'      => 'Adicionar Novo Local',
                    'new_item_name'     => 'Novo Local',
                    'menu_name'         => 'Locais',
                ),

                'map_meta_cap' => true,
				'meta_box_cb' => false,

                'capabilities' => array(
                    'manage_terms' => 'manage_locais',
                    'edit_terms'   => 'edit_locais',
                    'delete_terms' => 'delete_locais',
                    'assign_terms' => 'assign_locais',
                )
            )
        );

	}

}