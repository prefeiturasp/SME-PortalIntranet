<?php

namespace Classes\Cpt;


class Cpt
{
	protected $type, $cptSlug, $name, $todosOsItens, $menuName, $singularName, $taxonomy, $taxonomyLabel, $taxonomysingularLabel, $dashborarIcon, $excludeFromSearch;
	protected  static $cptSlugExtend;
	protected  static $nameExtend;
	protected  static $todosOsItensExtend;
	protected  static $taxonomyExtend;
	protected  static $dashborarIconExtend;

	protected $args;


	public function __construct($type, $cptSlug, $name, $todosOsItens, $menuName, $singularName, $taxonomy, $taxonomyLabel, $taxonomysingularLabel, $dashborarIcon, $excludeFromSearch=true)
	{
		$this->type = $type;
		$this->cptSlug = $cptSlug;
		$this->name = $name;
		$this->todosOsItens = $todosOsItens;
		$this->menuName = $menuName;
		$this->singularName = $singularName;
		$this->taxonomy = $taxonomy;
		$this->taxonomyLabel = $taxonomyLabel;
		$this->taxonomysingularLabel = $taxonomysingularLabel;
		$this->dashborarIcon = $dashborarIcon;
		$this->excludeFromSearch = $excludeFromSearch;

		self::$cptSlugExtend = $this->cptSlug;
		self::$nameExtend = $this->name;
		self::$todosOsItensExtend = $this->todosOsItens;
		self::$taxonomyExtend = $this->taxonomy;
		self::$dashborarIconExtend = $this->dashborarIcon;


		add_action('init', array($this, 'register'));
		add_action('restrict_manage_posts', array($this, 'my_restrict_manage_posts'));

		add_filter('manage_posts_columns', array($this, 'exibe_cols'), 10, 2);
		add_action('manage_' . $this->cptSlug . '_posts_custom_column', array($this, 'cols_content'));
		add_filter('manage_edit-' . $this->cptSlug . '_sortable_columns', array($this, 'cols_sort'));
		add_filter('request', array($this, 'orderby'));

		// Necessário para habilitar corretamente as tags. Com esse método é possível cadastrar uma tag em qualquer lugar.
		// Permite também a página loop-tag.php exibir posts de qualquer Post ou CPT
		add_filter( 'pre_get_posts', array($this, 'habilitaTags' ));

		$this->loadDependencesAdmin();
	}

	public static function getCptSlugExtend(){
		return self::$cptSlugExtend;
	}

	public static function getNameExtend(){
		return self::$nameExtend;
	}

	public static function getTodosOsItensExtend(){
		return self::$todosOsItensExtend;
	}

	public static function getTaxonomyExtend(){
		return self::$taxonomyExtend;
	}

	public static function getDashborarIconExtendExtend(){
		return self::$dashborarIconExtend;
	}

	public function loadDependencesAdmin()
	{
		if (is_admin()){
			add_action('init', array($this, 'custom_formats_admin'));
		}
	}

	public function custom_formats_admin()
	{
		wp_register_style('font_awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
		wp_enqueue_style('font_awesome');
	}


	/**
	 * Register post type
	 */
	public function register()
	{
		$labels = array(
			'name' => _x($this->name, 'post type general name'),
			'singular_name' => _x($this->name, 'post type singular name'),
			'all_items' => _x( $this->todosOsItens, 'Admin Menu todos os itens'),
			'add_new' => _x('Adicionar', 'Adicionar ' . $this->singularName),
			'add_new_item' => __('Adicionar ' . $this->singularName),
			'edit_item' => __('Editar Item'),
			'new_item' => __('Adicionar ' . $this->singularName),
			'view_item' => __('Ver ' . $this->singularName),
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
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => 10,
			'menu_icon'   => $this->dashborarIcon,
			'exclude_from_search' => true,
			'show_in_rest' => true,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'author', 'excerpt', 'comments'),
		);

		register_post_type($this->cptSlug, $args);
		flush_rewrite_rules();

		if ($this->taxonomy && $this->taxonomy !== '') {

			register_taxonomy(
				$this->taxonomy,
				$this->cptSlug,
				array(
					"label" => $this->taxonomyLabel,
					"singular_label" => $this->taxonomysingularLabel,
					"hierarchical" => true,
					'show_ui' => true,
					'query_var' => true,
					'show_in_rest' => true,
					'rest_controller_class' => 'WP_REST_Terms_Controller',
				)
			);
		}
	}

	// Funções necessária para exibir o filtro de categorias nos produtos no Dashboard
	public function my_restrict_manage_posts(){

		global $typenow;
		$taxonomy = $this->taxonomy; // taxonomia personalizada = categorias
		if ($typenow == $this->cptSlug) { // custom post type = link
			$filters = array($taxonomy);
			foreach ($filters as $tax_slug) {
				//$tax_obj = get_taxonomy($tax_slug);
				//$tax_name = $tax_obj->labels->name;
				$terms = get_terms($tax_slug);
				echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
				echo "<option value=''>Ver todas as categorias</option>";
				foreach ($terms as $term) {
					echo '<option value=' . $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '', '>' . $term->name . ' (' . $term->count . ')</option>';
				}
				echo "</select>";
			}
		}
	}

	//Exibindo as colunas no Dashboard
	public function exibe_cols($cols, $post_type)
	{

		if ($post_type == $this->cptSlug) {
			$cols['categoria'] = 'Categoria';
			//$cols['imagem'] = 'Imagem';
		}
		return $cols;
	}

	//Exibindo as informações correspondentes de cada coluna
	public function cols_content($col)
	{
		global $post;
		switch ($col) {
			case 'categoria':
				$tax = '';
				$terms = get_the_terms($post->ID, $this->taxonomy);

				if ($terms) {
					foreach ($terms as $t) {
						if ($tax) $tax .= ', ';
						$tax .= $t->name;
					}
					echo $tax;
				}else{
					echo '<p>Nenhuma Categoria Selecionada</p>';
				}

				break;
		}
	}

	// Permitindo a ordenação das colunas exibidas no Dashboard
	function cols_sort($cols)
	{
		$cols['categoria'] = 'Categoria';
		$cols['destaque'] = 'Destaque';
		return $cols;
	}

	function orderby($vars)
	{
		if (is_admin()) {
			if (isset($vars['orderby']) && $vars['orderby'] == 'categoria') {
				$vars['orderby'] = 'menu_order';
			}

			if (isset($vars['orderby']) && $vars['orderby'] == 'destaque') {
				$vars['orderby'] = 'menu_order';
			}
		}
		return $vars;
	}

	public function habilitaTags( $query ) {

		if( is_tag() && $query->is_main_query() ) {

			$post_types = get_post_types();

			$query->set( 'post_type', $post_types );
		}
	}

}