<?php

function minha_soma($a, $b) {
    return $a + $b;
}

use Respect\Validation\Rules\Length;
use EnviaEmailSme\classes\Envia_Emails_Sorteio_SME;
if (!session_id()) {
    session_start();
}

// Desabilitando o Gutemberg
add_filter('use_block_editor_for_post', '__return_false');

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

// Remover a tag p da category_description
remove_filter('term_description', 'wpautop');
// Remover a tag p do the_excerpt()
remove_filter('the_excerpt', 'wpautop');

add_action('after_setup_theme', 'custom_setup');

/**
 * @codeCoverageIgnore
 */
function custom_setup() {
	if ( !( current_user_can('editor') || current_user_can('administrator') ) && !is_admin() ) {
		show_admin_bar(false);
	}
	add_action('wp_enqueue_scripts', 'custom_formats');
	add_filter('get_image_tag_class', 'image_tag_class');
	add_action('login_head', 'custom_login_logo');
	add_filter('login_headerurl', 'my_login_logo_url');
	add_filter('login_headertitle', 'my_login_logo_url_title');
	add_action( 'widgets_init', 'theme_slug_widgets_init' );

	register_nav_menus(array(
		'primary' => __('Menu Superior', 'THEMENAME'),
	));

	register_nav_menus(array(
		'primary_parc' => __('Menu Superior Parceiras', 'THEMENAME'),
	));

	register_nav_menu('navbar', __('Navbar', 'your-theme'));


	if (function_exists('add_image_size')) {
		add_theme_support('post-thumbnails');
	}

	if (function_exists('add_image_size')) {
		add_image_size('home-thumb', 250, 166);
		add_image_size('default-image', 825, 470, true);
		add_image_size('img-dest', 1000, 400, true);
	}

	//Permite adicionar no post ou página uma imagem com tamanho personalizado, nesse caso a home-thumb já definida anteriormente com 250X147
	function custom_choose_sizes($sizes) {
		$custom_sizes = array(
			'home-thumb' => 'Tamanho Personalizado',
			'default-image' => 'Tamanho Padrão',
			'img-dest' => 'Imagem de Destaque'
		);
		return array_merge($sizes, $custom_sizes);
	}

	add_filter('image_size_names_choose', 'custom_choose_sizes');

	// Limita o Numero de palavras da função the_excerpt(), nesse caso em 20
	function wpdev_custom_excerpt_length() {
		return 20;
	}
	add_filter('excerpt_length', 'wpdev_custom_excerpt_length');

	function theme_slug_widgets_init()
	{

		register_sidebar(array(
			'name' => 'Rodape Esquerda',
			'id' => 'sidebar-4',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '<p class="titulo-rodape">',
			'after_title' => '</p>',
		));

		register_sidebar(array(
			'name' => 'Rodape Centro',
			'id' => 'sidebar-5',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '<p class="titulo-rodape">',
			'after_title' => '</p>',
		));


		register_sidebar(array(
			'name' => 'Rodape Direita',
			'id' => 'sidebar-6',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '<p class="titulo-rodape">',
			'after_title' => '</p>',
		));

		register_sidebar(array(
			'name' => 'Facebook Home',
			'id' => 'sidebar-7',
			'before_widget' => '',
			'after_widget' => '',
			//'before_title' => '<p class="titulo-rodape">',
			//'after_title' => '</p>',
		));
	}


	//////////////////////////////////////////////////////////////////////////
	///        FUNCAO PARA TROCAR BACKGROUND                            /////
	////////////////////////////////////////////////////////////////////////


	$defaults = array(
		'default-color' => '',
		'default-image' => '',
		'wp-head-callback' => '_custom_background_cb',
		'admin-head-callback' => '',
		'admin-preview-callback' => ''
	);
	add_theme_support('custom-background', $defaults);


	//////////////////////////////////////////////////////////////////////////
	///        FUNCAO HEADER, PARA TROCAR O CABEÃ‡ALHO                   /////
	////////////////////////////////////////////////////////////////////////
	$defaults = array(
		'default-image' => '',
		'width' => 0,
		'height' => 0,
		'flex-height' => false,
		'flex-width' => false,
		'uploads' => true,
		'random-default' => false,
		'header-text' => true,
		'default-text-color' => '',
		'wp-head-callback' => '',
		'admin-head-callback' => '',
		'admin-preview-callback' => '',
	);
	add_theme_support('custom-header', $defaults);


	//////////////////////////////////////////////////////////////////////////
	///        FUNCAO HEADER, PARA TROCAR O lOGOTIPO                    /////
	////////////////////////////////////////////////////////////////////////
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );


}


/**
 * @codeCoverageIgnore
 */
function custom_formats() {

	//wp_register_style('bootstrap_css', STM_THEME_URL . 'css/bootstrap.css', null, null, 'all');
	wp_register_style('bootstrap_4_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css', null, '4.2.1', 'all');

	wp_register_style('animate_css', STM_THEME_URL . 'css/animate.css', null, null, 'all');
	wp_register_style('hamburger_menu_icons_css', STM_THEME_URL . 'css/hamburger_menu_icons.css', null, null, 'all');
	wp_register_style('hover-effects_css', STM_THEME_URL . 'css/hover-effects.css', null, null, 'all');
	wp_register_style('default_ie', STM_THEME_URL . 'css/ie6.1.1.css', null, null, 'all');
	wp_register_style('font_awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
	wp_register_style('style', get_stylesheet_uri(), null, null, 'all');
	wp_register_style('slick_css', STM_THEME_URL . 'css/slick.css', null, null, 'all');
	wp_register_style('slick_theme_css', STM_THEME_URL . 'css/slick-theme.css', null, null, 'all');
	

	//wp_register_script('bootstrap_js', STM_THEME_URL . 'js/bootstrap.js', false, false);

	wp_register_script('bootstrap_4_popper_js',  'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js', false, '1.14.6', true);
	wp_register_script('bootstrap_4_js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js', false, '4.2.1', true);


	wp_register_script('modal_on_load_js', STM_THEME_URL . 'js/modal_on_load.js', false, true);
	wp_register_script('wow_js', STM_THEME_URL . 'js/wow.min.js', array('jquery'), 1.0, true);
	wp_register_script('jquery_waituntilexists', STM_THEME_URL . 'js/jquery.waituntilexists.js', array('jquery'), 1.0, true);
	wp_register_script('scripts_js', STM_THEME_URL . 'js/scripts.js', array('jquery'), 1.0, true);
	wp_register_script('jquery.event.move_js', STM_THEME_URL . 'js/jquery.event.move.js', array('jquery'), 1.0, true);
	wp_register_script('slick_min_js', STM_THEME_URL . 'js/slick.min.js', array('jquery'), 1.0, true);
	wp_register_script('slick_func_js', STM_THEME_URL . 'js/slick-func.js', array('jquery'), 1.0, true);
	wp_register_script('lightbox_js', STM_THEME_URL . 'js/jquery-simple-lightbox.js', array('jquery'), 1.0, true);


	global $wp_styles;
	$wp_styles->add_data('default_ie', 'conditional', 'IE 6');
	wp_enqueue_style('bootstrap_4_css');

	wp_enqueue_style('animate_css');
	wp_enqueue_style('hamburger_menu_icons_css');
	wp_enqueue_style('hover-effects_css');
	wp_enqueue_style('default_ie');
	wp_enqueue_style('font_awesome');
	wp_enqueue_style('style');

	wp_enqueue_script('jquery');

	wp_enqueue_script('bootstrap_4_popper_js');
	wp_enqueue_script('bootstrap_4_js');

	wp_enqueue_script('modal_on_load_js');
	wp_enqueue_script('wow_js');
	wp_enqueue_script('jquery_waituntilexists');
	wp_enqueue_script('scripts_js');
	wp_enqueue_script('jquery.event.move_js');
}


// **************** Scripts para fazer o efeito de rolagem do menu funcionar corretamente ****************

/* Função para adicionar classes ao li a do menu wp-nav-menu para fazer o efeito de scroll */
function adicionar_nav_class($output) {
	$output = preg_replace('/<a/', '<a class="nav-link scroll"', $output, -1);
	return $output;
}
add_filter('wp_nav_menu', 'adicionar_nav_class');



// **************** FIM dos Scripts para fazer o efeito de rolagem do menu funcionar corretamente ****************

/* Função para adicionar classes a imagem que vem da biblioteca de midia */
function image_tag_class($class) {
	$class .= ' img-fluid';
	return $class;
}

function paginacao() {
	echo '<nav id="pagination" class="container">';
	global $wp_query;
	$pagina_atual = (int) $wp_query->get('paged');
	if (!$pagina_atual)
		$pagina_atual = 1;
	$total_paginas = (int) $wp_query->max_num_pages;
	echo paginate_links(
		array(
			'current' => $pagina_atual,
			'total' => $total_paginas,
			'base' => str_replace($total_paginas + 1, '%#%', get_pagenum_link($total_paginas + 1)),
			'prev_next'         => True,
			'prev_text'          	=> __('<i class="fa fa-chevron-left fa-2x" aria-hidden="true"></i>'),
			'next_text'          	=> __('<i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i>'),
		)
	);
	echo '</nav>';
}


function custom_login_logo() {
    // permite sobrescrever em testes / plugins
    $template_dir = apply_filters('custom_login_template_dir', get_bloginfo('template_directory'));

    echo '<style type="text/css">
        .login h1 a{ background-size: 273px 159px !important; width:323px; height:159px }
        h1 a { background-image: url(' . esc_url( $template_dir . '/img/logo_admin.png' ) . ') !important; }
    </style>';

    echo '<style type="text/css">
        body { background-image: url(' . esc_url( $template_dir . '/img/bg-background.png' ) . ') !important; }
    </style>';
}

//Link na tela de login para a pÃ¡gina inicial
/**
 * @codeCoverageIgnore
 */
function my_login_logo_url() {
	return STM_URL;
}

/**
 * @codeCoverageIgnore
 */
function my_login_logo_url_title() {
	return STM_SITE_NAME;
}

// Adicionando alt e title nas images
add_filter( 'wp_get_attachment_image_attributes','getAltTitleImagesThePostThumbnail', 10, 2 );
function getAltTitleImagesThePostThumbnail( $attr=null, $attachment = null ) {
	
	$img_alt = trim( strip_tags( $attachment->post_excerpt ) );
	$attr['alt'] = $img_alt;

	return $attr;
}


function incluir_nome_nos_anexos($post_id, $xml_node, $is_update, $get_posts_cb = null, $wp_update_post_cb = null, $get_post_cb = null, $get_post_thumbnail_id_cb = null) {
    $get_posts_cb ??= 'get_posts';
    $wp_update_post_cb ??= 'wp_update_post';
    $get_post_cb ??= 'get_post';
    $get_post_thumbnail_id_cb ??= 'get_post_thumbnail_id';

    $xml_node = (array) $xml_node;
    $nome_dos_arquivos = $xml_node['Files_Nomes_Dos_Arquivos'];
    $pieces = explode(',', $nome_dos_arquivos);
    $post_thumbnail_id = $get_post_thumbnail_id_cb($post_id);
    $post = $get_post_cb($post_id);

    $attachments = $get_posts_cb([
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_parent' => $post_id,
        'orderby' => 'ID',
        'order' => 'ASC',
        'exclude' => $post_thumbnail_id
    ]);

    if ($attachments) {
        foreach ($attachments as $index => $attachment) {
            $my_post = [
                'ID' => $attachment->ID,
                'post_title' => $pieces[$index],
                'post_excerpt' => $post->post_excerpt,
                'post_content' => $post->post_excerpt,
            ];
            $wp_update_post_cb($my_post);
        }
    }
}

add_action('pmxi_saved_post', 'incluir_nome_nos_anexos', 10, 3);

add_image_size( 'admin-list-thumb', 80, 80, false );

// Retirando a tag <p> antes e depois de um iframe dentro do the_content
/**
 * @codeCoverageIgnore
 */
function remove_some_ptags( $content ) {
	$content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
	$content = preg_replace('/<p>\s*(<script.*>*.<\/script>)\s*<\/p>/iU', '\1', $content);
	$content = preg_replace('/<p>\s*(<iframe.*>*.<\/iframe>)\s*<\/p>/iU', '\1', $content);
	return $content;
}
add_filter( 'the_content', 'remove_some_ptags' );

// Removendo o atributo title dos menus
/**
 * @codeCoverageIgnore
 */
function my_menu_notitle( $menu ){
	return $menu = preg_replace('/ title=\"(.*?)\"/', '', $menu );

}
add_filter( 'wp_nav_menu', 'my_menu_notitle' );
add_filter( 'wp_page_menu', 'my_menu_notitle' );
add_filter( 'wp_list_categories', 'my_menu_notitle' );

// POSTS MAIS VISTOS  (NO FUNCTIONS)
// Função de contagem de posts populares com callbacks para testes
function shapeSpace_popular_posts(
    $post_id,
    $get_post_meta_cb = null,
    $update_post_meta_cb = null,
    $add_post_meta_cb = null,
    $delete_post_meta_cb = null
) {
    $get_post_meta_cb ??= 'get_post_meta';
    $update_post_meta_cb ??= 'update_post_meta';
    $add_post_meta_cb ??= 'add_post_meta';
    $delete_post_meta_cb ??= 'delete_post_meta';

    $count_key = 'popular_posts';
    $count = $get_post_meta_cb($post_id, $count_key, true);

    if ($count === '') {
        $count = 0;
        $delete_post_meta_cb($post_id, $count_key);
        $add_post_meta_cb($post_id, $count_key, '0');
    } else {
        $count++;
        $update_post_meta_cb($post_id, $count_key, $count);
    }
}

// Função para rastrear posts, com callback injetável para is_single
function shapeSpace_track_posts(
    $post_id = null,
    $meta_storage = null,
    $get_post_meta_cb = null,
    $update_post_meta_cb = null,
    $add_post_meta_cb = null,
    $delete_post_meta_cb = null,
    $is_single_cb = null
) {
    $is_single_cb ??= 'is_single';

    if (!$is_single_cb()) {
        return;
    }

    if (empty($post_id)) {
        global $post;
        $post_id = $post->ID ?? null;
    }

    if ($post_id) {
        shapeSpace_popular_posts(
            $post_id,
            $get_post_meta_cb,
            $update_post_meta_cb,
            $add_post_meta_cb,
            $delete_post_meta_cb
        );
    }
}

// Adiciona no WP apenas a função original
add_action('wp_head', 'shapeSpace_track_posts');

function redireciona_paginas_pendentes($is_404_cb = null, $redirect_cb = null, $request_uri = null, $wpdb_obj = null) {

    $is_404_cb = $is_404_cb ?: 'is_404';
    $redirect_cb = $redirect_cb ?: 'wp_redirect';
    $request_uri = $request_uri ?: $_SERVER['REQUEST_URI'];
    $wpdb_obj = $wpdb_obj ?: $GLOBALS['wpdb'];

    if ( $is_404_cb() ) {

        $querystr = "
            SELECT $wpdb_obj->posts.post_title 
            FROM $wpdb_obj->posts
            WHERE $wpdb_obj->posts.post_status = 'pending' 
            AND $wpdb_obj->posts.post_type = 'page'
            ORDER BY $wpdb_obj->posts.post_date DESC
        ";

        $pageposts = $wpdb_obj->get_results($querystr, OBJECT);

        $slugs = [];
        foreach ($pageposts as $page) {
            $slugs[] = sanitize_title($page->post_title);
        }

        $uri = trim($request_uri, '/');
        $segments = explode('/', $uri);
        $slug = $segments[count($segments) - 1] ?? '';

        if (in_array($slug, $slugs)) {
            $redirect_cb(STM_URL . '/conteudo-em-atualizacao/');
        }
    }
}
add_action('template_redirect', 'redireciona_paginas_pendentes');

// @codeCoverageIgnoreStart
define('STM_URL', get_home_url());
define('STM_THEME_URL', get_bloginfo('template_url') . '/');
define('STM_SITE_NAME', get_bloginfo('name'));
define('STM_SITE_DESCRIPTION', get_bloginfo('description'));
define('__ROOT__', dirname(dirname(__FILE__)).'/intranet');
// @codeCoverageIgnoreEnd

function load_lang_file(string $lang = null): string {
    if ($lang === 'en') {
        return 'includes/en.php';
    } else {
        return 'includes/pt.php';
    }
}

// Uso real
$lang = $_GET['lang'] ?? null;
require_once load_lang_file($lang);

// Inicialização das Classes
require_once 'classes/init.php';

require_once('classes/wp_bootstrap_navwalker.php');

// Carrega contador de visualizações de noticias
require 'includes/cont_visualizacao.php';

///////////////////////////////////////////////////////////////////////////////
/////////////////////habilita carregar SVG no wordpress////////////////////////
///////////////////////////////////////////////////////////////////////////////
function cc_mime_types($mimes) {
       $mimes['svg'] = 'image/svg+xml';
       return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

////////Habilita Opções Gerais ACF////////
/**
 * @codeCoverageIgnore
 */
if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title' 	=> 'Configurações Gerais',
        'menu_title'	=> 'Opções Gerais',
        'menu_slug' 	=> 'conf-geral',
        'position' 		=> '3',
		'update_button' => __('Atualizar', 'acf'),
        'capability'	=> 'publish_pages',
        //'redirect'		=> false
    ));

    acf_add_options_sub_page(array(
        'page_title' 	=> 'Alerta da Página Inicial',
        'menu_title'	=> 'Alerta da Página Inicial',
        'parent_slug'	=> 'conf-geral',
		'capability'	=> 'publish_pages',
		'update_button' => __('Atualizar', 'acf'),
		'updated_message' => __("Alerta da Página Inicial atualizado com sucesso", 'acf'),
    ));	
	
	acf_add_options_sub_page(array(
        'page_title' 	=> 'Configurações da Busca Manual',
        'menu_title'	=> 'Busca Manual',
        'parent_slug'	=> 'conf-geral',
		'capability'	=> 'publish_pages',
		'update_button' => __('Atualizar', 'acf'),
		'updated_message' => __("Configurações da Busca atualizado com sucesso", 'acf'),
	));
	
	acf_add_options_sub_page(array(
        'page_title' 	=> 'Configurações de tutoriais',
        'menu_title'	=> 'Inclusão de tutoriais',
        'parent_slug'	=> 'conf-geral',
        'capability'	=> 'publish_pages',
		'update_button' => __('Atualizar', 'acf'),
		'updated_message' => __("Tutoriais atualizado com sucesso", 'acf'),
    ));

	acf_add_options_sub_page(array(
        'page_title' 	=> 'Itens das laterais',
        'menu_title'	=> 'Lateral',
        'parent_slug'	=> 'conf-geral',
        'capability'	=> 'publish_pages',
		'post_id' => 'conf-lateral',
		'update_button' => __('Atualizar', 'acf'),
		'updated_message' => __("Laterais atualizado com sucesso", 'acf'),
    ));

    acf_add_options_sub_page(array(
        'page_title' 	=> 'Informações Rodapé e Topo',
        'menu_title'	=> 'Rodapé e Topo',
        'parent_slug'	=> 'conf-geral',
        'capability'	=> 'publish_pages',
		'post_id' => 'conf-rodape',
		'update_button' => __('Atualizar', 'acf'),
		'updated_message' => __("Informações do Rodapé e Topo atualizados com sucesso", 'acf'),
    ));

	acf_add_options_sub_page(array(
        'page_title' 	=> 'Redirecionamentos',
        'menu_title'	=> 'Redirecionamentos',
        'parent_slug'	=> 'conf-geral',
        'capability'	=> 'publish_pages',
		'update_button' => __('Atualizar', 'acf'),
		'updated_message' => __("Redirecionamentos atualizado com sucesso", 'acf'),
    ));

}
///////////////////////////////////////////////////////////////////

////////Ordena Relação de posts do ACF por data////////
function my_relationship_query( $args, $field, $post_id ) {
	
    // only show children of the current post being edited
    //$args['post_parent'] = $post_id;
	$args['orderby'] = 'date';
	$args['order'] = 'DESC';
	
	// return
    return $args;
    
}
// filter for every field
add_filter('acf/fields/relationship/query', 'my_relationship_query', 10, 3);

//força posicionamento dos campos ACF
/**
 * @codeCoverageIgnore
 */
function prefix_reset_metabox_positions(){
  delete_user_meta( wp_get_current_user()->ID, 'meta-box-order_post' );
  delete_user_meta( wp_get_current_user()->ID, 'meta-box-order_page' );
  delete_user_meta( wp_get_current_user()->ID, 'meta-box-order_custom_post_type' );
}
add_action( 'admin_init', 'prefix_reset_metabox_positions' );


//habilita revisões para o ACF
/**
 * @codeCoverageIgnore
 */
add_filter( 'rest_prepare_revision', function($response, $post){
	$data = $response->get_data();
	$data['acf'] = get_fields( $post->ID );

	return rest_ensure_response( $data );
}, 10, 2);

//habilita atualizações para o ACF
function atualizar_post_modified($wpdb, $post_id, $datetime) {
    $query = "UPDATE {$wpdb->posts}
              SET post_modified = '{$datetime}'
              WHERE ID = '{$post_id}'";
    return $wpdb->query($query);
}

// Função ligada ao hook do ACF
function my_acf_save_post($post_id) {
    if (is_admin() || $post_id === 'new') {
        return;
    }

    global $wpdb;
    $datetime = date("Y-m-d H:i:s");
    atualizar_post_modified($wpdb, $post_id, $datetime);
}

add_action('acf/save_post', 'my_acf_save_post', 20);

//coloca data atual no campo data no ACF
/**
 * @codeCoverageIgnore
 */
function my_acf_default_date($field){
	$field['default_value'] = date('dmY');
	return $field;
}
add_filter('acf/load_field/name=data_da_atualizacao_organograma','my_acf_default_date');

/**
 * @codeCoverageIgnore
 */
add_filter( 'request', 'my_request_filter' );
function my_request_filter( $query_vars ) {
    if( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) {
        $query_vars['s'] = " ";
        global $no_search_results;
        $no_search_results = TRUE;
    }
    return $query_vars;
}

/**
 * @codeCoverageIgnore
 */
function template_chooser($template){    
  global $wp_query;  
  global $no_search_results;
  $post_type = get_query_var('post_type');
  if( $wp_query->is_search && $post_type == 'concurso' )   
  {
    return locate_template('search_concurso.php');  //  redirect to archive-search.php
  }
  return $template;   
}
add_filter('template_include', 'template_chooser');

// Adiciona o title como parametro no wp_query
/**
 * @codeCoverageIgnore
 */
add_filter( 'posts_where', 'title_like_posts_where', 10, 2 );
function title_like_posts_where( $where, $wp_query ) {
    global $wpdb;
    if ( $post_title_like = $wp_query->get( 'post_title_like' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $post_title_like ) ) . '%\'';
    }
    return $where;
}

/**
 * @codeCoverageIgnore
 */
add_action('pre_user_query','wpse_27518_pre_user_query');
function wpse_27518_pre_user_query($user_search) {
    global $wpdb,$current_screen;

    if ( 'users' != $current_screen->id ) 
        return;

    $vars = $user_search->query_vars;

    if('setor' == $vars['orderby']) 
    {
        $user_search->query_from .= " INNER JOIN {$wpdb->usermeta} m1 ON {$wpdb->users}.ID=m1.user_id AND (m1.meta_key='setor')"; 
        $user_search->query_orderby = ' ORDER BY UPPER(m1.meta_value) '. $vars['order'];
    } 
    
}

// Remove o campo "Additional Capabilities" do editor de usuario
/**
 * @codeCoverageIgnore
 */
add_filter( 'ure_show_additional_capabilities_section', '__return_false' );

function get_first_image($post_id) {
    $post = get_post($post_id);
    $content = $post->post_content;
    $regex = '/src="([^"]*)"/';
    preg_match_all($regex, $content, $matches);

    if (empty($matches[1][0])) {
        return false;
    }

    $str = $matches[1][0];
    $re = '/-\d+[Xx]\d+\./';
    $str = preg_replace($re, '.', $str, 1); // já não será null

    $idImage = attachment_url_to_postid($str);

    return $idImage ?: false;
}

function get_thumb( $post_id, $size = 'default-image' ){

	$result = array();
	$alt = '';

	$imgSelect = get_the_post_thumbnail_url($post_id, $size);	
	$firstImage = get_first_image($post_id);

	if($imgSelect){

		$thumbnail_id = get_post_thumbnail_id( $post_id );
		$alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true); 

		if(!$alt){
			$alt = get_the_title($post_id);
		}

		$result[0] = $imgSelect;
		$result[1] = $alt;

	} elseif($firstImage){
		
		$imgOne = wp_get_attachment_image_src($firstImage, $size);
		$alt = get_post_meta($firstImage, '_wp_attachment_image_alt', true);
		
		$imgSlide = $imgOne[0];
		if(!$alt){
			$alt = get_the_title($post_id);
		}

		$result[0] = $imgSlide;
		$result[1] = $alt;

	} else {
		$imgSlide = 'https://hom-educacao.sme.prefeitura.sp.gov.br/wp-content/uploads/2020/03/placeholder06.jpg';
		if(!$alt){
			$alt = get_the_title($post_id);
		}

		$result[0] = $imgSlide;
		$result[1] = $alt;
	}

	return $result;
}

// Unifica o array multidimensional em array unico
function array_flatten($array) { 
	if (!is_array($array)) { 
	  return FALSE; 
	} 
	$result = array(); 
	foreach ($array as $key => $value) { 
	  if (is_array($value)) { 
		$result = array_merge($result, array_flatten($value)); 
	  } 
	  else { 
		$result[$key] = $value; 
	  } 
	} 
	return $result; 
}

// Filtra as paginas que grupo pertence
/**
 * @codeCoverageIgnore
 */
function wp37_limit_posts_to_author($query) {

	// pega as informacoes do usuario logado
	$user = wp_get_current_user();

	// 	filtra as paginas pelo grupo pertencente
	if( isset($_GET['filter']) && $_GET['filter'] == 'grupo' && $user->roles[0] == 'contributor')  {
		
		$variable = get_user_meta($user->ID, 'grupo', true);
		$variable = array_flatten($variable);
        $variable = array_unique($variable);
		
		$pages = array();

		if($variable && $variable != ''){
            foreach($variable as $grupo){
				$pages[] = get_post_meta($grupo, 'selecionar_paginas', true);
				$pages[] = get_post_meta($grupo, 'contatos_sme', true);
			}
        }

		$pages = array_flatten($pages);
        $pages = array_unique($pages);
		
		//print_r($variable);
		$query->set('post__in', $pages);
	} 

	// 	filtra as paginas por grupos
	if( isset($_GET['grupo_id']) && $_GET['grupo_id'] != '')  {
		
		$grupo = $_GET['grupo_id'];

		if($grupo && $grupo != ''){   
			if($_GET['post_type'] == 'contato'){
				$pages = get_post_meta($grupo, 'contatos_sme', true);
			} else {
				$pages = get_post_meta($grupo, 'selecionar_paginas', true);
			}
        }

		$pages = array_flatten($pages);
        $pages = array_unique($pages);
		
		//print_r($variable);
		$query->set('post__in', $pages);
	}	
	
	return $query;
	
}
add_filter('pre_get_posts', 'wp37_limit_posts_to_author');

// Adiciona o filtro Minhas Paginas
/**
 * @codeCoverageIgnore
 */
function wp38_add_movies_filter($views){
	
	// pega as informacoes do usuario logado
	$user = wp_get_current_user();

	if($user->roles[0] == 'contributor'){

		if( $_GET['filter'] == 'grupo' ){

			$views['grupos'] = "<a href='" . admin_url('edit.php?post_type=page&filter=grupo') . "' class='current'>Minhas Páginas</a>";
		return $views;

		} else {
			$views['grupos'] = "<a href='" . admin_url('edit.php?post_type=page&filter=grupo') . "'>Minhas Páginas</a>";
		return $views;
		}
	}

	return $views;
}
 
add_filter('views_edit-page', 'wp38_add_movies_filter');

// Altera a URL de Paginas para colaboladores
/**
 * @codeCoverageIgnore
 */
add_action('admin_menu', 'add_custom_link_into_appearnace_menu');
function add_custom_link_into_appearnace_menu() {
	global $submenu;
	
    // pega as informacoes do usuario logado
	$user = wp_get_current_user();
	
	if($user->roles[0] == 'contributor'){		
		$submenu['edit.php?post_type=page'][5][2] = 'edit.php?post_type=page&filter=grupo';
		$submenu['edit.php?post_type=contato'][5][2] = 'edit.php?post_type=contato&filter=grupo';
	}
}

// Adiciona o filtro Meus Contatos
/**
 * @codeCoverageIgnore
 */
function contatos_filter($views){

	// pega as informacoes do usuario logado
	$user = wp_get_current_user();

	if($user->roles[0] == 'contributor'){

		if( $_GET['filter'] == 'grupo' ){

			$views['grupos'] = "<a href='" . admin_url('edit.php?post_type=contato&filter=grupo') . "' class='current'>Meus Contatos</a>";
		return $views;

		} else {
			$views['grupos'] = "<a href='" . admin_url('edit.php?post_type=contato&filter=grupo') . "'>Meus Contatos</a>";
		return $views;
		}
	}

	return $views;
}

add_filter('views_edit-contato', 'contatos_filter');

// Incluir CSS no admin
/**
 * @codeCoverageIgnore
 */
function admin_style() {
	wp_enqueue_style('admin-styles', get_template_directory_uri().'/css/admin.css');
}

add_action('admin_enqueue_scripts', 'admin_style');

//remover opções de cores do perfil de usuários
/**
 * @codeCoverageIgnore
 */
function admin_color_scheme() {
	global $_wp_admin_css_colors;
	$_wp_admin_css_colors = 0;
}
add_action('admin_head', 'admin_color_scheme');

//remove avisos de atualizações do wordpress, temas e plugins
//add_filter( 'pre_site_transient_update_core','remove_core_updates' );
//add_filter( 'pre_site_transient_update_plugins','remove_core_updates' );
//add_filter( 'pre_site_transient_update_themes','remove_core_updates' );

/**
 * @codeCoverageIgnore
 */
function remove_core_updates(){
    global $wp_version;
    return(object) array(
        'last_checked' => time(),
        'version_checked' => $wp_version
    );
}


// Filtrar usuarios por grupo
/**
 * @codeCoverageIgnore
 */
function filter_users_by_grupo_id( $query ) {
    global $pagenow;

    
	if ( is_admin() && 
         'users.php' == $pagenow && 
         isset( $_GET[ 'grupo_id' ] ) && 
         !empty( $_GET[ 'grupo_id' ] ) 
       ) {
        $section = $_GET[ 'grupo_id' ];
        $meta_query = array(
            array(
				'key' => 'grupo', // name of custom field
				'value' => '"' . $section . '"', // matches exaclty "123", not just 123. This prevents a match for "1234"
				'compare' => 'LIKE'
			)
        );
		
        $query->set( 'meta_key', 'grupo' );
        $query->set( 'meta_query', $meta_query );
		
    }
}
add_filter( 'pre_get_users', 'filter_users_by_grupo_id' );

// Aumenta o tamanho do seletor de paginas
/**
 * @codeCoverageIgnore
 */
function custom_acf_css() {
	global $typenow;
    if( 'editores_portal' == $typenow ){
		echo '<style>
			.acf-relationship .list {
				height: 500px;
			}
		</style>';
	}

}
add_action('admin_head', 'custom_acf_css');

function clean($string) {
	$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.										 
	return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

// Inclui o JS para alterar o tipo de campo no alt das imagens
/**
 * @codeCoverageIgnore
 */
function custom_admin_js() {
    $url = get_bloginfo('template_directory') . '/js/wp-admin.js';
    echo '"<script type="text/javascript" src="'. $url . '"></script>"';
}
add_action('admin_footer', 'custom_admin_js');

// Altera o texto da label
add_filter(  'gettext',  'dirty_translate'  );
add_filter(  'ngettext',  'dirty_translate'  );
/**
 * @codeCoverageIgnore
 */
function dirty_translate( $translated ) {
    $words = array(
        // 'Texto para traduzir' => 'tradução'
        'Texto alternativo' => 'Descrição para acessibilidade'
    );
	$translated = str_ireplace(  array_keys($words),  $words,  $translated );
	return $translated;
}

// Compare dates ASC
function sort_objects_by_date($a, $b) {
	if($a->post_date == $b->post_date){ return 0 ; }
		return ($a->post_date > $b->post_date) ? -1 : 1;
}

/**
 * @codeCoverageIgnore
 */
function wcag_nav_menu_link_attributes( $atts, $item, $depth ) {

    // Add [aria-haspopup] and [aria-expanded] to menu items that have children
    $item_has_children = in_array( 'menu-item-has-children', $item->classes );
    if ( $item_has_children ) {
        $atts['role'] = "button";
        $atts['aria-expanded'] = "false";
    }

    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'wcag_nav_menu_link_attributes', 10, 4 );

// Desabilitar funcoes de usuarios
/**
 * @codeCoverageIgnore
 */
remove_role( 'subscriber' ); // Assinante
remove_role( 'author' ); // Autor

// Renomear tipo de usuario Contribuidor para Colaborador
/**
 * @codeCoverageIgnore
 */
add_action( 'wp_roles_init', static function ( \WP_Roles $roles ) {
    $roles->roles['contributor']['name'] = 'Colaborador';
    $roles->role_names['contributor'] = 'Colaborador';
} );

function str_replace_assoc(array $replace, $subject) {
	return str_replace(array_keys($replace), array_values($replace), $subject);   
}

function convert_chars_url($string){

	$replace = array(
		'a%CC%80' => '(.*)', // à
		'a%CC%81' => '(.*)', // á
		'a%CC%82' => '(.*)', // â
		'a%CC%83' => '(.*)', // ã
		'à' => '(.*)', // à
		'á' => '(.*)', // á
		'â' => '(.*)', // â
		'ã' => '(.*)', // ã
		'e%CC%80' => '(.*)', // è
		'e%CC%81' => '(.*)', // é
		'e%CC%82' => '(.*)', // ê
		'è' => '(.*)', // è
		'é' => '(.*)', // é
		'ê' => '(.*)', // ê
		'%C3%A9' => '(.*)',
		'i%CC%80' => '(.*)', // ì
		'i%CC%81' => '(.*)', // í
		'i%CC%82' => '(.*)', // î
		'ì' => '(.*)', // ì
		'í' => '(.*)', // í
		'î' => '(.*)', // î
		'o%CC%80' => '(.*)', // ò
		'o%CC%81' => '(.*)', // ó
		'o%CC%82' => '(.*)', // ô
		'o%CC%83' => '(.*)', // õ
		'ò' => '(.*)', // ò
		'ó' => '(.*)', // ó
		'ô' => '(.*)', // ô
		'õ' => '(.*)', // õ
		'u%CC%80' => '(.*)', // ù
		'u%CC%81' => '(.*)', // ú
		'u%CC%82' => '(.*)', // û
		'ù' => '(.*)', // ù
		'ú' => '(.*)', // ú
		'û' => '(.*)', // û
		'ç' => '(.*)', // ç
	);
	$retorno = str_replace_assoc($replace,$string);
	return $retorno;
}

function generate_redirects_htaccess(array $alllinks): string {
    $links = '';
    foreach($alllinks as $link){
        $origem = str_replace(
            ['https://educacao.sme.prefeitura.sp.gov.br', 'http://educacao.sme.prefeitura.sp.gov.br'], 
            '', 
            $link['origem']
        );
        $origem = convert_chars_url($origem);
        
        if (strpos($origem, '/uploads/') === false) {
            $origem = rtrim($origem, '/');
            $origem = '^' . $origem . '(\/|)$';
        }

        $destino = $link['destino'];
        $links .= 'RedirectMatch 301 ' . $origem . ' ' . $destino . PHP_EOL;
    }

    return $links;
}

function redirects_admin() {
    $alllinks = get_field('redirecionar', 'option');

    if (empty($alllinks)) {
        return; // nada a fazer se não houver links
    }

    // Gera o conteúdo dos redirects usando a função separada
    $links_content = generate_redirects_htaccess($alllinks);

    $path = ABSPATH;
    $htaccess_file = $path . '.htaccess';

    // Lê o .htaccess atual
    $htaccess_content = file_get_contents($htaccess_file);

    // Substitui a seção de redirects ou adiciona se não existir
    if (preg_match('/# REDIRECTS[\s\S]+?# END REDIRECTS/si', $htaccess_content)) {
        $filtered_htaccess_content = preg_replace(
            '/\# REDIRECTS[\s\S]+?\# END REDIRECTS/si',
            "# REDIRECTS\n" . $links_content . "\n# END REDIRECTS",
            $htaccess_content
        );
    } else {
        $filtered_htaccess_content = $htaccess_content . "\n# REDIRECTS\n" . $links_content . "\n# END REDIRECTS\n";
    }

    // Escreve de volta no .htaccess
    $fp = fopen($htaccess_file, 'w+');
    if ($fp) {
        fwrite($fp, $filtered_htaccess_content);
        fclose($fp);
    }
}
add_action('acf/save_post', 'redirects_admin'); 

// Ordenar Objeto de Posts por data - ACF
/**
 * @codeCoverageIgnore
 */
add_filter('acf/fields/post_object/query', 'my_acf_fields_post_object_query', 10, 3);
function my_acf_fields_post_object_query( $args, $field, $post_id ) {

    // modify the order
    $args['orderby'] = 'date';
    $args['order'] = 'DESC';

    return $args;
}

// Definir imagem destacada padrao

function fpw_post_info(
    $id,
    $post,
    $hasThumbnailFn = null,
    $getFirstImageFn = null,
    $getPostThumbnailIdFn = null,
    $setPostThumbnailFn = null,
    $deletePostMetaFn = null
) {
    // Definir as funções padrão (funções globais do WordPress)
    $hasThumbnailFn     = $hasThumbnailFn ?? 'has_post_thumbnail';
    $getFirstImageFn    = $getFirstImageFn ?? 'get_first_image';
    $getPostThumbnailIdFn = $getPostThumbnailIdFn ?? 'get_post_thumbnail_id';
    $setPostThumbnailFn   = $setPostThumbnailFn ?? 'set_post_thumbnail';
    $deletePostMetaFn   = $deletePostMetaFn ?? 'delete_post_meta';

    $firstImage = $getFirstImageFn($post->ID);

    if ($hasThumbnailFn($post->ID)) {
        $idThumb = $getPostThumbnailIdFn($post->ID);
        $setPostThumbnailFn($post->ID, $idThumb);
    } elseif ($firstImage) {
        $setPostThumbnailFn($post->ID, $firstImage);
    } else {
        $deletePostMetaFn($post->ID, '_thumbnail_id');
        $setPostThumbnailFn($post->ID, 28528);
    }
}
add_action( 'publish_post', 'fpw_post_info', 10, 2 );
add_action( 'publish_cortesias', 'fpw_post_info', 10, 2 );

// Gerar string aleatoria
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Listar apenas pagina atual e as subpaginas
/**
 * @codeCoverageIgnore
 */
add_filter('acf/fields/relationship/query/name=menu_lateral_principal', 'change_posts_order', 10, 3);
add_filter('acf/fields/relationship/query/name=outros_pagina', 'change_posts_order', 10, 3);
function change_posts_order( $args, $field, $post_id ){

	$pages = array();
	$pages[] = $post_id;

	$getpages = get_pages(array( 'child_of' => $post_id) );

	foreach($getpages as $page){
		$pages[] = $page->ID;
	}

	$args['post__in'] = $pages;

    return $args;
}

add_filter('redirect_canonical','pif_disable_redirect_canonical');
/**
 * @codeCoverageIgnore
 */
function pif_disable_redirect_canonical($redirect_url) {
    if (is_singular()) $redirect_url = false;
return $redirect_url;
}

// Desabilitar colunas do Yoast no listagem de noticias e paginas
add_filter( 'manage_edit-post_columns', 'yoast_seo_admin_remove_columns', 10, 1 );
add_filter( 'manage_edit-page_columns', 'yoast_seo_admin_remove_columns', 10, 1 );
/**
 * @codeCoverageIgnore
 */
function yoast_seo_admin_remove_columns( $columns ) {
  unset($columns['wpseo-score-readability']);
  unset($columns['wpseo-title']);
  unset($columns['wpseo-metadesc']);
  unset($columns['wpseo-focuskw']);
  unset($columns['wpseo-links']);
  unset($columns['wpseo-linked']);
  return $columns;
}

// Move Yoast Meta Box to bottom
/**
 * @codeCoverageIgnore
 */
function yoasttobottom() {
	return 'low';
}

add_filter( 'wpseo_metabox_prio', 'yoasttobottom');

add_action( 'wp_enqueue_scripts', 'load_dashicons_front_end' );
/**
 * @codeCoverageIgnore
 */
function load_dashicons_front_end() {
  wp_enqueue_style( 'dashicons' );
}

/**
 * Relacionamento Reciproco / Grupos e Paginas
 * Entre dois campos de relacionamento que pertencem a diferentes tipos de postagem
 */
// Defina as chaves de campo para os dois campos de relacionamento
$key_a = 'field_5fecb928c7571'; // Grupos
$key_b = 'field_616875a8b6c80'; // Paginas

// Adicione o filtro ao primeiro campo de relacionamento
// A chave deve corresponder a $ key_a acima
/**
 * @codeCoverageIgnore
 */
add_filter(
    'acf/update_value/key=field_5fecb928c7571',
    function ($value, $post_id, $field) use ($key_a, $key_b) {
        return acf_reciprocal_relationship($value, $post_id, $field, $key_a, $key_b);
    },
    10, 5
);

// Adicione o filtro ao segundo campo de relacionamento
// A chave deve corresponder a $ key_b acima
/**
 * @codeCoverageIgnore
 */
add_filter(
    'acf/update_value/key=field_616875a8b6c80',
    function ($value, $post_id, $field) use ($key_a, $key_b) {
        return acf_reciprocal_relationship($value, $post_id, $field, $key_a, $key_b);
    },
    10, 5
);


/**
 * Quando um campo de relacionamento é definido, um relacionamento recíproco
 * também é definido no tipo de post de destino.
 *
 * @param [type] $value
 * @param [type] $post_id
 * @param [type] $field
 * @param [type] $key_a
 * @param [type] $key_b
 * @return void
 */
function acf_reciprocal_relationship($value, $post_id, $field, $key_a, $key_b)
{
    // descobrir em que lado estamos trabalhando e configurar as variáveis
    // $key_a representa o campo para as postagens atuais
    // e $key_b representa o campo em postagens relacionadas
    if ($key_a !== $field['key']) {
        $temp = $key_a;
        $key_a = $key_b;
        $key_b = $temp;
    }

    // obter os dois campos
    // esta funcao do ACF obtem od valores dos campos
    $field_a = acf_get_field($key_a);
    $field_b = acf_get_field($key_b);

    // defina os nomes dos campos para verificar em cada postagem
    $name_a = $field_a['name'];
    $name_b = $field_b['name'];

    // obtem o valor do campo da postagem atual
	// e verifica se ela precisa ser atualizada

	$old_values = get_post_meta($post_id, $name_a, true);
    // verificar se o valor contem um array
    if (!is_array($old_values)) {
        if (empty($old_values)) {
            $old_values = array();
        } else {
            $old_values = array($old_values);
        }
    }
    // define novos valores para $value
    $new_values = $value;
    // verificar se o valor contem um array
    if (!is_array($new_values)) {
        if (empty($new_values)) {
            $new_values = array();
        } else {
            $new_values = array($new_values);
        }
    }


    $add = $new_values;
    $delete = array_diff($old_values, $new_values);

    // reordene os arrays para evitar possiveis erros de indice invalido
    $add = array_values($add);
    $delete = array_values($delete);

    if (!count($add) && !count($delete)) {
        // se nao tiver diferenca
        // nao ha nada pra fazer
        return $value;
    }

    // deleta o primeiro
    // passa por todos os posts que precisam ter a relacao removida
    for ($i=0; $i<count($delete); $i++) {
        $related_values = get_post_meta($delete[$i], $name_b, true);
        if (!is_array($related_values)) {
            if (empty($related_values)) {
                $related_values = array();
            } else {
                $related_values = array($related_values);
            }
        }

        $related_values = array_diff($related_values, array($post_id));
        // insere o novo valor
        update_post_meta($delete[$i], $name_b, $related_values);
        // insere a chave do acf
        update_post_meta($delete[$i], '_'.$name_b, $key_b);
    }


    for ($i=0; $i<count($add); $i++) {
        $related_values = get_post_meta($add[$i], $name_b, true);
        if (!is_array($related_values)) {
            if (empty($related_values)) {
                $related_values = array();
            } else {
                $related_values = array($related_values);
            }
        }
        if (!in_array($post_id, $related_values)) {
            // add new relationship if it does not exist
            $related_values[] = $post_id;
        }
        // atualiza os valores
        update_post_meta($add[$i], $name_b, $related_values);
        // insere a chave do acf
        update_post_meta($add[$i], '_'.$name_b, $key_b);
    }

    return $value;
}

// Desabilitar coluna Descricao para Comprimissos dentro da Agenda
/**
 * @codeCoverageIgnore
 */
add_filter('manage_edit-compromisso_columns', function ( $columns ) 
{
    if( isset( $columns['description'] ) )
        unset( $columns['description'] );   

    return $columns;
} );

// Desabilitar campo Descricao para Comprimissos dentro da Agenda

function hide_description_row() {
    echo "<style> .term-description-wrap { display:none; } </style>";
}

add_action( "compromisso_edit_form", 'hide_description_row');
add_action( "compromisso_add_form", 'hide_description_row');

// Inserir as opções no campo Compromisso no cadastro da Agenda
add_filter( 'acf/load_field/name=compromisso', function( $field ) {
  
	// Get all taxonomy terms
	$compromissos = get_terms( array(
	  'taxonomy' => 'compromisso',
	  'hide_empty' => false
	) );
	
	// Add each term to the choices array.
	// Example: $field['choices']['review'] = Review
	$field['choices']['outros'] = 'Outros';
	foreach ( $compromissos as $type ) {
	  $field['choices'][$type->term_id] = $type->name;
	}
  
	return $field;
} );

// Inserir as opções no campo Endereço no cadastro da Agenda
add_filter( 'acf/load_field/name=endereco_evento', function( $field ) {
  
	// Get all taxonomy terms
	$compromissos = get_terms( array(
	  'taxonomy' => 'endereco',
	  'hide_empty' => false
	) );
	
	// Add each term to the choices array.
	// Example: $field['choices']['review'] = Review
	$field['choices']['outros'] = 'Outros';
	foreach ( $compromissos as $type ) {
	  $field['choices'][$type->term_id] = $type->name;
	}
  
	return $field;
} );

/// Incluir JS de preenchimento e Ajax no Admin
function enqueue_scripts_back_end(){
	//wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/js/my_query.js', array('jquery'));
	
	wp_localize_script( 'ajax-script', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
	
}
add_action('admin_enqueue_scripts','enqueue_scripts_back_end');

// Recupera os valores dentro de Compromisso
add_action( 'wp_ajax_my_action', 'my_action' );
function my_action() {
	

	global $wpdb;
	
	$compromisso = intval( $_POST['compromisso'] );
	
	$pauta_assunto = get_field('pauta_assunto', 'term_' . $compromisso); 
	$participantes_evento = get_field('participantes_evento', 'term_' . $compromisso);
	$endereco_do_evento = get_field('endereco_do_evento', 'term_' . $compromisso);
	
	echo json_encode(array(
		'pauta_assunto' => $pauta_assunto,
		'participantes_evento' => $participantes_evento,
		'endereco_do_evento' => $endereco_do_evento
	));
	
	wp_die();
	
}

// Ordenar Nova Agenda por data por padrão
add_filter( 'parse_query', 'sort_posts_by_meta_value' );
 
function sort_posts_by_meta_value($query) {
    global $pagenow;
    if (is_admin() && $pagenow == 'edit.php' &&
        isset($_GET['post_type']) && $_GET['post_type']=='agendanew' &&
        !isset($_GET['orderby']) )  {
        $query->query_vars['orderby'] = 'meta_value_num date';
        $query->query_vars['meta_key'] = 'data_do_evento';
    }

	return $query;
}

// Ordenar Nova Agenda por data ao clicar em ordenacao
add_filter( 'parse_query', 'sort_agenda_by_date' );
 
function sort_agenda_by_date($query) {
    global $pagenow;
    if (is_admin() && $pagenow == 'edit.php' &&
        isset($_GET['post_type']) && $_GET['post_type']=='agendanew' &&
        isset($_GET['orderby'])  && $_GET['orderby'] == 'data_evento')  {
        $query->query_vars['orderby'] = 'meta_value_num date';
        $query->query_vars['meta_key'] = 'data_do_evento';
    }

	return $query;
}

// Filtrar Nova Agenda por mes / ano
add_filter( 'parse_query', 'filter_agenda_by_date' );
 
function filter_agenda_by_date($query) {
    global $pagenow;
    if (is_admin() && $pagenow == 'edit.php' &&
        isset($_GET['post_type']) && $_GET['post_type']=='agendanew' &&
        isset($_GET['search_year']) )  {
			
			$mesBusca = $_GET['search_month'];
			$anoBusca = $_GET['search_year'];

			$start = $anoBusca . '-' . $mesBusca . '-01';
			$end = $anoBusca . '-' . $mesBusca . '-31';

			$query->query_vars['orderby'] = 'meta_value_num date';
			$query->query_vars['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => 'data_do_evento',
					'value' => $start,
					'compare' => '>=',
					'type' => 'DATE'
				),

				array(
					'key' => 'data_do_evento',
					'value' => $end,
					'compare' => '<=',
					'type' => 'DATE'
				),
			);
    }

	return $query;
}

// Inclusao do filtro por mes / ano
add_action('restrict_manage_posts','filtering_month',10);
function filtering_month($post_type){
    
	if('agendanew' !== $post_type){
      return; //filter your post
    }
        
    //Lista de Meses.
    $meses = array(
		'01' => 'Janeiro',
		'02' => 'Fevereiro',
		'03' => 'Março',
		'04' => 'Abril',
		'05' => 'Maio',
		'06' => 'Junho',
		'07' => 'Julho',
		'08' => 'Agosto',
		'09' => 'Setembro',
		'10' => 'Outubro',
		'11' => 'Novembro',
		'12' => 'Dezembro',
	);

	// Mes Atual
	$month = date('m');

   //build a custom dropdown list of values to filter by
    echo '<select id="my-loc" name="search_month">';    
    foreach($meses as $key => $location){
      $select = ($month == $key) ? ' selected="selected"':'';
      echo '<option value="'.$key.'"'.$select.'>' . $location . ' </option>';
    }
    echo '</select>';


	// Ano Atual
	$year = date('Y');
	$previousyear = $year -1;
	$nextyear = $year +1;

	//build a custom dropdown list of values to filter by
    echo '<select id="my-loc" name="search_year">';        
    echo '<option value="'.$previousyear.'">' . $previousyear . ' </option>';
	echo '<option value="'.$year.'" selected="selected">' . $year . ' </option>';
	echo '<option value="'.$nextyear.'">' . $nextyear . ' </option>'; 
    echo '</select>';
}

add_filter('months_dropdown_results', '__return_empty_array');

// Adicionar busca em Atributos de Paginas > Ascendente 
function custom_scripts_wpse_215576() {
    //Chosen CSS file
    wp_enqueue_style('chose-style', get_template_directory_uri().'/css/chosen.css');
    //Chosen JS file
    wp_enqueue_script( 'chosen-script', get_template_directory_uri() . '/js/chosen.jquery.min.js', array(), '1.4.2', true );
}
add_action( 'admin_enqueue_scripts', 'custom_scripts_wpse_215576' );


// Incluir Meta Key nas buscas
add_action('pre_get_posts', 'my_search_query'); // add the special search fonction on each get_posts query (this includes WP_Query())
function my_search_query($query) {
    if ($query->is_search() and $query->query_vars and $query->query_vars['s'] and $query->query_vars['s_meta_keys']) { // if we are searching using the 's' argument and added a 's_meta_keys' argument
        global $wpdb;
        $search = $query->query_vars['s']; // get the search string
        $ids = array(); // initiate array of martching post ids per searched keyword
        foreach (explode(' ',$search) as $term) { // explode keywords and look for matching results for each
            $term = trim($term); // remove unnecessary spaces
            if (!empty($term)) { // check the the keyword is not empty
                $query_posts = $wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE post_status='publish' AND ((post_title LIKE '%%%s%%') OR (post_content LIKE '%%%s%%'))", $term, $term); // search in title and content like the normal function does
                $ids_posts = [];
                $results = $wpdb->get_results($query_posts);
                if ($wpdb->last_error)
                    die($wpdb->last_error);
                foreach ($results as $result)
                    $ids_posts[] = $result->ID; // gather matching post ids
                $query_meta = [];
                foreach($query->query_vars['s_meta_keys'] as $meta_key) // now construct a search query the search in each desired meta key
					//$where = str_replace("meta_key = 'fx_flex_layout_$", "meta_key LIKE 'fx_flex_layout_%", $where);
					//$where = str_replace("meta_key = 'fx_coluna_1_1_$", "meta_key LIKE 'fx_coluna_1_1_%", $where);
					//$meta_key = str_replace('fx_flex_layout_$_fx_coluna_1_1_$_fx_editor_1_1', 'fx_flex_layout_%_fx_coluna_1_1_%_fx_editor_1_1', $meta_key);
					$query_meta[] = $wpdb->prepare("meta_key='%s' AND meta_value LIKE '%%%s%%'", $meta_key, $term);
                $query_metas = $wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE ((".implode(') OR (',$query_meta)."))");
                $ids_metas = [];
                $results = $wpdb->get_results($query_metas);
                if ($wpdb->last_error)
                    die($wpdb->last_error);
                foreach ($results as $result)
                    $ids_metas[] = $result->post_id; // gather matching post ids
                $merged = array_merge($ids_posts,$ids_metas); // merge the title, content and meta ids resulting from both queries
                $unique = array_unique($merged); // remove duplicates
                if (!$unique)
                    $unique = array(0); // if no result, add a "0" id otherwise all posts wil lbe returned
                $ids[] = $unique; // add array of matching ids into the main array
            }
        }
        if (count($ids)>1)
            $intersected = call_user_func_array('array_intersect',$ids); // if several keywords keep only ids that are found in all keywords' matching arrays
        else
            $intersected = $ids[0]; // otherwise keep the single matching ids array
        $unique = array_unique($intersected); // remove duplicates
        if (!$unique)
            $unique = array(0); // if no result, add a "0" id otherwise all posts wil lbe returned
        unset($query->query_vars['s']); // unset normal search query
        $query->set('post__in',$unique); // add a filter by post id instead
    }
}

// Alterar placeholder cadastro/edicao Agenda do Secretario
function wpb_change_title_text( $title ){
	$screen = get_current_screen(); 
	if  ( 'agendanew' == $screen->post_type ) {
		 $title = 'Digite a data dos compromissos ou nome do evento';
	} 
	return $title;}

add_filter( 'enter_title_here', 'wpb_change_title_text' );

// Incluir div envolta do embed de video automatico do WordPress
add_filter( 'embed_oembed_html', 'tdd_oembed_filter', 10, 4 ) ; 
function tdd_oembed_filter($html, $url, $attr, $post_ID) {
    $return = '<div class="video-container">'.$html.'</div>';
    return $return;
}

// Paginas em rascunho e pendendentes no seletor de subpaginas
add_filter( 'page_attributes_dropdown_pages_args', 'so_3538267_enable_drafts_parents' );
add_filter( 'quick_edit_dropdown_pages_args', 'so_3538267_enable_drafts_parents' );

function so_3538267_enable_drafts_parents( $args )
{
    $args['post_status'] = 'draft,publish,pending,private';
    return $args;
}

// Inclui o JS para alterar o tipo de campo no alt das imagens
function custom_subpage_js() {
	//$url = get_bloginfo('template_directory') . '/js/subpages.js';	
    //echo '"<script type="text/javascript" src="'. $url . '"></script>"';

    echo '<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>';    
	$pagina = $_GET['post'];
	$pages = get_pages('child_of='.$pagina.'&sort_column=title&post_status=draft,publish,pending,private');
	$paginas = '';
	foreach($pages as $page){
		$paginas .= '<input type="checkbox" class="checkboxAll" name="type" value="' . $page->ID . '" /> ' . $page->post_title . '<br>';
	}
	?>
		<script>
			jQuery(document).ready(function($) {				

				function ajaxSubmit(pages) {

					var data = {
						action: 'subpages_private',
						page: pages
					};

					jQuery.ajax({
						type: "POST",
						url: "/wp-admin/admin-ajax.php",
						data: data,
						success: function(data){
							Swal.fire('Páginas alteradas com sucesso!', '', 'success');
						},
						error: function (request, status, error) {
							//alert(request.responseText);
						}
					});

					return false;
				}



				jQuery("#visibility-radio-private").click(function(){

					Swal.fire({
						title: 'Atenção',
						icon: 'question',
						html: '<h3>Esta página possui subpaginas, deseja transformá-las em Privadas?</h3>' +
							  '<div class="pages-modal">' +							  
							  '<?= $paginas; ?>' +
							  '</div>',
						showDenyButton: true,
						showCloseButton: true,
						confirmButtonText: 'Salvar',
						denyButtonText: 'Não alterar',
					}).then((result) => {
						/* Read more about isConfirmed, isDenied below */
						if (result.isConfirmed) {
							var yourArray = []

							jQuery("input:checkbox[name=type]:checked").each(function(){
								yourArray.push($(this).val());
							});

							ajaxSubmit(yourArray);							

						} else if (result.isDenied) {
							Swal.fire('Ação cancelada', '', 'error')
						}
					})
					//ajaxSubmit(); 
				});
			});
		</script>
	<?php
}

// Alerta subpagina
add_action( 'admin_init', 'alert_subpage' );

function alert_subpage() {
    global $pagenow;
    if ( 'post.php' === $pagenow && isset($_GET['post']) && 'page' === get_post_type( $_GET['post'] ) ){
		$pagina = $_GET['post'];

		$pages = get_pages('child_of='.$pagina.'&sort_column=menu_order&post_status=draft,publish,pending,private');

		//echo "<pre>";
		//print_r($pages);
		//echo "</pre>";

		if($pages){
			add_action('admin_footer', 'custom_subpage_js');
		}

    }
}

function subpages_private(){
	$paginas = $_POST['page'];

	foreach($paginas as $pagina){
		$post_data = array(
			'ID' => $pagina,
			'post_status' => 'private'
		);	
		wp_update_post( $post_data );
	}

	print_r($data);

	wp_die();
}
add_action('wp_ajax_subpages_private', 'subpages_private');
add_action('wp_ajax_nopriv_subpages_private', 'subpages_private');

// Incluir Pagina Exportar Usuarios no menu Usuarios
add_action('admin_menu', 'wpdocs_register_my_custom_submenu_page');

function wpdocs_register_my_custom_submenu_page() {
    add_submenu_page(
        'users.php',
        'Exportar Usuarios',
        'Exportar Usuarios',
        'manage_options',
        'export-users',
        'wpdocs_my_custom_submenu_page_callback' );
}

function wpdocs_my_custom_submenu_page_callback() {
    echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
        echo '<h2>Exportar Usuarios</h2><br>';
		$blogusers = get_users( array( 'fields' => array( 'id', 'user_login', 'user_email' ) ) );
		$usuarios = array();

		foreach($blogusers as $user){
			$user_meta = get_userdata($user->id);
			$user_roles = $user_meta->roles;
			$setor = get_field('setor', 'user_'. $user->id );
			$grupos = get_field('grupo', 'user_'. $user->id );
			$grupoTitle = '';
			$i = 0;
			if($grupos && $grupos != '' && $grupos[0] != ''){
				foreach($grupos as $grupo){
					if($i == 0){
						$grupoTitle .= get_the_title($grupo);
					} else {
						$grupoTitle .= ', ' . get_the_title($grupo);
					}
					$i++;
				}				
			}

			$usuarios[] = array(
				'id' => $user->id,
				'login' => $user->user_login,
				'email' => $user->user_email,
				'funcao' => $user_roles[0],
				'grupos' => $grupoTitle,
				'setor' => $setor
			);

		}

		//echo "<pre>";
		//print_r($usuarios);
		//echo "</pre>";
	?>

		<form action="<?= get_template_directory_uri(); ?>/export-users.php">
			<select name="funcao" id="">
				<option value="all">Todos</option>
				<option value="administrator">Administrador</option>
				<option value="editor">Editor</option>
				<option value="contributor">Colaborador</option>
				<option value="assessor">Assessor</option>
			</select>
			<input type="submit" value="Exportar" class="button action">
		</form>

	<?php
    echo '</div>';
}

// Incluir Pagina Importar Usuarios no menu Usuarios
add_action('admin_menu', 'cadastro_usuarios_core_sso');

function cadastro_usuarios_core_sso() {
    add_submenu_page(
        'users.php',
        'Importar Usuarios',
        'Importar Usuarios',
        'manage_options',
        'import-users',
        'incluir_cadastro_usuarios_core_sso' );
}

function incluir_cadastro_usuarios_core_sso(){
	include('includes/usuarios/cadastro_usuarios.php');
}

// Incluir Pagina Atualizar Usuarios no menu Usuarios
add_action('admin_menu', 'atualizar_usuarios_core_sso');

function atualizar_usuarios_core_sso() {
    add_submenu_page(
        'users.php',
        'Atualizar Usuarios',
        'Atualizar Usuarios',
        'manage_options',
        'update-users',
        'incluir_atualizar_usuarios_core_sso' );
}

function incluir_atualizar_usuarios_core_sso(){
	include('includes/usuarios/atualizar_usuarios.php');
}

// Incluir Pagina Atualizar Usuarios no menu Usuarios
add_action('admin_menu', 'cadastrar_assessores');

function cadastrar_assessores() {
    add_submenu_page(
        'users.php',
        'Cadastrar Assessores',
        'Cadastrar Assessores',
        'edit_pages',
        'cadastro-assessores',
        'incluir_cadastrar_assessores' );
}

function incluir_cadastrar_assessores(){
	include('includes/usuarios/cadastro_assessores.php');
}

// Incluir Pagina Remover Usuarios
add_action('admin_menu', 'remover_usuarios');

function remover_usuarios() {
    add_submenu_page(
        'users.php',
        'Limpar Duplicados por RF',
        'Limpar Duplicados RF',
        'manage_options',
        'limpar-duplicados-rf',
        'render_limpar_duplicados_rf_page' );
}

function render_limpar_duplicados_rf_page(){
	include('includes/usuarios/limpar_duplicados.php');
}

add_action('pre_get_posts', 'my_make_search_exact', 10);
function my_make_search_exact($query){

    if(!is_admin() && $query->is_main_query() && $query->is_search) :
        $query->set('exact', true);
    endif;

}

// define the media_send_to_editor callback 
function filter_media_send_to_editor( $html, $send_id, $attachment ) { 
    if (get_post_mime_type( $send_id ) == "application/pdf") {
		//$meta = get_post_meta( $send_id, '_wp_attachment_metadata', true );
		//$meta = wp_get_attachment_image_src( $send_id, 'medium' );
		//$html .= print_r($meta, true);

		$arquivo = wp_get_attachment_image_src( $send_id, 'full' );
		$arquivoMedium = wp_get_attachment_image_src( $send_id, 'medium' );
		
		if($attachment['url']){			
			$html = '<a href="' . $attachment['url'] . '"><img class="size-medium img-fluid" width="' . $arquivoMedium[1] . '" height="' . $arquivoMedium[2] . '" src="' . $arquivo[0] . '"></a>';
		} else {
			$html = '<img class="size-medium img-fluid" width="' . $arquivoMedium[1] . '" height="' . $arquivoMedium[2] . '" src="' . $arquivo[0] . '">';
		}
	}
	
    return $html; 
};

add_filter( 'media_send_to_editor', 'filter_media_send_to_editor', 11, 3 );

// Inclui link esqueceu a senha
add_filter('login_form_middle','lost_pass');
function lost_pass(){
    
	//Output your HTML
	$link = get_field('link', $post_id);
	$additional_field = '';
	if($link){
     	$additional_field .= '<div class="lost-pass">
		<p class="pass-text m-0">Na senha, digite a mesma senha do Sistema de Gestão Pedagógica (SGP) e Plateia. Caso esqueça sua senha e necessite redefinir, a mesma será aplicada
		 para os outros acessos (Portais e Sistemas) da SME.</p>
        <p><a href="' . $link . '">Esqueceu sua senha?</a></p>
		
     </div>';
	}
	
	$additional_field .= '<div class="login-custom-field-wrapper">
        <input type="hidden" value="1" name="login_page"></label>
    </div>';

    return $additional_field;
}

// Retorna dia da semana
function getDay($dia_num) {
    $diasMapa = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado');
    
    if (isset($diasMapa[$dia_num])) {
        return $diasMapa[$dia_num];
    }

    return null;
}

/**
 * Retorna a primeira letra de cada palavra de uma string, ignorando artigos e preposições
 * 
 * @param string $words String com palavras separadas por espaços
 * @return string String com as primeiras letras de cada palavra (exceto stop words)
 * @testedBy FirstLetterTest
 * @since 1.0.0
 */
function firstLetter( string $words ) {
	$acronym = "";
	$palavras = explode(" ", $words);
	
	// Lista de palavras a ignorar (artigos, preposições, etc.)
	$stopWords = ['de', 'da', 'do', 'das', 'dos', 'as', 'os', 'em', 'na', 'no', 'nas', 'nos', 'para', 'por', 'com', 'sem'];
	
	foreach ($palavras as $w) {
		if( empty( $w ) ) {
			continue;
		}
		
		// Converte para minúsculo para comparação e verifica se é stop word
		$wLower = mb_strtolower(trim($w));
		if( in_array( $wLower, $stopWords, true ) ) {
			continue;
		}

		$acronym .= $w[0];
	}
	
	return $acronym;
}

/**
 * Converte o número do mês para o formato abreviado em Português.
 * 
 * @param string $mes Número do mês
 * @return string String com o mês abreviado
 * @testedBy ConverterMesTest
 * @since 1.0.0
 */
function converter_mes($mes){
	switch ($mes) {
		case '01':
			return "Jan";
		case '02':
			return "Fev";
		case '03':
			return "Mar";
		case '04':
			return "Abr";
		case '05':
			return "Mai";
		case '06':
			return "Jun";
		case '07':
			return "Jul";
		case '08':
			return "Ago";
		case '09':
			return "Set";
		case '10':
			return "Out";
		case '11':
			return "Nov";
		case '12':
			return "Dez";
		default:
			return "";
	}
}

// Pega o nome da categoria no Acervo Digital
function get_tax_name($tax, $id){

	$url = 'https://hom-acervodigital.sme.prefeitura.sp.gov.br/wp-json/wp/v2/' . $tax . '/' . $id ;

	$cURLConnection = curl_init();
	curl_setopt($cURLConnection, CURLOPT_URL, $url);
	curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

	$taxList = curl_exec($cURLConnection);
	curl_close($cURLConnection);

	$taxResponse = json_decode($taxList);
	return $taxResponse->name;
}

// Pega o a url do arquivo no Acervo Digital
function get_file_url($id){
	$url = 'https://hom-acervodigital.sme.prefeitura.sp.gov.br/wp-json/wp/v2/media/' . $id ;

	$cURLConnection = curl_init();
	curl_setopt($cURLConnection, CURLOPT_URL, $url);
	curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

	$mediaList = curl_exec($cURLConnection);
	curl_close($cURLConnection);

	$mediaResponse = json_decode($mediaList);
	return $mediaResponse->source_url;
}

// Incluir a opacao de Limpar o contador dos usuarios
add_filter('bulk_actions-users', function($bulk_actions) {
	$bulk_actions['limpar-contator'] = __('Limpar Contator', 'txtdomain');
	return $bulk_actions;
});

// Acao para limpar o contador e a resposta de feedback
add_filter('handle_bulk_actions-users', function($redirect_url, $action, $users) {
	if ($action == 'limpar-contator') {
		//print_r($users);
		foreach ($users as $user_id) {
			update_user_meta($user_id, 'wp_login_count', '');
			update_user_meta($user_id, 'feed_resp', '');
		}
		$redirect_url = add_query_arg('activate-user', count($users), $redirect_url);
	}
	return $redirect_url;
}, 10, 3);

function destroy_sessions() {
	$sessions->destroy_all();//destroys all sessions
	wp_clear_auth_cookie();//clears cookies regarding WP Auth
}
add_action('wp_logout', 'destroy_sessions');

// Validar Senha
add_action('wp_ajax_valida_user','valida_user');
add_action('wp_ajax_nopriv_valida_user','valida_user');
function valida_user(){
	$curl = curl_init();

	curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://hom-smeintegracaoapi.sme.prefeitura.sp.gov.br/api/v1/autenticacao',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>'{
				"login": "' . $_POST['user'] . '",
				"senha": "' . $_POST['atual'] . '"
			}',
			CURLOPT_HTTPHEADER => array(
					'x-api-eol-key: fe8c65abfac596a39c40b8d88302cb7341c8ec99',
					'Content-Type: application/json-patch+json'
			),
	));

	$response = curl_exec($curl);
	$info = curl_getinfo($curl);

	curl_close($curl);
	//print_r($info);
	echo $info['http_code'];
	die();
}

// Alterar Senha
add_action('wp_ajax_altera_senha','altera_senha');
add_action('wp_ajax_nopriv_altera_senha','altera_senha');
function altera_senha(){
	$curl = curl_init();

	$retorno = array();
	$campos = array('Usuario' => $_POST['user'],'Senha' => $_POST['nova1']);

	// Given password
	$password = $_POST['nova1'];

	// Validate password strength
	$uppercase = preg_match('@[A-Z]@', $password);
	$lowercase = preg_match('@[a-z]@', $password);
	$number    = preg_match('@[0-9]@', $password);
	$specialChars = preg_match('@[^\w]@', $password);

	if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8 || strlen($password) > 12) {
		$retorno['code'] = 401;
		$retorno['body'] = 'A senha deve ter entre 8 e 12 caracteres e deve incluir pelo menos uma letra maiúscula, um número e um caractere especial.';
	}else{
		curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://hom-smeintegracaoapi.sme.prefeitura.sp.gov.br/api/AutenticacaoSgp/AlterarSenha',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $campos,
				CURLOPT_HTTPHEADER => array(
						'x-api-eol-key: fe8c65abfac596a39c40b8d88302cb7341c8ec99',
						'Content-Type: multipart/form-data'
				),
		));
		
		$response = curl_exec($curl);
		$info = curl_getinfo($curl);

		curl_close($curl);
		//print_r($info);
		//echo $info['http_code'];
		
		$retorno['code'] = $info['http_code'];
		$retorno['body'] = $response;
	}

	
	echo json_encode($retorno);
	die();
}

// Função para alterar a senha do usuário via AJAX
function change_user_password_callback() {    

    // Obtém o ID do usuário e a nova senha do parâmetro da requisição
    $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
    $new_password = isset( $_POST['new_password'] ) ? sanitize_text_field( $_POST['new_password'] ) : '';

    // Verifica se o ID do usuário e a nova senha foram fornecidos
    if ( empty( $user_id ) || empty( $new_password ) ) {
        wp_send_json_error( 'ID do usuário e nova senha são obrigatórios.' );
    }

    // Altera a senha do usuário
    $result = wp_set_password( $new_password, $user_id );

    // Verifica se a senha foi alterada com sucesso
    if ( is_wp_error( $result ) ) {
        wp_send_json_error( 'Erro ao alterar a senha do usuário.' );
    }

    // Envie uma resposta de sucesso
    wp_send_json_success( 'Senha do usuário alterada com sucesso.' );
}

// Registra a função AJAX no WordPress
add_action( 'wp_ajax_change_user_password', 'change_user_password_callback' );

// Alterar cor padrao do admin
add_filter( 'get_user_option_admin_color', 'update_user_option_admin_color', 5 );

function update_user_option_admin_color( $color_scheme ) {
    $color_scheme = 'sunrise';

    return $color_scheme;
}

if ( (! empty($GLOBALS['pagenow']) && 'post.php' === $GLOBALS['pagenow']) ||  (! empty($GLOBALS['pagenow']) && 'edit.php' === $GLOBALS['pagenow']))
    add_action('admin_footer', 'trash_click_message');
function trash_click_message() {
    echo <<<JQUERY
<script>
	jQuery( function($) {       
		$('.edit-php a.submitdelete, .post-php a.submitdelete').click( function( event ) {
			if( ! confirm( 'Você realmente deseja mover para a lixeira?' ) ) {
				event.preventDefault();
			}           
		});
	});
</script>
JQUERY;
}

function wpse95147_filter_wp_title( $title ) {
    if ( is_single() || ( is_home() && !is_front_page() ) || ( is_page() && !is_front_page() ) ) {
        $title = single_post_title( '', false );
    }
    return $title;
}
add_filter( 'wp_title', 'wpse95147_filter_wp_title' );

function post_like_table_create() {

	global $wpdb;
	$table_name = $wpdb->prefix. "post_like_table";
	global $charset_collate;
	$charset_collate = $wpdb->get_charset_collate();
	global $db_version;
	
	if( $wpdb->get_var("SHOW TABLES LIKE '" . $table_name . "'") != $table_name)
	{ $create_sql = "CREATE TABLE " . $table_name . " (
	id INT(11) NOT NULL auto_increment,
	postid INT(11) NOT NULL ,
	
	clientip VARCHAR(40) NOT NULL ,
	
	PRIMARY KEY (id))$charset_collate;";
	require_once(ABSPATH . "wp-admin/includes/upgrade.php");
	dbDelta( $create_sql );
	}
	
	
	//register the new table with the wpdb object
	if (!isset($wpdb->post_like_table))
	{
	$wpdb->post_like_table = $table_name;
	//add the shortcut so you can use $wpdb->stats
	$wpdb->tables[] = str_replace($wpdb->prefix, '', $table_name);
	}
	
}
add_action( 'init', 'post_like_table_create');
	
// Add the JS
function theme_name_scripts() {
	wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/post-like.js', array('jquery'), '1.0.0', true );
	wp_localize_script( 'script-name', 'MyAjax', array(
	// URL to wp-admin/admin-ajax.php to process the request
	'ajaxurl' => admin_url( 'admin-ajax.php' ),
	// generate a nonce with a unique ID "myajax-post-comment-nonce"
	// so that you can check it later when an AJAX request is sent
	'security' => wp_create_nonce( 'my-special-string' )
	));
}
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
// The function that handles the AJAX request
	
function get_client_ip() {
	if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	{
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	{
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
	
function post_like_callback() {
	check_ajax_referer( 'my-special-string', 'security' );
	$postid = intval( $_POST['postid'] );
	$clientip = get_client_ip();
	$like = 0;
	$dislike = 0;
	$like_count = 0;

	//check if post id and ip present
	global $wpdb;
	$row = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$postid' AND clientip = '$clientip'");

	if(empty($row)){
		//insert row
		$wpdb->insert( $wpdb->post_like_table, array( 'postid' => $postid, 'clientip' => $clientip ), array( '%d', '%s' ) );
		//echo $wpdb->insert_id;
		$like=1;
	}

	if(!empty($row)){
		//delete row
		$wpdb->delete( $wpdb->post_like_table, array( 'postid' => $postid, 'clientip'=> $clientip ), array( '%d','%s' ) );
		$dislike = 1;
	}

	//calculate like count from db.
	$totalrow = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$postid'");
	$total_like = $wpdb->num_rows;
	$data = array( 'postid' => $postid,'likecount' => $total_like, 'clientip' => $clientip, 'like' => $like, 'dislike' => $dislike);
	echo json_encode($data);
	//echo $clientip;
	die(); // this is required to return a proper result
}

add_action( 'wp_ajax_post_like', 'post_like_callback' );
add_action( 'wp_ajax_nopriv_post_like', 'post_like_callback' );

add_filter( 'ajax_query_attachments_args', 'filterMediaLibrary', 10, 1 );
//add_action( 'pre_get_posts', 'filterMediaLibrary' );
function filterMediaLibrary($query = array()) {
    $query['post_parent__not_in'] = array(1454);
    return $query;
}

function author_filter($query) {
    //if ( is_admin() ) {
        //$query->set('post_parent__not_in', 1454);
    //}
	//return $query;
}
add_action('pre_get_posts','author_filter');

add_filter('get_avatar', 'tsm_acf_profile_avatar', 10, 5);
function tsm_acf_profile_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = '';

    // Get user by id or email
    if ( is_numeric( $id_or_email ) ) {
        $id   = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );
    } elseif ( is_object( $id_or_email ) ) {
        if ( ! empty( $id_or_email->user_id ) ) {
            $id   = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }
    } else {
        $user = get_user_by( 'email', $id_or_email );
    }
    if ( ! $user ) {
        return $avatar;
    }
    // Get the user id
    $user_id = $user->ID;
    // Get the file id
    $image_id = get_user_meta($user_id, 'imagem', true); // CHANGE TO YOUR FIELD NAME
	//$image_id = get_field('imagem', 'user_' . $user_id);
    // Bail if we don't have a local avatar
    if ( ! $image_id ) {
        return $avatar;
    }	
    // Get the file size
    $image_url  = wp_get_attachment_image_src( $image_id, 'thumbnail' ); // Set image size by name
    // Get the file url
    $avatar_url = $image_url[0];
    // Get the img markup
    $avatar = '<img alt="' . $alt . '" src="' . $avatar_url . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '"/>';
    // Return our new avatar
    return $avatar;
}

function revcon_change_post_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Sorteios';
    $submenu['edit.php'][5][0] = 'Sorteios';
    $submenu['edit.php'][10][0] = 'Add Sorteio';
    $submenu['edit.php'][16][0] = 'Locais';
	$menu[5][6] = 'dashicons-tickets-alt'; 
}

function revcon_change_post_object() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Sorteios';
    $labels->singular_name = 'Sorteio';
    $labels->add_new = 'Add Sorteio';
    $labels->add_new_item = 'Add Sorteio';
    $labels->edit_item = 'Editar Sorteio';
    $labels->new_item = 'Sorteios';
    $labels->view_item = 'Visualizar Sorteios';
    $labels->search_items = 'Buscar Sorteios';
    $labels->not_found = 'Nenhum Sorteio encontrado';
    $labels->not_found_in_trash = 'Nenhum Sorteio encontrado na lixeira';
    $labels->all_items = 'Todos os Sorteios';
    $labels->menu_name = 'Sorteios';
    $labels->name_admin_bar = 'Sorteios';
}

function rename_post_tags() {
    global $wp_taxonomies;
    
    if (!empty($wp_taxonomies['post_tag']->labels)) {
        $wp_taxonomies['post_tag']->labels->name = 'Locais';
        $wp_taxonomies['post_tag']->labels->menu_name = 'Locais';
        $wp_taxonomies['post_tag']->labels->singular_name = 'Local';
    }
}
add_action('init', 'rename_post_tags');
 
add_action( 'admin_menu', 'revcon_change_post_label' );
add_action( 'init', 'revcon_change_post_object' );

// Incluir um seletor na listagem de paginas no admin baseado nos modelos de paginas disponivel no tema
add_action( 'restrict_manage_posts', 'wp_page_model_filter_manage_posts' );
function wp_page_model_filter_manage_posts(){
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    //adicionar o filtro somente em paginas
    if ('page' == $type){
        //pegar os valores para serem exibidos
        //formato 'label' => 'valor'
		$templates = wp_get_theme()->get_page_templates();
        $current_v = isset($_GET['modelo_pagina'])? $_GET['modelo_pagina']:'';
        ?>
        <select name="modelo_pagina">
        <option value=""><?php _e('Todos os modelos ', 'wose45436'); ?></option>
		<option value="default">Modelo Padrão</option>
        <?php
            
            foreach ($templates as $label => $value) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $label,
                        $label == $current_v? ' selected="selected"':'',
                        $value
                    );
                }
        ?>
        </select>
        <?php
    }
}

// Quando o filtro de modelos for acionado será incluido na query para exibir a lista baseado no modelo
add_filter( 'parse_query', 'wp_page_model_filter' );
function wp_page_model_filter( $query ){
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if ( 'page' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['modelo_pagina']) && $_GET['modelo_pagina'] != '') {
        $query->query_vars['meta_key'] = '_wp_page_template';
        $query->query_vars['meta_value'] = $_GET['modelo_pagina'];
    }
}

function sample_admin_notice__success() {
    $screen = get_current_screen();
    // Array com os CPTs desejados
    $allowed_post_types = array( 'agendanew');

    // Verifique se o tipo de post atual está na lista de tipos de post permitidos
    if ($screen && $screen->post_type && in_array($screen->post_type, $allowed_post_types) && ($screen->base === 'post' && ($screen->action === 'add' || $screen->action === 'edit' || $screen->action === '')) ) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Digite a data dos compromissos ou nome do evento!', 'sample-text-domain' ); ?></p>
        </div>
        <?php
    }
}
add_action( 'admin_notices', 'sample_admin_notice__success' );

// Função para adicionar notificação quando um comentário é respondido
function adicionar_notificacao_resposta_comentario($comment_id, $comment) {
    // Verifica se o comentário é uma resposta a outro comentário
    if ($comment->comment_parent) {
        // Obtém o comentário pai
        $parent_comment = get_comment($comment->comment_parent);

        // Verifica se o autor do comentário pai é um usuário logado
        if ($parent_comment->user_id) {
            $user_id = $parent_comment->user_id;

            // Verifica se o usuário que está respondendo é o mesmo que fez o comentário original
            if ($comment->user_id != $user_id) {
                
				// Obtém o post associado ao comentário
                $post_id = $comment->comment_post_ID;
                $post_title = get_the_title($post_id);
				if (strlen($post_title) > 50) {
					$post_title = substr($post_title, 0, 50) . '...';
				}

				// Gera um ID único para a notificação
				$notificacao_id = uniqid();

                // Cria a notificação com o título do post
                $notificacao = array(
					'id' => $notificacao_id, // Adiciona um ID único
					'mensagem' => 'Seu comentário em "' . $post_title . '" foi respondido.',
					'link' => get_comment_link($comment_id),
					'timestamp' => current_time('timestamp'), // Adiciona o timestamp atual
					'lida' => false // Campo para indicar se a notificação foi lida
				);

                // Obtém as notificações existentes do usuário
                $notificacoes = get_user_meta($user_id, 'notificacoes', true);

                // Se não houver notificações, inicializa um array vazio
                if (empty($notificacoes)) {
                    $notificacoes = array();
                }

                // Adiciona a nova notificação ao array
                array_push($notificacoes, $notificacao);

                // Atualiza as notificações no banco de dados
                update_user_meta($user_id, 'notificacoes', $notificacoes);
            }
        }
    }
}
add_action('wp_insert_comment', 'adicionar_notificacao_resposta_comentario', 10, 2);

// Shortcode para exibir notificações
function exibir_notificacoes_usuario() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $notificacoes = get_user_meta($user_id, 'notificacoes', true);

        if (!empty($notificacoes)) {
			
			usort($notificacoes, function($a, $b) {
				return $b['timestamp'] - $a['timestamp'];
			});

            echo '<ul class="list-group list-group-flush m-0">';
				foreach ($notificacoes as $notificacao) {
					
					// Adiciona uma classe CSS para notificações lidas
					$classe_lida = $notificacao['lida'] ? 'notificacao-lida' : '';

					// Separa o link do comentário e o hash
					$link_comentario = $notificacao['link'];
					$url_sem_hash = strtok($link_comentario, '#'); // Remove o hash da URL
					$hash = parse_url($link_comentario, PHP_URL_FRAGMENT); // Obtém o hash
			
					// Adiciona o parâmetro notificacao_id antes do hash
					$link_notificacao = $url_sem_hash . '?notificacao_id=' . $notificacao['id'];
					if ($hash) {
						$link_notificacao .= '#' . $hash; // Adiciona o hash de volta
					}

					$icone_lida = $notificacao['lida'] ? ' <i class="fa fa-check" aria-hidden="true"></i>' : '';

					echo '<li class="list-group-item ' . esc_attr($classe_lida) . '"><a href="' . esc_url($link_notificacao) . '">					
							<p class="mb-1">' . esc_html($notificacao['mensagem']) . $icone_lida . '</p>
												
						</a><small>' . date('d/m/Y H:i', $notificacao['timestamp']) . '</small></li>';
				}
            echo '</ul>';

			
            echo '<div class="text-center"><a class="btn btn-danger mt-3 mb-1" href="?limpar_notificacoes=1">Limpar Notificações</a></div>';
			
        } else {
            echo '<p class="px-3 mt-3">Nenhuma notificação nova.</p>';
        }
    } else {
        echo '<p class="px-3 mt-3">Por favor, faça login para ver suas notificações.</p>';
    }
}
add_shortcode('notificacoes', 'exibir_notificacoes_usuario');

// Função para limpar notificações
function limpar_notificacoes() {
    if (is_user_logged_in() && isset($_GET['limpar_notificacoes'])) {
		$url_sem_parametros = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url_sem_parametros = strtok($url_sem_parametros, '?'); // Remove os parâmetros GET

        $user_id = get_current_user_id();
        delete_user_meta($user_id, 'notificacoes');
        wp_redirect($url_sem_parametros); // Redireciona de volta para a página atual
        exit;
    }
}
add_action('init', 'limpar_notificacoes');

function marcar_notificacao_como_lida() {
	
    if (is_user_logged_in() && isset($_GET['notificacao_id'])) {		
        $user_id = get_current_user_id();
        $notificacao_id = $_GET['notificacao_id'];

        // Obtém as notificações do usuário
        $notificacoes = get_user_meta($user_id, 'notificacoes', true);

        if (!empty($notificacoes)) {
            // Procura a notificação pelo ID e marca como lida
            foreach ($notificacoes as &$notificacao) {
                if ($notificacao['id'] === $notificacao_id) {
                    $notificacao['lida'] = true;
                    break;
                }
            }

            // Atualiza as notificações no banco de dados
            update_user_meta($user_id, 'notificacoes', $notificacoes);
        }

        // Redireciona para a URL sem o parâmetro notificacao_id
        $url_sem_parametros = strtok($_SERVER['REQUEST_URI'], '?');
        wp_redirect($url_sem_parametros);
        exit;
    }
}
add_action('init', 'marcar_notificacao_como_lida');

// Função para formatar datas no estilo desejado
function formatarData($data, $formato) {
	$formatter = new IntlDateFormatter(
		'pt_BR',
		IntlDateFormatter::LONG,
		IntlDateFormatter::NONE,
		'America/Sao_Paulo',
		IntlDateFormatter::GREGORIAN
	);
	$formatter->setPattern($formato);
	return $formatter->format($data);
}

function load_posts_by_ajax_callback() {
    check_ajax_referer('load_more_posts', 'security');

    // Validação dos dados
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $searchTerm = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';
    $dateIni = isset($_POST['date_ini']) ? sanitize_text_field($_POST['date_ini']) : '';
    $dateEnd = isset($_POST['date_end']) ? sanitize_text_field($_POST['date_end']) : '';

    // Argumentos da query
    $args = array(
        'post_type' => 'agendanew',
        'posts_per_page' => 10,
        'paged' => $paged,
        's' => $searchTerm, // Termo de busca
    );

    $date_ini = isset($dateIni) ? $dateIni : null;
	$date_fin = isset($dateEnd) ? $dateEnd : null;

	// Configura a meta_query para filtrar pelas datas
	$meta_query = [
		'relation' => 'OR', // Para eventos de data única ou periódicos
	];

	// Eventos de data única
	if ($date_ini || $date_fin) {
		$meta_query[] = [
			'key' => 'data_do_evento',
			'value' => array_filter([$date_ini, $date_fin]), // Remove valores nulos
			'compare' => $date_ini && $date_fin ? 'BETWEEN' : ($date_ini ? '>=' : '<='), // Ajusta o operador
			'type' => 'DATE',
		];
	}

	// Eventos periódicos
	if ($date_ini || $date_fin) {
		$meta_query[] = [
			'relation' => 'AND',
			[
				'key' => 'data_do_evento',
				'value' => $date_fin ?: $date_ini, // Use date_ini se date_fin for vazio
				'compare' => '<=', // Data inicial não pode ser maior que a data final fornecida
				'type' => 'DATE',
			],
			[
				'key' => 'data_evento_final',
				'value' => $date_ini ?: $date_fin, // Use date_fin se date_ini for vazio
				'compare' => '>=', // Data final não pode ser menor que a data inicial fornecida
				'type' => 'DATE',
			],
		];
	}

	// Configura os argumentos da query
	$args['meta_query'] = $meta_query;
	$args['orderby'] = 'meta_value';
	$args['meta_key'] = 'data_do_evento';
	$args['order'] = 'DESC';
	$args['meta_type'] = 'DATE';

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
		?>
			<div class="no-results">
				<h2 class="search-title">
					<span class="azul-claro-acervo"><strong>0</strong></span><strong> 
						resultados</strong>
				</h2>											
				<p>Não há conteúdo disponível para o termo buscado. <br>Por favor faça uma nova busca ou clique <button id="tab-conteudo">aqui</button> e busque pelo conteúdo.</p>
				<img src="<?php echo get_template_directory_uri(); ?>/img/search-empty.png" alt="Imagem ilustrativa para nenhum resultado de busca encontrado" />
			</div>	
		<?php
       
        wp_die();
    }

    // Exibe os posts
    echo '<div class="posts-container">';
    while ( $query->have_posts() ) : $query->the_post();
		?>
            <div class="calendario-categ">
				<p>Categoria: Calendário Escolar</p>
			</div>
			<div class="calendario-busca recado">
				<div class="row">
					<div class="col-3 col-md-2 img-column">												
						<img src="<?= get_template_directory_uri(); ?>/img/calendar1.png" class="img-fluid rounded" alt="Imagem de ilustração categoria">
					</div>

					<div class="col-9 col-md-10">
						
						<p class="data">
							<?php
								$tipo = get_field('tipo_de_data', get_the_ID());
								if($tipo){														

									$dataIni = get_field('data_do_evento', get_the_ID());
									$dataFin = get_field('data_evento_final', get_the_ID());															

									// Criar objetos DateTime a partir das strings de data
									$dataInicial = DateTime::createFromFormat('d/m/Y', $dataIni);
									$dataFinal = DateTime::createFromFormat('d/m/Y', $dataFin);

									// Verificar se as datas estão no mesmo mês
									if ($dataInicial->format('mY') === $dataFinal->format('mY')) {
										// Mesmo mês
										$resultado = sprintf(
											'De %d à %d de %s de %d',
											$dataInicial->format('j'), // Dia inicial
											$dataFinal->format('j'),   // Dia final
											formatarData($dataInicial, 'MMMM'), // Nome do mês
											$dataInicial->format('Y') // Ano
										);
									} else {
										// Meses distintos
										$resultado = sprintf(
											'De %d de %s à %d de %s de %d',
											$dataInicial->format('j'), // Dia inicial
											formatarData($dataInicial, 'MMMM'), // Nome do mês inicial
											$dataFinal->format('j'),   // Dia final
											formatarData($dataFinal, 'MMMM'),   // Nome do mês final
											$dataInicial->format('Y') // Ano (assumindo que o ano é o mesmo para ambas)
										);
									}

									// Exibir o resultado
									echo ucfirst($resultado); // Capitaliza a primeira letra
									
								} else {
									$data = get_field('data_do_evento', get_the_ID());
							
									// Criar um objeto DateTime
									$dateTime = DateTime::createFromFormat('d/m/Y', $data);

									// Configurar o formato desejado
									$formatter = new IntlDateFormatter(
										'pt_BR', // Idioma e localidade
										IntlDateFormatter::FULL, // Formato da data (completo)
										IntlDateFormatter::NONE, // Sem horário
										'America/Sao_Paulo', // Timezone
										IntlDateFormatter::TRADITIONAL
									);

									// Exibir a data formatada
									echo $formatter->format($dateTime);
								}

								//print_r($datas);
							?>
						</p>

						
						<h2><?= get_the_title(); ?></h2>																							
						
						<hr>
						<a class="btn-collapse collapsed" data-toggle="collapse" href="#collapse<?= get_the_ID(); ?>" role="button" aria-expanded="false" aria-controls="collapse<?= get_the_ID(); ?>">
							<span class="button-more">ver mais <i class="fa fa-chevron-down" aria-hidden="true"></i></span><span class="button-less">ver menos <i class="fa fa-chevron-up" aria-hidden="true"></i></span>
						</a> 
						
					</div>
				</div>

				<div class="collapse" id="collapse<?= get_the_ID(); ?>">
					<div class="agenda">
						<?php
							$eventos = get_field('eventos_do_dia', get_the_ID());
							
							if($eventos){														

								foreach($eventos as $evento){
									
									if($evento['hora_evento'])
										echo $evento['hora_evento'] . ' - ';

									if($evento['fim_evento'])
										echo $evento['fim_evento'];

									if($evento['hora_evento'] || $evento['fim_evento'])
											echo ' | ';

									if($evento['nome_compromisso'])
										echo $evento['nome_compromisso'];

									if($evento['pauta_assunto'])
										echo '<div class="local"><strong>Descrição:</strong> ' . $evento['pauta_assunto'] . '</div>';
									
									if($evento['digite_o_endereco_do_evento'])
										echo '<div class="local"><strong>Local:</strong> ' . $evento['digite_o_endereco_do_evento'] . '</div>';
									
									if($evento['participantes_evento'])
										echo '<div class="local"><strong>Participantes:</strong> ' . $evento['participantes_evento'] . '</div>';

								} // end foreach
							} // end if
							
							
						?>
					</div>   
				</div>

			</div>
		<?php
        endwhile;
    echo '</div>';

    // Paginação numérica
    echo '<div class="pagination-prog">';
    echo '<div class="wp-pagenavi text-center">';
    $big = 999999999;
    echo paginate_links(array(
        'base'    => home_url('/page/%#%/'),
        'format'  => '?paged=%#%',
        'current' => max(1, $paged),
        'total'   => $query->max_num_pages,
        'prev_next' => true,
        'type'    => 'plain',
    ));
    echo '</div>';
	echo '</div>';

    wp_die();
}
add_action('wp_ajax_load_posts_by_ajax', 'load_posts_by_ajax_callback');
add_action('wp_ajax_nopriv_load_posts_by_ajax', 'load_posts_by_ajax_callback');


function load_conteudo_by_ajax_callback() {
    check_ajax_referer('load_conteudo', 'security');

    // Captura os parâmetros da requisição
    $searchTerm = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';
    $categoria = isset($_POST['categoria']) ? sanitize_text_field($_POST['categoria']) : '';
    $dateIni = isset($_POST['date_ini']) ? sanitize_text_field($_POST['date_ini']) : '';
    $dateEnd = isset($_POST['date_end']) ? sanitize_text_field($_POST['date_end']) : '';

    // Lógica para buscar os resultados (igual ao código que você já tem)
    $allResults = array();
    $i = 0;

    $types = array('destaque', 'cursos', 'portais', 'post', 'noticia');
    if ($categoria && $categoria != '') {
        $types = array($categoria);
    }

    if(isset($searchTerm)):
		$query = $searchTerm;
		
		foreach($types as $type){

			if($type == 'cursos'){

				$qtd = 99;
				$url = 'https://hom-acervodigital.sme.prefeitura.sp.gov.br/wp-json/wp/v2/acervo/?per_page=' . $qtd . '&filter[categoria_acervo]=acesso-a-informacao';
				
				if($searchTerm && $searchTerm != ''){
					$busca = str_replace(' ', '+', $searchTerm);
					$url .= '&search=' . $busca; 
				} 

				if( $dateIni &&  $dateIni != '')
					$url .= '&after=' .  $dateIni . 'T00:00:01';

				if($dateEnd && $dateEnd != '')
					$url .= '&before=' . $dateEnd . 'T23:59:59';  
					
				//echo $url;
			
				$headers = [];
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADERFUNCTION,
					function ($curl, $header) use (&$headers) {
						$len = strlen($header);
						$header = explode(':', $header, 2);
						if (count($header) < 2) // ignore invalid headers
							return $len;

						$headers[strtolower(trim($header[0]))][] = trim($header[1]);

						return $len;
					}
				);
				$response = curl_exec($ch);                
				
				$jsonArrayResponse = json_decode($response);
				

				if($jsonArrayResponse && $jsonArrayResponse[0] != ''){
					foreach($jsonArrayResponse as $curso){
						$allResults[$i]['id'] = $curso->id;
						$allResults[$i]['titulo'] = $curso->title->rendered;
						$allResults[$i]['url'] = $curso->link;
						$allResults[$i]['num_hom'] = $curso->numero_de_despacho_de_homologacao;
						$allResults[$i]['pg_do'] = $curso->pagina_do_diario_oficial;
						$allResults[$i]['type'] = 'cursos';
						$old_date_timestamp = strtotime($curso->date);        
						$data = getDay(date('w', $old_date_timestamp)) . ', ' . converter_mes(date('m', $old_date_timestamp)) . ' ' . date('d', $old_date_timestamp) . ' às ' . date('H\hi\m\i\n', $old_date_timestamp);
						$allResults[$i]['data_curso'] = $data;
						$allResults[$i]['area_promotora'] = $curso->area_promotora;
						$arquivo = '';
						if($curso->arquivo_acervo_digital && $curso->arquivo_acervo_digital != '')
							$arquivo = get_file_url($curso->arquivo_acervo_digital);
						
						if($curso->arquivos_particionados_0_arquivo && $curso->arquivos_particionados_0_arquivo != '')
							$arquivo = get_file_url($curso->arquivos_particionados_0_arquivo);
						
						$allResults[$i]['arquivo'] = $arquivo;

						$i++;
					}
				}

			} else {
				$after = '';
				$before = '';										
				
				if( $dateIni &&  $dateIni != '' && $type != 'portais')
					$after =  $dateIni;

				if($dateEnd && $dateEnd != '' && $type != 'portais')
					$before = $dateEnd;

				$args = array( 
					's' => $query,
					'posts_per_page' => -1,
					'post_type' => $type,
					'post_status' => 'publish',										
				);
					
				$args['orderby'] = 'relevance';
				$args['date_query'] = array(
					array(
						'after'     => $after,
						'before'    => $before,
						'inclusive' => true,
					),
				);

				// Incluir subtitulo da busca de noticias
				
				if($type == 'post'){
					$args['s_meta_keys'] = array('insira_o_subtitulo');
				}
	
				$the_query = new WP_Query( $args );
				

				// The Loop
				if ( $the_query->have_posts() ) {
					
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						
						$allResults[$i]['id'] = get_the_id();
						$allResults[$i]['titulo'] = get_the_title();												
						$allResults[$i]['resumo'] = get_field('insira_o_subtitulo');

						if($type == 'destaque'){
							$categorias = get_the_terms(get_the_ID(), 'categorias-destaque');
							$tags = get_the_terms(get_the_ID(), 'tags-destaque');
							$image = get_template_directory_uri() . '/img/categ-destaques.jpg';
							if($categorias)
								$image = get_field('imagem_principal', 'categorias-destaque_' . $categorias[0]->term_id);
								
						}

						$allResults[$i]['conteudo'] = get_the_content();
						$allResults[$i]['categorias'] = $categorias;
						$allResults[$i]['tags'] = $tags;												
						$allResults[$i]['url'] = get_field('insira_link');
						if($type == 'destaque')
							$allResults[$i]['url'] = get_field('insira_o_link');
						$allResults[$i]['url_video'] = get_field('url_do_video');
						$allResults[$i]['image_anexo'] = get_field('selecione_imagem');												
						$allResults[$i]['type'] = get_post_type();
						$allResults[$i]['image'] = $image;
						$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
						$allResults[$i]['alt']  = get_post_meta ( $thumbnail_id, '_wp_attachment_image_alt', true );
						$allResults[$i]['data_semana']  = getDay(get_the_date('w'));
						$allResults[$i]['data_dia_mes'] = get_the_date('M d');
						$allResults[$i]['data_hora']  = get_the_date('H\hi\m\i\n');												

						$i++;
					}
					
				} 

				/* Restore original Post Data */
				wp_reset_postdata();

			}

		}

	endif;

    // Paginação
    $pagina = !empty($_POST['pagina']) ? (int) $_POST['pagina'] : 1;
    $total = count($allResults); // Total de itens no array
    $limit = 10; // Itens por página
    $totalPages = ceil($total / $limit); // Total de páginas
    $pagina = max($pagina, 1); // Garante que a página seja no mínimo 1
    $pagina = min($pagina, $totalPages); // Garante que a página não ultrapasse o total
    $offset = ($pagina - 1) * $limit;
	$current_date = date('Ymd');

    $allResults = array_slice($allResults, $offset, $limit);

    // Exibe os resultados
    if ($allResults) {
        foreach($allResults as $result):
			?>
				<?php if($result['type'] == 'destaque'): ?>

					<div class="recado-categ">
						<p>Categoria: Recados</p>
					</div>
					<div class="recado">
						<div class="row">
							<div class="col-3 col-md-2 img-column">
								
								<?php
									$categorias = get_the_terms($result['id'], 'categorias-destaque');
									
									if($categorias)
										$image = get_field('imagem_principal', 'categorias-destaque_' . $categorias[0]->term_id);
										$i = 0;
								?>
								<?php if($image && isset($image['url'])): ?>
									<img src="<?= $image['url']; ?>" class="img-fluid rounded d-none d-sm-none d-md-block" alt="Imagem de ilustração categoria">
									<img src="<?= $image['sizes']['thumbnail']; ?>" class="img-fluid rounded d-md-none" alt="Imagem de ilustração categoria">
								<?php else: ?>
									<img src="<?= get_template_directory_uri(); ?>/img/categ-destaques.jpg" class="img-fluid rounded" alt="Imagem de ilustração categoria">
								<?php endif; ?>

							</div>

							<div class="col-9 col-md-10">
								
								<p class="data"><?= $result['data_semana']; ?>, <?= $result['data_dia_mes']; ?> às <?= $result['data_hora']; ?></p>
								
								<?php if($result['tags']): ?>
									<div class="tags-recados">
										<?php 
											foreach($result['tags'] as $tag){
												$cor = get_field('cor_principal', 'tags-destaque_' . $tag->term_id);
												echo '<a href="' . get_home_url() . '/index.php/mural-de-recados/?tag=' . $tag->term_id . '" target="_blank" style="background: ' . $cor . '">' . firstLetter($tag->name) . '</a> ';
											}
										?>
									</div>
								<?php endif; ?>

								
								<h2><?= $result['titulo']; ?></h2>
								<?php
									$subtitulo = $result['resumo'];
									if($subtitulo && $subtitulo != '')
										echo '<p>' . $subtitulo . '</p>';
								?>

								<?php if($result['categorias']): ?>
									<p class="categs">
										<?php
											$j = 0;
											foreach($result['categorias'] as $term){
												if($j == 0){
													echo '<a href="' . get_home_url() . '/index.php/mural-de-recados/?categoria=' . $term->term_id . '">' . $term->name . '</a>';
												} else {
													echo ', <a href="' . get_home_url() . '/index.php/mural-de-recados/?categoria=' . $term->term_id . '">' . $term->name . '</a>';
												}
												$j++;
											}                                        
										?>
									</p>
								<?php endif; ?>
								
								<hr>
								<a class="btn-collapse collapsed" data-toggle="collapse" href="#collapse<?= $result['id']; ?>" role="button" aria-expanded="false" aria-controls="collapse<?= $result['id']; ?>">
									<span class="button-more">ver mais <i class="fa fa-chevron-down" aria-hidden="true"></i></span><span class="button-less">ver menos <i class="fa fa-chevron-up" aria-hidden="true"></i></span>
								</a> 
								
							</div>
						</div>

						<div class="collapse" id="collapse<?=$result['id']; ?>">
							<div class="recado-content">
								<?php 
									$content = apply_filters('the_content', $result['conteudo']);
								?>
								<?= $content; ?>
								<?php if( $result['url'] ): ?>
									<p class="link-externo"><a href="<?= $result['url']; ?>">Ver link externo</a></p>
								<?php endif; ?>
							</div>
							<?php if($result['url_video']): ?>
								<div class="recado-video">
									<div class="embed-container">
										<?php the_field('url_do_video', $result['id']); ?>
									</div>                                    
								</div>
							<?php endif; ?>

							<?php if($result['image_anexo']): ?>
								<div class="recado-video">                                    
									<?php $imagem = $result['image_anexo']; ?>
									<img src="<?= $imagem['url']; ?>" alt="<?= $imagem['alt']; ?>">
								</div>
							<?php endif; ?>
						</div>

					</div>
				
				<?php elseif($result['type'] == 'portais'): ?>

					<div class="portais-categ">
						<p>Categoria: Portais e Sistemas</p>
					</div>

					<div class="lista-portais">
						<div class="portal">
							<div class="row">
								<div class="col-sm-2 d-flex justify-content-center">
									<?php
										$imagem = get_field('imagem_destacada', $result['id']);
										$imagemPadrao = get_template_directory_uri() . '/img/categ-portais.jpg';
										if($imagem['sizes']['admin-list-thumb'])
											$imagemPadrao = $imagem['sizes']['admin-list-thumb'];
									?>

									<a href="<?= get_field('insira_link', $result['id']); ?>" target="_blank"><img src="<?= $imagemPadrao; ?>" alt="Imagem de ilustração categoria" srcset=""></a>
										
								</div>

								<div class="col-sm-10">                                        
									<h3><a href="<?= get_field('insira_link', $result['id']); ?>" target="_blank"><?= $result['titulo']; ?></a></h3>
									<hr>
									<?php 
										$content = apply_filters('the_content', $result['conteudo']);
										echo $content;
									?>
								</div>                          
						</div>
					</div>

				<?php elseif($result['type'] == 'post'): ?>

					<div class="noticias-categ">
						<p>Categoria: Sorteios</p>
					</div>

					<div class="recado noticia-busca">
						<div class="row">
							<div class="col-3 col-md-2 img-column">
								
								<?php 
									$image = get_the_post_thumbnail( $result['id'], 'default-image', array( 'class' => 'img-fluid rounded' ) );
								?>
								<?php if($image): ?>
									<?= $image; ?>
								<?php else: ?>
									<img src="<?= get_template_directory_uri(); ?>/img/categ-destaques.jpg" class="img-fluid rounded" alt="Imagem de ilustração categoria">
								<?php endif; ?>

							</div>

							<div class="col-9 col-md-10">
								
								<p class="data"><?= $result['data_semana']; ?>, <?= $result['data_dia_mes']; ?> às <?= $result['data_hora']; ?></p>
																				
								<?php
									// Verificando se a data de encerramento é menor que a data atual
									$enc_inscri = get_field('enc_inscri', $result['id']);																
									$status_prefix = ($enc_inscri < $current_date) ? 'ENCERRADO - ' : '';
								?>
								
								<h2><a href="<?= get_the_permalink($result['id']); ?>"><?= $status_prefix . $result['titulo']; ?></a></h2>
								<?php
									$subtitulo = $result['resumo'];
									if($subtitulo && $subtitulo != '')
										echo '<p>' . $subtitulo . '</p>';
								?>

								<hr>
								<a class="btn-collapse collapsed" href="<?= get_the_permalink($result['id']); ?>">
									<span class="button-more">saiba mais <i class="fa fa-chevron-right" aria-hidden="true"></i></span>
								</a> 
								
							</div>
						</div>

					</div>

				<?php elseif($result['type'] == 'noticia'): ?>

					<div class="noticias-categ">
						<p>Categoria: Notícias</p>
					</div>

					<div class="recado noticia-busca">
						<div class="row">
							<div class="col-3 col-md-2 img-column">
								
								<?php 
									$image = get_the_post_thumbnail( $result['id'], 'default-image', array( 'class' => 'img-fluid rounded' ) );
								?>
								<?php if($image): ?>
									<?= $image; ?>
								<?php else: ?>
									<img src="<?= get_template_directory_uri(); ?>/img/categ-destaques.jpg" class="img-fluid rounded" alt="Imagem de ilustração categoria">
								<?php endif; ?>

							</div>

							<div class="col-9 col-md-10">
								
								<p class="data"><?= $result['data_semana']; ?>, <?= $result['data_dia_mes']; ?> às <?= $result['data_hora']; ?></p>
																				
								<h2><a href="<?= get_the_permalink($result['id']); ?>"><?= $result['titulo']; ?></a></h2>
								<?php
									$subtitulo = $result['resumo'];
									if($subtitulo && $subtitulo != '')
										echo '<p>' . $subtitulo . '</p>';
								?>

								<hr>
								<a class="btn-collapse collapsed" href="<?= get_the_permalink($result['id']); ?>">
									<span class="button-more">saiba mais <i class="fa fa-chevron-right" aria-hidden="true"></i></span>
								</a> 
								
							</div>
						</div>

					</div>

				<?php else: ?>											
					<div class="cursos-categ">
						<p>Categoria: Cursos</p>
					</div>
					<div class="curso">
						<p class="date">
						
							<?php if($result['num_hom'] && $result['num_hom'] != ''): ?>
								Homologação <?= $result['num_hom']; ?> -                                 
							<?php endif; ?>
							<?= $result['data_curso']; ?>

							<?php if($result['pg_do'] && $result['pg_do'] != ''): ?>
								- página <?= $result['pg_do']; ?>                              
							<?php endif; ?>
						</p>
						<h2><a target="_blank" href="<?= $result['url']; ?>"><?= $result['titulo']; ?></a></h2>
						<?php if($result['area_promotora'] && $result['area_promotora'][0] != ''): ?>
							<p class="promotora"><strong>Área promotora: </strong>
								<?php
									$i = 0;
									foreach($result['area_promotora'] as $area){
										if($i == 0){
											echo get_tax_name('promotora', $area);
										} else {
											echo '/ ' . get_tax_name('promotora', $area);
										}
										$i++;
									}
								?>
							</p>
						<?php endif; ?>                        
						<hr>                        
					
						<?php
							$arquivo = $result['arquivo'];
							
							if($arquivo && $arquivo != ''){ 
																
							?>                           

							<i class="fa fa-search" aria-hidden="true"></i> <a href="#modal-<?=$result['id']; ?>" class="link" data-toggle="modal" data-target="#modal-<?=$result['id']; ?>">Visualizar</a> / 

								<?php if(substr($arquivo, -3) == 'jpg' || substr($arquivo, -3) == 'jpeg' || substr($arquivo, -3) == 'png' || substr($arquivo, -3) == 'gif' || substr($arquivo, -3) == 'webp') : ?>
						
									<!-- Modal -->
									<div class="modal fade" id="modal-<?=$result['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<p class="modal-title"><?= $result['titulo']; ?></p>
													<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
													<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<?php if($arquivo) : ?>
														<img src="<?php echo $arquivo; ?>" class="img-fluid d-block mx-auto py-2">
													<?php else: ?>
														<p>Visualização não disponível.</p>
													<?php endif; ?>
												</div>															
											</div>
										</div>
									</div>

								<?php elseif(substr($arquivo, -3) == 'pdf'): ?>

									<div class="modal fade" id="modal-<?=$result['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-xl">
											<div class="modal-content">

												<div class="modal-header">
													<p class="modal-title"><?= $result['titulo']; ?></p>
													<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
													<span aria-hidden="true">&times;</span>
													</button>
												</div>

												<div class="modal-body">
													<div class="embed-responsive embed-responsive-16by9">                                                        
														<iframe style="largura: 718px; altura: 700px;" src="<?= $arquivo; ?>" frameborder="0"></iframe>
													</div>
												</div>

											</div>
										</div>
									</div>

								<?php else : ?>
									
									<div class="modal fade" id="modal-<?=$result['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-xl">
											<div class="modal-content">

												<div class="modal-header">
													<p class="modal-title"><?= $result['titulo']; ?></p>
													<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
													<span aria-hidden="true">&times;</span>
													</button>
												</div>

												<div class="modal-body">
													<div class="embed-responsive embed-responsive-16by9">
														<iframe title="doc" type="application/pdf" src="https://docs.google.com/gview?url=<?php echo $arquivo; ?>&amp;embedded=true" class="jsx-690872788 eafe-embed-file-iframe"></iframe>
													</div>
												</div>

											</div>
										</div>
									</div>

								<?php endif;                              
							}
								
						?> 
					
						<a href="<?= $result['url']; ?>" class="link" target="_blank" rel="noopener noreferrer">Ver detalhes no Acervo Digital</a>

					</div>

				<?php endif; ?>
			<?php
		endforeach;
    } else {
        ?>
			<div class="no-results">
				<h2 class="search-title">
					<span class="azul-claro-acervo"><strong>0</strong></span><strong> 
						resultados</strong>
				</h2>				
				<p>Não há conteúdo disponível para o termo buscado. <br>Por favor faça uma nova busca ou clique <button id="tab-calendario">aqui</button> e busque pelo calendário.</p>
				<img src="<?php echo get_template_directory_uri(); ?>/img/search-empty.png" alt="Imagem ilustrativa para nenhum resultado de busca encontrado" />
			</div>
			

		<?php
    }

    // Exibe a paginação
    if ($allResults && $totalPages > 1) {
        echo '<div class="pagination-prog">';
        echo '<div class="wp-pagenavi">';
        echo '<div style="text-align: center;display: flex;align-items: center;justify-content: center; margin-top: 10px;">';
        echo '<a class="aaa paginationA" href="#" data-pagina="1"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>';
        // Links de paginação
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<a class="paginationB ' . ($i == $pagina ? 'active' : '') . '" href="#" data-pagina="' . $i . '">' . $i . '</a>';
        }
        echo '<a class="d paginationA" href="#" data-pagina="' . $totalPages . '"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>';
        echo '</div></div></div>';
    }

    wp_die(); // Finaliza a execução do AJAX
}
add_action('wp_ajax_load_conteudo_by_ajax', 'load_conteudo_by_ajax_callback');
add_action('wp_ajax_nopriv_load_conteudo_by_ajax', 'load_conteudo_by_ajax_callback');

function notify_admins_and_editors_on_comment($comment_id) {
    // Obtém o objeto do comentário
    $comment = get_comment($comment_id);

    // Verifica se o comentário é spam
    if ($comment->comment_approved === 'spam') {
        return; // Se for spam, não envia o e-mail
    }

    // Obtém o post relacionado ao comentário
    $post = get_post($comment->comment_post_ID);

    // Busca todos os usuários com as funções de administrador e editor
    $admins_and_editors = get_users(array(
        'role__in' => array('administrator', 'editor'), // Seleciona apenas administradores e editores
        'fields' => array('user_email') // Retorna apenas os e-mails
    ));

    // Verifica se há usuários com essas funções
    if (!empty($admins_and_editors)) {
        // Assunto do e-mail
        $subject = 'Novo comentário em "' . $post->post_title . '"';

        // Corpo do e-mail
        $message = 'Novo comentário em "' . $post->post_title . '"' . "\n\n";
        $message .= 'Autor: ' . $comment->comment_author . "\n";
        $message .= 'E-mail: ' . $comment->comment_author_email . "\n";
        $message .= 'Comentário:' . "\n";
        $message .= $comment->comment_content . "\n\n";
        $message .= 'Você pode ver todos os comentários para este post em:' . "\n";
        $message .= get_permalink($post->ID) . '#comments' . "\n\n";
        $message .= 'Link do comentário: ' . get_comment_link($comment_id) . "\n";
        $message .= 'Mover para a lixeira: ' . admin_url('comment.php?action=trash&c=' . $comment_id) . "\n";
        $message .= 'Marcar como spam: ' . admin_url('comment.php?action=spam&c=' . $comment_id) . "\n";

        // Envia o e-mail para cada administrador e editor
        foreach ($admins_and_editors as $user) {
            wp_mail($user->user_email, $subject, $message);
        }
    }
}
add_action('comment_post', 'notify_admins_and_editors_on_comment');

function enqueue_comment_reply_script() {
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_comment_reply_script');

function custom_comment_reply_link($args = array(), $comment = null, $post = null) {
    // Parâmetros padrão
    $defaults = array(
        'add_below'  => 'comment',
        'respond_id' => 'respond',
        'reply_text' => __('Reply'),
        'login_text' => __('Log in to Reply'),
        'depth'      => 0,
        'before'     => '',
        'after'      => '',
        'reply_to'   => 'Responder a %s', // Texto do link
    );

    // Mescla os parâmetros padrão com os passados
    $args = wp_parse_args($args, $defaults);

    // Gera o link de resposta
    if (get_option('comment_registration') && !is_user_logged_in()) {
        $link = sprintf(
            '<a rel="nofollow" class="comment-reply-login" href="%s">%s</a>',
            esc_url(wp_login_url(get_permalink())),
            $args['login_text']
        );
    } else {
        $onclick = sprintf(
            'return addComment.moveForm("%1$s-%2$s", "%2$s", "%3$s", "%4$s")',
            $args['add_below'],
            $comment->comment_ID,
            $args['respond_id'],
            $post->ID
        );

        $link = sprintf(
            "<a rel='nofollow' class='comment-reply-link' href='%s' onclick='%s' aria-label='%s'>%s</a>",
            esc_url(add_query_arg('replytocom', $comment->comment_ID, get_permalink($post->ID))) . "#" . $args['respond_id'],
            $onclick,
            esc_attr(sprintf($args['reply_to'], $comment->comment_author)),
            $args['reply_text']
        );
    }

    return $args['before'] . $link . $args['after'];
}

function custom_comment_callback($comment, $args, $depth) {
    // Verifica o tipo de comentário (comentário, pingback, trackback)
    $tag = ('div' === $args['style']) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            
                <div class="comment-author vcard">
                    <?php
                    // Exibe o avatar do comentário
                    if (0 != $args['avatar_size']) {
                        echo get_avatar($comment, $args['avatar_size'], '', '', array('class' => 'avatar avatar-32 img-fluid'));
                    }
                    // Exibe o nome do autor do comentário
                    printf(__(' <cite class="fn">%s</cite>'), get_comment_author_link());
                    ?>
                    <span class="says">disse:</span>
                </div><!-- .comment-author -->

                <div class="comment-metadata">
                    <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                        <time datetime="<?php comment_time('c'); ?>">
                            <?php
                            printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time());
                            ?>
                        </time>
                    </a>
                    <?php edit_comment_link(__('Edit'), '<span class="edit-link">', '</span>'); ?>
                </div><!-- .comment-metadata -->

                <?php if ('0' == $comment->comment_approved) : ?>
                    <p class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.'); ?></p>
                <?php endif; ?>
           

            <div class="comment-content">
                <?php comment_text(); ?>
            </div><!-- .comment-content -->

            <div class="reply">
                <?php
                // Usa a função personalizada para gerar o link de resposta
                echo custom_comment_reply_link(array(
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                ), $comment, get_the_ID());
                ?>
            </div><!-- .reply -->
        </article><!-- .comment-body -->
    <?php
}


add_action('init', 'save_redirect_to_in_session');
function save_redirect_to_in_session() {

    // Verifica se o parâmetro redirect_to está presente na URL e salva em um cookie
    if ( isset( $_GET['redirect_to'] ) ) {
        setcookie(
			'redirect_to',
			esc_url_raw( $_GET['redirect_to'] ),
			time() + 300,
			COOKIEPATH,
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);
    }
}

function verificar_cpf($request = null) {
    // Captura CPF e post_id de $_POST ou do $request (REST)
    $cpf     = isset($_POST['cpf'])     ? sanitize_text_field($_POST['cpf'])     : sanitize_text_field($request['cpf'] ?? '');
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id'])              : intval($request['post_id'] ?? 0);

    if (!$cpf || !$post_id) {
        wp_send_json_error('Dados incompletos.');
    }

    global $wpdb;
    $tabela_inscricoes = $wpdb->prefix . 'inscricoes';
    $tabela_sancoes    = $wpdb->prefix . 'inscricao_sancoes';

    $cpf_cadastrado = $wpdb->get_row($wpdb->prepare(
        "SELECT user_id, email_institucional, email_secundario FROM $tabela_inscricoes WHERE cpf = %s AND post_id = %d",
        $cpf,
        $post_id
    ));

	$usuario_id = $cpf_cadastrado->user_id ?? null;
	$tipo_usuario = 'estagiario';

	if ( $usuario_id ) {
		if ( boolval( get_user_meta( $usuario_id, 'parceira', true ) ) ) {
			$tipo_usuario = 'parceira';
		} else {
			$tipo_usuario = 'servidor';
		}
	}

	if ( !empty( $cpf_cadastrado ) ) {
		wp_send_json_success([
			'cadastrado' => true,
			'tipo_usuario' => $tipo_usuario,
			'emails_cadastrados' => [
				'institucional' => mascarar_email( $cpf_cadastrado->email_institucional ),
				'secundario' => mascarar_email( $cpf_cadastrado->email_secundario )
			]
		]);
	}

    date_default_timezone_set('America/Sao_Paulo');

    $sancao_usuario = $wpdb->get_var($wpdb->prepare(
        "SELECT data_validade FROM $tabela_sancoes WHERE cpf = %s",
        $cpf
    ));

    $ativo = false;
    $dataPermissao = '';

    if ($sancao_usuario) {
        $hoje     = new DateTime('today', new DateTimeZone('America/Sao_Paulo')); 
        $validade = new DateTime($sancao_usuario, new DateTimeZone('America/Sao_Paulo'));

        if ($validade >= $hoje) {
            $ativo = true;
            $validadeMaisUm = clone $validade;
            $validadeMaisUm->modify('+1 day');
            $dataPermissao = $validadeMaisUm->format('d/m/Y');
        }
    }

    if ( $ativo ) {
        wp_send_json_success(['sancao' => true, 'data_permissao' => $dataPermissao]);
    }

	wp_send_json_success(['cadastrado' => false]);
}

// Hooks para requisições AJAX
add_action('wp_ajax_verificar_cpf', 'verificar_cpf'); // Para usuários logados
add_action('wp_ajax_nopriv_verificar_cpf', 'verificar_cpf'); // Para usuários não logados

function cancelar_inscricao() {
    if (!isset($_POST['user_id'], $_POST['post_id'], $_POST['modelo'])) {
        wp_send_json_error('Dados incompletos.');
    }

    $user_id = intval($_POST['user_id']);
    $post_id = intval($_POST['post_id']);
    $modelo = sanitize_text_field($_POST['modelo']);
    $datas = isset($_POST['datas']) ? (array) $_POST['datas'] : [];
	$premios = isset($_POST['premios']) ? (array) ($_POST['premios']) : [];

    global $wpdb;
    $tabela_inscricoes = $wpdb->prefix . 'inscricoes';
    $tabela_datas = $wpdb->prefix . 'inscricao_datas';

    // Busca a inscrição
    $inscricao = $wpdb->get_row($wpdb->prepare("
        SELECT * FROM {$tabela_inscricoes}
        WHERE user_id = %d AND post_id = %d
    ", $user_id, $post_id));

    if (!$inscricao) {
        wp_send_json_error('Inscrição não encontrada.');
    }

    // MODELO ÚNICO → Exclui diretamente
    if ($modelo === 'unico' || $modelo === 'periodo') {
		$mensagem = 'Você não está mais participando deste sorteio.';
        is_plugin_active('envia-email-sme/envia-email-sme.php') ? new Envia_Emails_Sorteio_SME(null, $user_id, $post_id, 'cancelamento', $mensagem) : '';

        $resultado = $wpdb->delete($tabela_inscricoes, [
            'user_id' => $user_id,
            'post_id' => $post_id,
        ], ['%d', '%d']);

        if ($resultado) {
            wp_send_json_success(['mensagem' => 'Você não está mais participando deste sorteio.']);
        } else {
            wp_send_json_error('Erro ao cancelar inscrição.');
        }

    // MODELO MULTI → Exclui datas específicas
    } elseif ($modelo === 'multi' || $modelo === 'premio') {
        if (empty($datas)) {
            wp_send_json_error('Nenhuma data selecionada.');
        }

        // Remove as datas selecionadas
        foreach ($datas as $data) {
            $wpdb->delete($tabela_datas, [
                'inscricao_id' => $inscricao->id,
                'data_evento' => sanitize_text_field($data)
            ], ['%d', '%s']);
        }

        // Verifica se ainda restam datas
        $restantes = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM {$tabela_datas}
            WHERE inscricao_id = %d
        ", $inscricao->id));

        // Se não restar nenhuma, deleta a inscrição
        if ($restantes == 0) {

			if(is_array($datas)){
				if (count($datas) > 1) {
					
					
				$datasFormatadas = array_map(function($data) {
					$dt = new \DateTime($data);
					$hora = $dt->format('H');
					$minuto = (int) $dt->format('i');

					$horaFormatada = ($minuto === 0) ? "{$hora}h" : sprintf("%sh%02d", $hora, $minuto);

					return $dt->format('d/m/Y') . ' ' . $horaFormatada;
				}, $datas);

				if ($modelo === 'premio') {					
					$premiosSelecionados = [];
					foreach ($datas as $data) {
						if (isset($premios[$data])) {
							$premiosSelecionados[] = $premios[$data];
						}
					}

					$mensagem = 'Você não está mais participando deste sorteio para os prêmios: <br><strong>' . implode('<br>', $premiosSelecionados) . '</strong>';
				} else {
					$mensagem = 'Você não está mais participando deste sorteio nas datas: <br>' . implode('<br>', $datasFormatadas);
				}
					
				} else {
					$dt = new \DateTime($datas[0]);
					$hora = $dt->format('H');
					$minuto = (int) $dt->format('i');

					if ($minuto === 0) {
						$horaFormatada = "{$hora}h";
					} else {
						$horaFormatada = sprintf("%sh%02d", $hora, $minuto);
					}

					if ( $modelo == 'premio' ) {
						$mensagem = 'Você não está mais participando deste sorteio para o prêmio: <strong>' . $premios[$datas[0]] . '</strong>';
					} else {
						$mensagem = 'Você não está mais participando deste sorteio na data: <br>'. $dt->format('d/m/Y') . ' ' . $horaFormatada;
					}

				}
					
			} else {
				$mensagem = 'Você não está mais participando deste sorteio.';
			}

            is_plugin_active('envia-email-sme/envia-email-sme.php') ? new Envia_Emails_Sorteio_SME(null, $user_id, $post_id, 'cancelamento', $mensagem) : '';

            $wpdb->delete($tabela_inscricoes, [
                'id' => $inscricao->id
            ], ['%d']);

            wp_send_json_success(['mensagem' => $mensagem]);
        } else {
			if(is_array($datas)){
				if (count($datas) > 1) {
					
					$datasFormatadas = array_map(function($data) {
					$dt = new \DateTime($data);
					$hora = $dt->format('H');
					$minuto = (int) $dt->format('i');

					$horaFormatada = ($minuto === 0) ? "{$hora}h" : sprintf("%sh%02d", $hora, $minuto);

					return $dt->format('d/m/Y') . ' ' . $horaFormatada;
				}, $datas);

				if ($modelo === 'premio') {					
					$premiosSelecionados = [];
					foreach ($datas as $data) {
						if (isset($premios[$data])) {
							$premiosSelecionados[] = $premios[$data];
						}
					}

					$mensagem = 'Você não está mais participando deste sorteio para os prêmios: <br><strong>' . implode('<br>', $premiosSelecionados) . '</strong>';
				} else {
					$mensagem = 'Você não está mais participando deste sorteio nas datas: <br>' . implode('<br>', $datasFormatadas);
				}
					
				} else {
					$dt = new \DateTime($datas[0]);
					$hora = $dt->format('H');
					$minuto = (int) $dt->format('i');

					if ($minuto === 0) {
						$horaFormatada = "{$hora}h";
					} else {
						$horaFormatada = sprintf("%sh%02d", $hora, $minuto);
					}

					if ( $modelo == 'premio' ) {
						$mensagem = 'Você não está mais participando deste sorteio para o prêmio: <strong>' . $premios[$datas[0]] . '</strong>';
					} else {
						$mensagem = 'Você não está mais participando deste sorteio na data: <br>'. $dt->format('d/m/Y') . ' ' . $horaFormatada;
					}
				}
					
			} else {
				$mensagem = 'Você não está mais participando deste sorteio.';
			}
			
            is_plugin_active('envia-email-sme/envia-email-sme.php') ? new Envia_Emails_Sorteio_SME(null, $user_id, $post_id, 'cancelamento', $mensagem) : '';

            wp_send_json_success(['mensagem' => $mensagem]);
        }

    } else {
        wp_send_json_error('Modelo inválido.');
    }
}

// Hooks para requisições AJAX
add_action('wp_ajax_cancelar_inscricao', 'cancelar_inscricao'); // Para usuários logados
add_action('wp_ajax_nopriv_cancelar_inscricao', 'cancelar_inscricao'); // Para usuários não logados

add_action( "compromisso_edit_form", 'hide_description_row');
add_action( "compromisso_add_form", 'hide_description_row');

// Inserir as opções no campo Compromisso no cadastro da Agenda de Eventos SME
add_filter( 'acf/load_field/name=compromisso_sme', function( $field ) {
  
	// Get all taxonomy terms
	$compromissos = get_terms( array(
	  'taxonomy' => 'compromisso_sec',
	  'hide_empty' => false
	) );
	
	// Add each term to the choices array.
	// Example: $field['choices']['review'] = Review
	$field['choices']['outros'] = 'Outros';
	foreach ( $compromissos as $type ) {
	  $field['choices'][$type->term_id] = $type->name;
	}
  
	return $field;
} );

// Inserir as opções no campo Endereço no cadastro da Agenda de Eventos SME
add_filter( 'acf/load_field/name=endereco_evento_sme', function( $field ) {
  
	// Get all taxonomy terms
	$compromissos = get_terms( array(
	  'taxonomy' => 'endereco_sec',
	  'hide_empty' => false
	) );
	
	// Add each term to the choices array.
	// Example: $field['choices']['review'] = Review
	$field['choices']['outros'] = 'Outros';
	foreach ( $compromissos as $type ) {
	  $field['choices'][$type->term_id] = $type->name;
	}
  
	return $field;
} );

// Incluir CSS no admin
function meu_admin_css() {
    // Verifica se o usuário está na área de administração
    if (is_admin()) {
        // Registra o arquivo CSS
        wp_register_style('meu-admin-style', get_template_directory_uri() . '/classes/assets/css/admin-style.css', array(), '1.0');
        // Adiciona o arquivo CSS ao admin
        wp_enqueue_style('meu-admin-style');
    }
}
// Adiciona a ação ao hook 'admin_enqueue_scripts'
add_action('admin_enqueue_scripts', 'meu_admin_css');

// Filtrar agenda por autor para Colaboradores
function filtrar_agenda_por_autor($query) {
    // Verifica se estamos no admin e se o post type é 'agenda'
    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'agenda') {
        // Obtém o usuário atual
        $usuario_atual = wp_get_current_user();
        
        // Verifica se o usuário é do tipo 'contributor' (Colaborador)
        if (in_array('contributor', $usuario_atual->roles)) {
            // Filtra os posts para exibir apenas os criados pelo usuário atual
            $query->set('author', $usuario_atual->ID);
        }
    }
}
add_action('pre_get_posts', 'filtrar_agenda_por_autor');


// Ocultar elementos do dashboard (Painel) para Colaboradores
function ocultar_elementos_dashboard_colaborador() {
    // Verifica se o usuário atual é do tipo Colaborador
    if (current_user_can('contributor')) {
        // Remove todas as meta boxes do Dashboard
        remove_action('welcome_panel', 'wp_welcome_panel'); // Remove o painel de boas-vindas
        remove_meta_box('dashboard_primary', 'dashboard', 'side'); // Remove as notícias do WordPress
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); // Remove o painel "Publicação Rápida"
        remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); // Remove o painel "Agora"
        remove_meta_box('dashboard_activity', 'dashboard', 'normal'); // Remove o painel "Atividade"
        remove_meta_box('dashboard_site_health', 'dashboard', 'normal'); // Remove o painel "Saúde do Site"
        remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side'); // Remove o painel "Rascunhos Recentes"
        remove_meta_box('dashboard_secondary', 'dashboard', 'side'); // Remove as notícias secundárias do WordPress
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Remove o painel "Comentários Recentes"
        remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal'); // Remove o painel "Links Recebidos"
        remove_meta_box('dashboard_plugins', 'dashboard', 'normal'); // Remove o painel "Plugins"
        remove_meta_box('dashboard_php_nag', 'dashboard', 'normal'); // Remove o aviso de versão do PHP
        remove_meta_box('cpdDashboardWidget', 'dashboard', 'normal'); // Remove o aviso de versão do PHP
    }
}
add_action('admin_init', 'ocultar_elementos_dashboard_colaborador');

// Criar elemento customizado no painel do dashboard (Painel)
function adicionar_widget_agenda_colaborador() {
    // Verifica se o usuário atual é do tipo Colaborador
    if (current_user_can('contributor')) {
        // Adiciona um widget personalizado ao Dashboard
        wp_add_dashboard_widget(
            'widget_agenda_colaborador', // ID do widget
            'Últimos Eventos Cadastrados', // Título do widget
            'exibir_agendas_recentes_colaborador' // Função de callback para exibir o conteúdo
        );
    }
}
add_action('wp_dashboard_setup', 'adicionar_widget_agenda_colaborador');

function exibir_agendas_recentes_colaborador() {
    // Obtém o ID do usuário logado
    $usuario_id = get_current_user_id();

    // Configura os argumentos da consulta
    $args = array(
        'post_type' => 'agenda', // CPT 'agenda'
        'author' => $usuario_id, // Apenas posts do usuário atual
        'posts_per_page' => 5, // Limita a 5 posts
        'orderby' => 'date', // Ordena por data
        'order' => 'DESC', // Ordem decrescente (mais recentes primeiro)
    );

    // Executa a consulta
    $query = new WP_Query($args);

    // Verifica se há posts
    if ($query->have_posts()) {
        echo '<ul>';
        while ($query->have_posts()) {
            $query->the_post();
			$data_do_evento = get_field('data_do_evento', get_the_ID());

            echo '<li>';
            echo '<a href="' . get_edit_post_link() . '">' . get_the_title() . '</a>'; // Link para editar o post
            echo ' <em>(' . $data_do_evento . ')</em>'; // Data da publicação
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>Nenhuma evento cadastrado.</p>';
    }

    // Restaura os dados originais da consulta
    wp_reset_postdata();
}

add_action('admin_enqueue_scripts', 'load_jquery_ui_for_agenda');
function load_jquery_ui_for_agenda() {
    if (get_post_type() === 'agenda') {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('wp-jquery-ui-dialog'); // CSS nativo do WordPress para diálogos
    }
}


// Enfileira scripts
add_action('admin_enqueue_scripts', 'enqueue_agenda_validation_scripts');
function enqueue_agenda_validation_scripts() {
    if (get_post_type() === 'agenda') {
        wp_enqueue_script(
            'agenda-date-validation',
            get_template_directory_uri() . '/js/agenda-date-validation.js',
            array('jquery', 'acf-input'),
            '1.0',
            true
        );
        // Passa dados para o JS
        wp_localize_script(
            'agenda-date-validation',
            'agenda_ajax',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'duplicate_message' => '<p class="green">Existe outro evento registrado para este dia/período! Não há problema em selecionar o mesmo dia.</p>'
            )
        );
    }
}

// Função AJAX para verificar duplicatas
add_action('wp_ajax_check_agenda_date', 'check_agenda_date');
function check_agenda_date() {
    $date_ini = sanitize_text_field($_POST['date']);
	$date_fin = sanitize_text_field($_POST['dateFinal']);
    $post_id = intval($_POST['post_id'] ?? 0);
    
    $args = array(
		'post_type'      => 'agenda',
		'posts_per_page' => -1,
		'post__not_in'   => array($post_id),
		'meta_query'     => array(
			'relation' => 'OR', // Para considerar eventos únicos e periódicos
		),
	);
	
	// Se **somente a data inicial** for fornecida
	if ($date_ini && !$date_fin) {
		$args['meta_query'][] = [
			'relation' => 'OR',
			// Evento de data única
			[
				'key'     => 'data_do_evento',
				'value'   => $date_ini,
				'compare' => '=',
				'type'    => 'DATE',
			],
			// Evento periódico (se o evento começar ou terminar nessa data, ou se a data estiver dentro do intervalo)
			[
				'relation' => 'AND',
				[
					'key'     => 'data_do_evento',
					'value'   => $date_ini,
					'compare' => '<=',
					'type'    => 'DATE',
				],
				[
					'key'     => 'data_evento_final',
					'value'   => $date_ini,
					'compare' => '>=',
					'type'    => 'DATE',
				],
			],
		];
	}
	
	// Se **as duas datas** forem fornecidas
	if ($date_ini && $date_fin) {
		$args['meta_query'][] = [
			'relation' => 'OR',
			// Evento de data única dentro do intervalo
			[
				'key'     => 'data_do_evento',
				'value'   => [$date_ini, $date_fin],
				'compare' => 'BETWEEN',
				'type'    => 'DATE',
			],
			// Evento periódico (se o evento começar antes da data final e terminar depois da data inicial)
			[
				'relation' => 'AND',
				[
					'key'     => 'data_do_evento',
					'value'   => $date_fin,
					'compare' => '<=',
					'type'    => 'DATE',
				],
				[
					'key'     => 'data_evento_final',
					'value'   => $date_ini,
					'compare' => '>=',
					'type'    => 'DATE',
				],
			],
		];
	}

    $query = new WP_Query($args);
    wp_send_json_success($query->have_posts());
}

// Incluir botao de emojis nos comentarios
function adicionar_seletor_emojis_comentarios() {
    // Carrega apenas em posts single
    if (!is_single()) return;

    // Carrega o Emoji Mart (CSS e JS) - CDNs corrigidos
    wp_enqueue_style('emoji-mart-css', 'https://cdn.jsdelivr.net/npm/emoji-mart@5.6.0/dist/browser.min.css');
    wp_enqueue_script('emoji-mart-js', 'https://cdn.jsdelivr.net/npm/emoji-mart@5.6.0/dist/browser.min.js', array(), '5.6.0', true);
    
    // Carrega o script customizado de um arquivo externo
    wp_enqueue_script('emoji-selector-script', get_template_directory_uri() . '/js/emoji-selector.js', array('emoji-mart-js'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'adicionar_seletor_emojis_comentarios');

/**
 * Formata uma data no padrão brasileiro por extenso
 * 
 * @param string $data_original Data no formato dd/mm/yyyy
 * @param bool $capitalizar Se true, capitaliza o primeiro caractere (padrão: true)
 * @return string Data formatada ou string vazia em caso de erro
 */
function formatar_data_por_extenso($data_original, $capitalizar = true) {
    
	$data = null;
    
    // Verifica se a data está no formato dd/mm/aaaa
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data_original)) {
        $data = DateTime::createFromFormat('d/m/Y', $data_original);
    } 
    // Verifica se a data está no formato aaaammdd
    elseif (preg_match('/^\d{8}$/', $data_original)) {
        $data = DateTime::createFromFormat('Ymd', $data_original);
    }
    
    if ($data === false) {
        return '';
    }

    // Tenta usar IntlDateFormatter (mais moderno)
    if (class_exists('IntlDateFormatter')) {
        try {
            $formatter = new IntlDateFormatter(
                'pt_BR',
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                null,
                null,
                "EEEE, 'dia' d 'de' MMMM 'de' yyyy"
            );
            $data_formatada = $formatter->format($data);
            
            if ($capitalizar) {
                $data_formatada = mb_convert_case($data_formatada, MB_CASE_TITLE, "UTF-8");
            }
            
            return $data_formatada;
        } catch (Exception $e) {
            // Continua para o método alternativo se falhar
        }
    }

    // Método alternativo para quando Intl não está disponível
    $dias_semana = array(
        'Sunday'    => 'Domingo',
        'Monday'    => 'Segunda-feira',
        'Tuesday'   => 'Terça-feira',
        'Wednesday' => 'Quarta-feira',
        'Thursday'  => 'Quinta-feira',
        'Friday'    => 'Sexta-feira',
        'Saturday'  => 'Sábado'
    );

    $meses = array(
        'January'   => 'janeiro',
        'February'  => 'fevereiro',
        'March'     => 'março',
        'April'     => 'abril',
        'May'       => 'maio',
        'June'      => 'junho',
        'July'      => 'julho',
        'August'    => 'agosto',
        'September' => 'setembro',
        'October'   => 'outubro',
        'November'  => 'novembro',
        'December'  => 'dezembro'
    );

    $dia_semana_ingles = $data->format('l');
    $mes_ingles = $data->format('F');
    
    $data_formatada = sprintf(
        '%s, %d de %s de %Y',
        $dias_semana[$dia_semana_ingles],
        $data->format('d'),
        $meses[$mes_ingles],
        $data->format('Y')
    );

    if ($capitalizar) {
        $data_formatada = mb_convert_case($data_formatada, MB_CASE_TITLE, "UTF-8");
    }

    return $data_formatada;
}

function formatar_hora($hora_original) {
    // Verifica se o formato está correto (hh:mm:ss)
    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $hora_original)) {
        return $hora_original; // Retorna original se formato inválido
    }

    list($horas, $minutos, $segundos) = explode(':', $hora_original);

    // Remove zeros à esquerda (opcional)
    $horas = ltrim($horas, '0');
    $horas = $horas === '' ? '0' : $horas; // Se era "00", vira "0"

    // Formatação condicional
    if ($minutos === '00') {
        return $horas . 'h'; // Exemplo: "8h" ou "0h" (meia-noite)
    } else {
        return $horas . 'h' . ltrim($minutos, '0'); // Exemplo: "8h30" ou "0h5"
    }
}

add_action('rest_api_init', function() {

    // Rota para múltiplos posts
	register_rest_route('custom/v1', '/posts/', [
        'methods' => 'GET',
        'callback' => 'custom_posts_endpoint',
        'args' => [
            'meta_query' => [
                'description' => 'Filtros por meta fields (JSON)',
                'type' => 'string',
            ],
            'fields' => [
                'description' => 'Campos a retornar (id,title,content,meta,etc)',
                'type' => 'string',
                'default' => 'id,title,excerpt,meta',
            ],
            'per_page' => [
                'description' => 'Posts por página',
                'type' => 'integer',
                'default' => 10,
            ],
			'post__not_in' => [
				'description' => 'ID do post que não devem ser retornado',				
				'type' => 'string',
				'default' => '',

			],
            'page' => [
                'description' => 'Página atual',
                'type' => 'integer',
                'default' => 1,
            ],
			'tag_ids' => [
				'description' => 'IDs das tags para filtrar (separados por vírgula)',
				'type' => 'string',
				'validate_callback' => function($param) {
					return is_string($param); // Validação básica
				}
			],
			'search' => [
				'description' => 'Nome do evento a ser buscado',
				'type' => 'string',
				'validate_callback' => function($param) {
					return is_string($param); // Validação básica
				}
			],
        ],
        'permission_callback' => '__return_true'
    ]);

	// Nova rota para post único por ID
    register_rest_route('custom/v1', '/posts/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'custom_single_post_endpoint',
        'args' => [
            'fields' => [
                'description' => 'Campos a retornar (id,title,content,meta,etc)',
                'type' => 'string',
                'default' => 'id,title,content,meta',
            ],
        ],
        'permission_callback' => '__return_true'
    ]);
});

function custom_posts_endpoint($request) {

	$status = $request->get_param('status');

	global $wpdb;

	$sticky = get_option( 'sticky_posts' );

    $args1 = [
        'post_type' => ['post', 'cortesias'],
        'posts_per_page' => -1,
        'post_status' => 'publish',
		'fields' => 'ids',
		'post__in'  => $sticky,  
    ];

	$args2 = [
        'post_type' => ['post', 'cortesias'],
        'posts_per_page' => -1,
        'post_status' => 'publish',
		'fields' => 'ids',
		'post__not_in' => $sticky, 
    ];
    
	// Adiciona tag se fornecido
    if ($tag_ids = $request->get_param('tag_ids')) {				

		$args1['tax_query'][] = array(
			'taxonomy' => 'post_tag',
			'field'    => 'term_id',
			'terms'    => $tag_ids,
		);
		
		$args2['tax_query'][] = array(
			'taxonomy' => 'post_tag',
			'field'    => 'term_id',
			'terms'    => $tag_ids,
		);		
    }
    
    // Adiciona meta_query se fornecido
    if ($meta_query = $request->get_param('meta_query')) {
		$args1['meta_query'] = json_decode($meta_query, true);
        $args2['meta_query'] = json_decode($meta_query, true);
    }

	$post__not_in = $request->get_param('post__not_in');

	if ($post__not_in) {
		$ids = array_map('intval', explode(',', $post__not_in));

		$args1['post__not_in'] = $ids;
		$args2['post__not_in'] = $ids;
	}

	$args1['meta_query'][] = [
		'relation' => 'OR',
		[
			'key'     => 'exibir_portal',
			'compare' => 'NOT EXISTS',
		],
		[
			'key'     => 'exibir_portal',
			'value'   => '1',
			'compare' => '=',
		]
	];

	$args2['meta_query'][] = [
		'relation' => 'OR',
		[
			'key'     => 'exibir_portal',
			'compare' => 'NOT EXISTS',
		],
		[
			'key'     => 'exibir_portal',
			'value'   => '1',
			'compare' => '=',
		]
	];

	if($request->get_param('search')){
		$args1['s'] = $request->get_param('search');
		$args2['s'] = $request->get_param('search');
	}

	// Define a função
	

	// Antes de criar a query, adiciona o filtro
    add_filter('posts_where', 'wpza_replace_repeater_field');
    
    $query1 = new WP_Query($args1);
	$query2 = new WP_Query($args2);

	// Depois de criar a query, remove o filtro para não afetar outras queries
    remove_filter('posts_where', 'wpza_replace_repeater_field');

    $selected_fields = explode(',', $request->get_param('fields'));

	$allTheIDs = array_merge($query1->posts,$query2->posts);

	if($status == 'encerrados'){
		$args = array(
			'post_type' => ['post', 'cortesias'],
			'post__in' => $allTheIDs,
			'posts_per_page' => $request->get_param('per_page'),
			'paged' => $request->get_param('page'),
			'ignore_sticky_posts' => 1,
			'meta_key' => 'enc_inscri',
			'orderby' => 'meta_value_num', 
			'order' => 'DESC'
		);
	} else {
		$args = array(
			'post_type' => ['post', 'cortesias'],
			'post__in' => $allTheIDs,
			'posts_per_page' => $request->get_param('per_page'),
			'paged' => $request->get_param('page'),
			'ignore_sticky_posts' => 1,
			'orderby' => 'post__in'
		);
	}

	$data = [];
	//create new empty query and populate it with the other two
	if($allTheIDs){
		$the_query = new WP_Query($args);
		
		foreach ($the_query->get_posts() as $post) {
			$post_data = [];
			$post_type = get_post_type_label( $post->ID );
			
			// Campos básicos
			if (in_array('id', $selected_fields)) $post_data['id'] = $post->ID;
			if (in_array('title', $selected_fields)) $post_data['title'] = $post->post_title;
			if (in_array('content', $selected_fields)) $post_data['content'] = $post->post_content;
			if (in_array('excerpt', $selected_fields)) $post_data['excerpt'] = $post->post_excerpt;
			if (in_array('date', $selected_fields)) $post_data['date'] = $post->post_date;
			if (in_array('slug', $selected_fields)) $post_data['slug'] = $post->post_name;

			$categories = get_the_category($post->ID);

			// Extrai apenas os nomes das categorias
			$category_names = array();
			if (!empty($categories)) {
				foreach ($categories as $category) {
					$category_names[] = $category->name;
				}
				// Junta os nomes separados por vírgula
				$categories_list = implode(', ', $category_names);
				$post_data['categories'] = $categories_list;				
			}

			if ( $post_type === 'cortesias' ) {
				$post_data['subtitulo'] = ( $status == 'encerrados' )
					? 'Evento encerrado. Consulte mais detalhes na notícia'
					: 'Ingressos gratuitos por ordem de inscrição, enquanto houver disponibilidade'; 
			} else {

				$texto_subtitulo = ( $status == 'encerrados' ) ? 'Sorteio' : 'Sorteio será realizado';
				$dataSorteio = ( $status == 'encerrados' ) ? obter_ultima_data_sorteio( $post->ID ) : obter_proxima_data_sorteio( $post->ID );
				$post_data['subtitulo'] = $texto_subtitulo . ' ' . $dataSorteio;
			}
			
			$totalrow = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$post->ID'");
			$total_like = $wpdb->num_rows;

			$post_data['likes'] = $total_like;

			//Tipo de post: Sorteio ou Cortesia
			$post_data['post_type'] = $post_type;

			// Custom fields (meta)
			if (in_array('meta', $selected_fields)) {
				$meta_fields = get_post_meta($post->ID);
				$post_data['meta'] = array_map(function($v) { 
					return is_serialized($v[0]) ? unserialize($v[0]) : $v[0];
				}, $meta_fields);
			}
			
			// Imagem destacada (thumbnail)
			if (in_array('thumbnail', $selected_fields)) {
				$thumbnail_id = get_post_thumbnail_id($post->ID);
				$post_data['thumbnail'] = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'default-image') : '';
			}
			
			$data[] = $post_data;
		}
	}
    
    $response = new WP_REST_Response($data, 200);
    $response->header('X-WP-Total', $query->found_posts);
    $response->header('X-WP-TotalPages', $query->max_num_pages);
    
    return $response;
}

function custom_single_post_endpoint($request) {
    global $wpdb;
    
    $post_id = $request->get_param('id');
    $selected_fields = explode(',', $request->get_param('fields'));
    
    // Verifica se o post existe
    $post = get_post($post_id);
    if (!$post || $post->post_status !== 'publish') {
        return new WP_Error('not_found', 'Post não encontrado', ['status' => 404]);
    }
    
    $post_data = [];

    // Campos básicos
    if (in_array('id', $selected_fields)) $post_data['id'] = $post->ID;
    if (in_array('title', $selected_fields)) $post_data['title'] = $post->post_title;
    if (in_array('content', $selected_fields)) $post_data['content'] = $post->post_content;
    if (in_array('excerpt', $selected_fields)) $post_data['excerpt'] = $post->post_excerpt;
    if (in_array('date', $selected_fields)) $post_data['date'] = $post->post_date;
    if (in_array('slug', $selected_fields)) $post_data['slug'] = $post->post_name;
    
    // Likes (mantendo sua lógica existente)
    $totalrow = $wpdb->get_results("SELECT id FROM $wpdb->post_like_table WHERE postid = '$post->ID'");
    $post_data['likes'] = $wpdb->num_rows;
    $post_data['sorteados'] = [];
	$post_data['datas_dispo'] = [];

	$genero = get_field('genero_taxo', $post->ID);
	$local = get_field('local', $post->ID);
	$local_outros = get_field('local_outros', $post->ID);
	$post_type = get_post_type_label( $post->ID );

	if($genero){
		$post_data['genero'] = $genero->name;
	}

	if($local && $local != 'outros'){
		$term = get_term($local);

		if ($term && !is_wp_error($term)) {
			$post_data['local'] = $term->name;
		}
		
	}
	if($local && $local == 'outros'){
		$post_data['local'] = $local_outros;
	}

	$post_data['post_type'] = $post_type;

    // Custom fields (meta)
    if (in_array('meta', $selected_fields)) {
        $meta_fields = get_post_meta($post->ID);
        $post_data['meta'] = array_map(function($v) { 
            return is_serialized($v[0]) ? unserialize($v[0]) : $v[0];
        }, $meta_fields);

		$tipo_evento = get_field('tipo_evento', $post_id);

		$datas_evento = obter_datas_evento_formatadas( $post_id );
		if( $datas_evento ){
			$post_data['meta']['data_evento_form'] = $datas_evento;
		}

		if($post_data['meta']['data_sorteio'] && $post_data['meta']['data_sorteio'] != ''){
			$post_data['meta']['data_sorteio_form'] = formatar_data_por_extenso($post_data['meta']['data_sorteio'], false);
		}

		if($post_data['meta']['hora_evento'] && $post_data['meta']['hora_evento'] != ''){
			$post_data['meta']['hora_evento'] = formatar_hora($post_data['meta']['hora_evento']);
		}

		$current_date = date('Ymd');

		// Obtendo o valor da data de encerramento
		$enc_inscri = get_field('enc_inscri', $post_id);

		// Verificando se a data de encerramento é menor que a data atual
		$status_prefix = ($enc_inscri < $current_date) ? 'ENCERRADO - ' : '';
		$post_data['title_prefix'] = $status_prefix;

		$dataSorteio = ($enc_inscri < $current_date) ? obter_ultima_data_sorteio( $post_id ) : obter_proxima_data_sorteio( $post_id );

		if($dataSorteio && $post_type === 'sorteio'){
			$texto_subtitulo = ($enc_inscri < $current_date) ? 'Sorteio' : 'Sorteio será realizado';
			$post_data['subtitulo'] = $texto_subtitulo . ' ' . $dataSorteio;	
		}

		if ( $post_type === 'cortesias' ) {
			$texto_subtitulo = ($enc_inscri < $current_date)
				? 'Evento encerrado. Consulte mais detalhes na notícia'
				: 'Ingressos gratuitos por ordem de inscrição, enquanto houver disponibilidade';
			$post_data['subtitulo'] = $texto_subtitulo;
		}

		$publicado = get_the_date('d/m/Y G\hi', $post_id);
		if($publicado){
			$post_data['data_publicacao'] = $publicado;
		}

		$atualizado = get_the_modified_date('d/m/Y', $post_id);
		if($atualizado){
			$post_data['data_atualizacao'] = $atualizado;
		}

		$term_obj_list = get_the_terms( $post_id, 'category' );
		if($term_obj_list){
			$post_data['categorias'] = [];
			foreach($term_obj_list as $categoria){
				$post_data['categorias'][] = $categoria->name;
			}
		}
		
		if( !$post_data['meta']['insira_o_subtitulo'] || ($post_data['meta']['insira_o_subtitulo'] && $post_data['meta']['insira_o_subtitulo'] != '')){
			$post_data['meta']['insira_o_subtitulo'] = 'O Sorteio será realizado '.formatar_data_por_extenso(get_post_meta($post->ID, 'data_sorteio', true), false);
		}

		if($post_data['meta']['exibe_resultado_pagina'] == '1'){
			if($tipo_evento == 'premio'){
				$datas_disponiveis = get_field('evento_premios', $post_id);
			} elseif ($tipo_evento == 'data') {
				$datas_disponiveis = get_field('evento_datas', $post_id);
			}
			$tabela =  'int_inscricoes';

			if($post_data['meta']['tipo_evento'] != 'periodo' && $datas_disponiveis && $datas_disponiveis != ''){				
				// Ordenar por data do evento
				usort($datas_disponiveis, function($a, $b) {
					return strtotime($a['data']) - strtotime($b['data']);
				});

				foreach ($datas_disponiveis as $data) {
					$resultados = $wpdb->get_results(
						"SELECT * FROM $tabela 
						WHERE post_id = $post_id AND sorteado = 1 AND data_sorteada = '" . $data['data'] . "'
						ORDER BY data_hora_sorteado
						ASC", 
						ARRAY_A 
					);

					if (!empty($resultados)) {
						$dataSorteio = date('d/m/Y', strtotime($data['data_sorteio']));
						$dataEvento = date('d/m/Y H\hi', strtotime($data['data']));
						$dataEvento = str_replace('h00', 'h', $dataEvento);

						foreach ($resultados as $key => $linha) {
							if (isset($linha['user_id'])) {
								$tipo = get_user_meta($linha['user_id'], 'parceira', true);
								if ($tipo == 1) {
									$tipo = 'PARCEIRO';
								} else if ($tipo == 0) {
									$tipo = 'SERVIDOR';
								}
							} else { // Programa 1, 2 ou 3
								if (isset($linha['programa_estagio'])) {
									$tipo = 'ESTAGIÁRIO';
								}
							} 

							$item = file_get_contents(get_template_directory().'/includes/sorteio/conteudo-tab-lista-sorteados.html');
							$item = str_replace('{NOME-SORTEADO}',     esc_html(mb_strtoupper($linha['nome_completo']), 'UTF-8'),   $item);
							$item = str_replace('{TIPO-SORTEADO}',    esc_html(mb_strtoupper($tipo), 'UTF-8'), $item);
							$itens .= $item;
						}

						
				
						$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-lista-sorteados-view.html');
						$html = str_replace('{CONTEUDO-LISTA-SORTEADOS}', $itens, $html);
						$html = str_replace('{DATA-SORTEIO}', $dataSorteio, $html);
						if($tipo_evento == 'premio'){
							$texto = 'Contemplados <strong>' . $data['premio'] . '</strong>';
							$html = str_replace('{TEXTO-COLLAPSE}', $texto, $html);
						} else {
							$texto = 'Contemplados para evento do dia <strong>' . $dataEvento . '</strong>';
							$html = str_replace('{TEXTO-COLLAPSE}', $texto, $html);
						}
						$html = str_replace('{ITEM-ID}', $data['data_sorteio'] . '-' . $key, $html);

						$post_data['sorteados'][] = $html;
						$itens = '';

					}				
				}

				//$post_data['sorteados'] = $datas_disponiveis;
			} elseif (  isset( $post_data['meta']['tipo_evento'] ) && $post_data['meta']['tipo_evento'] === 'periodo' ) {
				$resultados = $wpdb->get_results(
					"SELECT * FROM $tabela 
					WHERE post_id = $post_id AND sorteado = 1
					ORDER BY data_hora_sorteado
					ASC", 
					ARRAY_A 
				);
	
				if (!empty($resultados)) {
					$info_periodo_evento = get_field( 'evento_periodo', $post_id );
					$dataSorteio = $info_periodo_evento['data_sorteio'];
	
					foreach ($resultados as $linha) {
						if (isset($linha['user_id'])) {
							$tipo = get_user_meta($linha['user_id'], 'parceira', true);
							if ($tipo == 1) {
								$tipo = 'PARCEIRO';
							} else if ($tipo == 0) {
								$tipo = 'SERVIDOR';
							}
						} else { // Programa 1, 2 ou 3
							if (isset($linha['programa_estagio'])) {
								$tipo = 'ESTAGIÁRIO';
							}
						} 
	
						$item = file_get_contents(get_template_directory().'/includes/sorteio/conteudo-tab-lista-sorteados.html');
						$item = str_replace('{NOME-SORTEADO}',     esc_html(mb_strtoupper($linha['nome_completo']), 'UTF-8'),   $item);
						$item = str_replace('{TIPO-SORTEADO}',    esc_html(mb_strtoupper($tipo), 'UTF-8'), $item);
						$itens .= $item;
					}
	
					$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-lista-sorteados-view.html');
					$html = str_replace('{CONTEUDO-LISTA-SORTEADOS}', $itens, $html);
					$html = str_replace('{TEXTO-COLLAPSE}', 'Contemplados do evento', $html);
					$html = str_replace('{DATA-SORTEIO}', $dataSorteio, $html);
					$html = str_replace('{DATA-EVENTO}', '', $html);
					$html = str_replace('{ITEM-ID}', $post_id, $html);
	
					$post_data['sorteados'][] = $html;
					$itens = '';
	
				}
			} else {
				$resultados = $wpdb->get_results(
					"SELECT * FROM $tabela 
					WHERE post_id = $post_id AND sorteado = 1
					ORDER BY data_hora_sorteado
					ASC", 
					ARRAY_A 
				);

				if (!empty($resultados)) {
					$dataSorteio = date( 'd/m/Y', strtotime( get_field( 'data_sorteio', $post_id ) ) );

					foreach ($resultados as $linha) {
						if (isset($linha['user_id'])) {
							$tipo = get_user_meta($linha['user_id'], 'parceira', true);
							if ($tipo == 1) {
								$tipo = 'PARCEIRO';
							} else if ($tipo == 0) {
								$tipo = 'SERVIDOR';
							}
						} else { // Programa 1, 2 ou 3
							if (isset($linha['programa_estagio'])) {
								$tipo = 'ESTAGIÁRIO';
							}
						} 

						$item = file_get_contents(get_template_directory().'/includes/sorteio/conteudo-tab-lista-sorteados.html');
						$item = str_replace('{NOME-SORTEADO}',     esc_html(mb_strtoupper($linha['nome_completo']), 'UTF-8'),   $item);
						$item = str_replace('{TIPO-SORTEADO}',    esc_html(mb_strtoupper($tipo), 'UTF-8'), $item);
						$itens .= $item;
					}

					$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-lista-sorteados-view.html');
					$html = str_replace('{CONTEUDO-LISTA-SORTEADOS}', $itens, $html);
					$html = str_replace('{DATA-SORTEIO}', $dataSorteio, $html);
					$html = str_replace('{DATA-EVENTO}', '', $html);
					$html = str_replace('{ITEM-ID}', $post_id, $html);
					$html = str_replace('{TEXTO-COLLAPSE}', 'Contemplados', $html);

					$post_data['sorteados'][] = $html;
					$itens = '';

				}
			}
			//$post_data['sorteados'] = json_encode(exibeTabResultadoPagina($post->ID));
		} 
    }
    
    // Imagem destacada
    if (in_array('thumbnail', $selected_fields)) {
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        $post_data['thumbnail'] = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'default-image') : '';
    }

	$tipo_evento = get_field('tipo_evento', $post->ID);
	if($tipo_evento == 'premio'){
		$datas_disponivies = get_field('evento_premios', $post->ID);
		$post_data['tipo_evento'] = 'premio';
	} elseif ($tipo_evento == 'data') {
		$datas_disponivies = get_field('evento_datas', $post->ID);
	}
	

	if($datas_disponivies){
		foreach ($datas_disponivies as $data) {
			$diponivel = verifica_disponibilidade_data_inscricao($post->ID, $data['data'], $tipo_evento);
			if ($diponivel) {
				if($tipo_evento == 'premio'){
					$post_data['datas_dispo'][$data['data']] = esc_html( $data['premio'] );
				} else {
					$post_data['datas_dispo'][$data['data']] = esc_attr( date( 'd/m/Y', strtotime( $data['data'] ) ) ) . ' ' . date( 'H:i', strtotime( $data['hora'] ) );
				}
			}
		}
	}
    
    return new WP_REST_Response($post_data, 200);
}

// Endpoint atualizado para salvar inscrições
add_action('rest_api_init', function () {
    register_rest_route('inscricao/v1', '/salvar', [
        'methods'  => 'POST',
        'callback' => 'salvar_inscricao',
        'permission_callback' => function() {
            return true;
        }
    ]);
});

// Endpoint atualizado para validar inscrições
add_action('rest_api_init', function () {
    register_rest_route('inscricao/v1', '/validar', [
        'methods'  => 'POST',
        'callback' => 'verificar_cpf',
        'permission_callback' => function() {
            return true;
        }
    ]);
});

// Endpoint soliciar email cancelamento inscrição
add_action('rest_api_init', function () {
    register_rest_route('inscricao/v1', '/cancelar', [
        'methods'  => 'POST',
        'callback' => 'email_cancelar_inscricao',
        'permission_callback' => function() {
            return true;
        }
    ]);
});

function validar_token_api($request) {
    $token = $request->get_header('X-Auth-Token');
    return ($token === 'SEU_TOKEN_SECRETO'); // Substitua por um token forte
}

function salvar_inscricao($request) {
    global $wpdb;
    $tabela = $wpdb->prefix . 'inscricoes'; // int_inscricoes
	$tabela_sancoes = $wpdb->prefix . 'inscricao_sancoes'; // int_inscricao_sancoes

    $dados = $request->get_json_params();

    // Validação dos campos obrigatórios
    if (empty($dados['nomeComp']) || empty($dados['emailInsti']) || empty($dados['cpf']) || empty($dados['external_sorteio_id'])) {
        return new WP_Error('dados_invalidos', 'Nome completo, email institucional, CPF e ID do sorteio são obrigatórios.', ['status' => 400]);
    }

    // Mapeamento dos campos
    $dados_insercao = [
        'nome_completo'        => sanitize_text_field($dados['nomeComp']),
        'email_institucional'  => sanitize_email($dados['emailInsti']),
        'cpf'                  => sanitize_text_field($dados['cpf']),
        'email_secundario'     => !empty($dados['emailSec']) ? sanitize_email($dados['emailSec']) : '',
        'celular'              => sanitize_text_field($dados['celular'] ?? ''),
        'dre'                  => sanitize_text_field($dados['dre'] ?? ''),
        'telefone_comercial'   => sanitize_text_field($dados['telCom'] ?? ''),
        'unidade_setor'        => sanitize_text_field($dados['uniSetor'] ?? ''),
        'ciente'               => !empty($dados['ciente']) ? 1 : 0,
        'remanescentes'        => !empty($dados['remanescentes']) ? 1 : 0,
        'programa_estagio'     => intval($dados['programa_estagio'] ?? 0),
        'post_id'              => absint($dados['external_sorteio_id']), // ⬅️ Recebido do Site A
        'data_inscricao'       => current_time('mysql'),
		//'datas'                => $dados['datas'] ?? []
    ];

    // Verificação de duplicidade ANTES da inserção (mais eficiente)
    $cpf_existe = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $tabela WHERE post_id = %d AND cpf = %s",
        $dados_insercao['post_id'],
        $dados_insercao['cpf']
    ));

	$cpf_sancao = $wpdb->get_var($wpdb->prepare(
		"SELECT data_validade FROM $tabela_sancoes WHERE cpf = %s",
		$dados_insercao['cpf']
	));

	$ativo = false;
	$dataPermissao = '';

	if ($cpf_sancao) {
		$hoje = new DateTime('today', new DateTimeZone('America/Sao_Paulo')); 
		$validade = new DateTime($cpf_sancao, new DateTimeZone('America/Sao_Paulo'));

		if ($validade >= $hoje) {
			$ativo = true;
			$validadeMaisUm = clone $validade;
			$validadeMaisUm->modify('+1 day');
			$dataPermissao = $validadeMaisUm->format('d/m/Y');
		}
	}

	if ($ativo) {
		wp_send_json([
			'code' => 'cpf_sancao',
			'message' => 'CPF já cadastrado para este sorteio.',
			'data_permissao' => $dataPermissao,
		], 410); // ← Status HTTP aqui
	}

    if ($cpf_existe > 0) {
		wp_send_json([
			'code' => 'cpf_duplicado',
			'message' => 'CPF já cadastrado para este sorteio.',
		], 409); // ← Status HTTP aqui
	} else {
		// Tentativa de inserção
		$resultado = $wpdb->insert($tabela, $dados_insercao);

		if ($resultado === false) {
			return new WP_REST_Response([
				'code' => 'erro_bd',
				'message' => 'Falha ao salvar no banco de dados.',
				'data' => ['status' => 500]
			], 500);
		}

		if($resultado !== false) {
			$datas = $dados['datas'] ?? [];
			$inscricao_id = $wpdb->insert_id;

			foreach ( $datas as $data ) {
				$wpdb->insert(
					$wpdb->prefix . 'inscricao_datas',
					[
						'inscricao_id' => $inscricao_id,
						'data_evento'  => $data,
					],
					['%d', '%s']
				);
			}
			
		}
	
		return new WP_REST_Response([
			'success' => true,
			'id' => $wpdb->insert_id,
			'message' => 'Inscrição efetuada com sucesso!'
		], 200);
	}
    
}

function validar_cpf_inscricao($request) {
    global $wpdb;
	$tabela_sancoes = $wpdb->prefix . 'inscricao_sancoes'; // int_inscricao_sancoes

    $cpf = $request->get_param('cpf');

    $cpf_sancao = $wpdb->get_var($wpdb->prepare(
        "SELECT data_validade FROM $tabela_sancoes WHERE cpf = %s",
        $cpf
    ));

    $ativo = false;
    $dataPermissao = '';

    if ($cpf_sancao) {
        $hoje = new DateTime('today', new DateTimeZone('America/Sao_Paulo')); 
        $validade = new DateTime($cpf_sancao, new DateTimeZone('America/Sao_Paulo'));

        if ($validade >= $hoje) {
            $ativo = true;
            $validadeMaisUm = clone $validade;
            $validadeMaisUm->modify('+1 day');
            $dataPermissao = $validadeMaisUm->format('d/m/Y');
        }
    }

	if ($ativo) {
		return new WP_REST_Response([
			'success' => true,
			'data_permissao' => $dataPermissao
		], 410);
	} else {
		return new WP_REST_Response([
			'success' => false,
			'message' => 'CPF não cadastrado para este sorteio.',
			'cpf' => $cpf,
			'cpf_sancao' => $cpf_sancao
		], 409);
	}
}

function email_cancelar_inscricao($request) {
	$cpf = $request->get_param('cpf');
	$post_id = $request->get_param('post_id');

	global $wpdb;

	if($cpf == '' || $post_id == '') {
		return new WP_REST_Response([
			'success' => false,
			'message' => 'CPF ou ID inválido',
		], 409);
	}

	$tabela =  'int_inscricoes';
	$resultados = $wpdb->get_results("SELECT id FROM $tabela WHERE post_id = $post_id AND cpf = $cpf", ARRAY_A);

	if (empty($resultados)) {
		return new WP_REST_Response([
			'success' => false,
			'message' => 'CPF não cadastrado para este sorteio.',
			'cpf' => $cpf,
			'cpf_sancao' => $cpf_sancao
		], 409);
	}

	foreach($resultados as $item) {
		if (is_plugin_active('envia-email-sme/envia-email-sme.php')) {
			new Envia_Emails_Sorteio_SME($item['id'], null, $post_id, 'desistencia');
		}
	}

    return new WP_REST_Response([
		'success' => true,
		'message' => 'Email enviado com sucesso',
	], 200);
}

function adicionaPostMetaExibirResultado(){

	$post_id = get_the_id();
	$meta_key = 'exibe_resultado_pagina';
	$meta_value = '0';

	// Verifica se já existe e atualiza, senão adiciona
    if (get_post_meta($post_id, $meta_key, true) !== '') {} else {
        add_post_meta($post_id, $meta_key, $meta_value);
    }
}

add_action('init', function() {
    $file = get_stylesheet_directory() . '/acf/acf-field-shortcode-display.php';
    require_once($file);
    acf_register_field_type('ACF_Field_Shortcode_Display');
});

add_action('init', 'alterar_rotulos_tags_para_locais');
function alterar_rotulos_tags_para_locais() {
    global $wp_taxonomies;
    
    if (!isset($wp_taxonomies['post_tag'])) return;
    
    $wp_taxonomies['post_tag']->labels = (object) array(
        'name' => 'Locais',
        'singular_name' => 'Local',
        'search_items' => 'Buscar Locais',
        'popular_items' => 'Locais Populares',
        'all_items' => 'Todos os Locais',
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => 'Editar Local',
        'view_item' => 'Ver Local',
        'update_item' => 'Atualizar Local',
        'add_new_item' => 'Adicionar novo Local',
        'new_item_name' => 'Novo nome do Local',
        'separate_items_with_commas' => 'Separe os Locais com vírgulas',
        'add_or_remove_items' => 'Adicionar ou remover Locais',
        'choose_from_most_used' => 'Escolher entre os Locais mais usados',
        'not_found' => 'Nenhum Local encontrado',
        'no_terms' => 'Nenhum Local',
        'items_list_navigation' => 'Navegação na lista de Locais',
        'items_list' => 'Lista de Locais',
        'most_used' => 'Locais mais usados',
        'back_to_items' => '← Voltar para Locais',
        'menu_name' => 'Locais',
        'name_admin_bar' => 'Local'
    );
    
    $wp_taxonomies['post_tag']->label = 'Locais';
}

add_action('admin_head', function () {
    $screen = get_current_screen();
    
    // Apenas no tipo de post desejado
    if ('post' === $screen->post_type) { 
        echo '<style>
            #tagsdiv-post_tag { 
                display: none; 
            }
        </style>';
    }
});

// Alterar ordem do menu midias e posts (Sorteios) (os: 127730)
add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', 'custom_reorder_admin_menu');

function custom_reorder_admin_menu($menu_order) {
    if (!$menu_order) return true;

    $media_index = array_search('upload.php', $menu_order);
    $posts_index = array_search('edit.php', $menu_order);

    // Verifica se os dois itens existem e o índice da mídia é maior que o de posts
    if ($media_index !== false && $posts_index !== false && $media_index > $posts_index) {
        // Troca as posições
        $temp = $menu_order[$media_index];
        $menu_order[$media_index] = $menu_order[$posts_index];
        $menu_order[$posts_index] = $temp;
    }

    return $menu_order;
}

// Remover o role subscriber ao atualizar o usuario
add_action( 'edit_user_profile_update', 'remover_capacidade_subscriber', 100 );

function remover_capacidade_subscriber( $user_id ) {
    $user = new WP_User( $user_id );

    if ( $user->has_cap( 'subscriber' ) ) {
        // Remove a capability 'subscriber' diretamente
        $user->remove_cap( 'subscriber' );
    }
}

/**
 * Retorna a data atual formatada no timezone especificado.
 *
 * @param string $formato Formato da data, ex: 'd/m/Y H:i:s'
 * @param string $timezone Timezone, ex: 'America/Sao_Paulo'
 * @return string Data formatada no timezone desejado
 */
function obter_data_com_timezone( $formato = 'd/m/Y H:i:s', $timezone = 'UTC' ) {

    $original_tz = date_default_timezone_get();
    date_default_timezone_set( $timezone );

    $data_formatada = date( $formato );

    date_default_timezone_set( $original_tz );

    return $data_formatada;
}

//#################################################################################//
//############################### FUNÇÕES DO SORTEIO ##############################//
//#################################################################################//
include_once get_template_directory() . '/includes/sorteio/funcoes/sorteioCtrl.php';
//#################################################################################//

//#################################################################################//
//############################### FUNÇÕES DE GRATUIDADE E CORTESIAS ##############################//
//#################################################################################//
include_once get_template_directory() . '/includes/cortesias/funcoes/cortesiaController.php';
//#################################################################################//

// Alterar rotulo descrição para endereço
function alterar_rotulo_descricao_para_endereco() {
    $screen = get_current_screen();

    if ( $screen && $screen->taxonomy === 'post_tag' ) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Adição de novo Local (formulário simples)
                const addLabel = document.querySelector('label[for="tag-description"]');
                if (addLabel) {
                    addLabel.textContent = 'Endereço';
                }

                const addHelp = document.querySelector('#description-description');
                if (addHelp) {
                    addHelp.textContent = 'Insira o endereço do local cadastrado para mostrar na publicação do sorteio.';
                }

                // Edição de Local existente (formulário com <tr>)
                const editLabel = document.querySelector('label[for="description"]');
                if (editLabel) {
                    editLabel.textContent = 'Endereço';
                }

                const editHelp = document.querySelector('#description-description');
                if (editHelp) {
                    editHelp.textContent = 'Insira o endereço do local cadastrado para mostrar na publicação do sorteio.';
                }
            });
        </script>
        <?php
    }
}

add_action('admin_footer', 'alterar_rotulo_descricao_para_endereco');
add_action('admin_footer-edit-tags.php', 'alterar_rotulo_descricao_para_endereco');
add_action('admin_footer-term.php', 'alterar_rotulo_descricao_para_endereco');

function alterar_coluna_descricao_para_endereco($columns) {
    if (isset($columns['description'])) {
        $columns['description'] = 'Endereço';
    }
    return $columns;
}
add_filter('manage_edit-post_tag_columns', 'alterar_coluna_descricao_para_endereco');


// Preencher o campo de endereço com a descrição do local selecionado
add_action('acf/input/admin_footer', 'acf_local_atualiza_endereco_com_observer');
function acf_local_atualiza_endereco_com_observer() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'post') return;

    $tags = get_terms([
        'taxonomy' => 'post_tag',
        'hide_empty' => false,
    ]);

    $dados = [];
    foreach ($tags as $tag) {
        $dados[$tag->term_id] = esc_js($tag->description);
    }

    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        
		const descricoes = <?php echo json_encode($dados); ?>;
		const select = document.querySelector('#acf-field_67eec90fad2a6');
		const inputEndereco = document.querySelector('#acf-field_67eec95fcdafc');

		function atualizarEndereco(termId) {
			const descricao = descricoes[termId];
			const hasDescricao = typeof descricao === 'string' && descricao.trim() !== '';
			const hasValorAtual = inputEndereco.value.trim() !== '';

			if (hasDescricao) {
				// Só atualiza se for diferente do que já está
				if (inputEndereco.value !== descricao) {
					inputEndereco.value = descricao;
				}
				inputEndereco.setAttribute('readonly', 'readonly');
			} else {
				// Sem descrição: não sobrescreve se já existe valor
				inputEndereco.removeAttribute('readonly');

				// Se já está vazio, mantém vazio; se tem valor, não mexe
				if (!hasValorAtual) {
					inputEndereco.value = '';
				}
			}
		}


        function processarSelecao() {
            const termId = parseInt(select.value);
            if (!termId) return;

            if (!(termId in descricoes)) {
                // novo termo
                inputEndereco.value = '';
                inputEndereco.removeAttribute('readonly');
            } else {
                atualizarEndereco(termId);
            }
        }

        if (select && inputEndereco) {
            jQuery(select).on('select2:select', function () {
                processarSelecao();
            });

            // Executa a primeira vez
            processarSelecao();

            // Observa mudanças no select (ex: novo termo adicionado via botão "+")
            const observer = new MutationObserver(() => {
                processarSelecao();
            });

            observer.observe(select, {
                childList: true,
                subtree: true,
            });
        }
    });
    </script>
    <?php
}

// Registrar taxonomia "Gênero" para posts
add_action('init', 'registrar_taxonomia_genero');
function registrar_taxonomia_genero() {
    register_taxonomy('genero', ['post', 'cortesias'], [
        'labels' => [
            'name' => 'Gêneros',
            'singular_name' => 'Gênero',
            'search_items' => 'Buscar Gêneros',
            'all_items' => 'Todos os Gêneros',
            'edit_item' => 'Editar Gênero',
            'update_item' => 'Atualizar Gênero',
            'add_new_item' => 'Adicionar novo Gênero',
            'new_item_name' => 'Nome do novo Gênero',
            'menu_name' => 'Gêneros',
        ],
        'public' => true,
        'hierarchical' => false, // tipo "tag"
        'show_ui' => true, // mostra no menu do admin
        'show_in_menu' => true, // pode ser omitido
        'show_in_quick_edit' => false,
        'show_admin_column' => false,
        'meta_box_cb' => false, // remove do editor de post
        'show_in_rest' => true, // importante para Gutenberg e ACF
    ]);
}

// Alterar texto de ajuda da descrição do gênero e nome
add_filter('gettext', 'alterar_texto_ajuda_descricao_genero', 20, 3);
function alterar_texto_ajuda_descricao_genero($translated_text, $text, $domain) {
    // Altere apenas na tela de edição da taxonomia 'genero'
    if (is_admin() && isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'genero') {
        if ($translated_text === 'A descrição não está em destaque por padrão, entretanto alguns temas podem mostrá-la.') {
            return 'Insira uma breve descrição sobre esse gênero de evento (uso interno, opcional).';
        }

		if ($translated_text === 'O nome é como aparece em seu site.') {
            return 'Esse nome será exibido como o gênero do evento na notícia do sorteio (ex: Visita Cultural, Show Musical).';
        }
    }
    return $translated_text;
}

// Adicionar checkbox "Fixar no topo" editor de posts do CPT 'noticia'
add_action('post_submitbox_misc_actions', 'adicionar_sticky_manualmente');
function adicionar_sticky_manualmente() {
    if (get_post_type() === 'noticia') {
        $post = get_post();
        $sticky = is_sticky($post->ID);
        ?>
        <div class="misc-pub-section">
            <label><input type="checkbox" name="sticky" value="sticky" <?php checked($sticky); ?> /> Fixar no topo</label>
        </div>
        <?php
    }
}

add_action('save_post', 'salvar_sticky_manualmente');
function salvar_sticky_manualmente($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (get_post_type($post_id) !== 'noticia') return;
    
    if (isset($_POST['sticky'])) {
        stick_post($post_id);
    } else {
        unstick_post($post_id);
    }
}

// Adicionar widget de calendário de eventos ao dashboard do WordPress
function add_widget_calendario_dashboard() {
    include( 'includes/widgets/widget_eventos_dia.php' );
	include( 'includes/widgets/widget_eventos_encerrados.php' );
	include( 'includes/widgets/widget_eventos_semana.php' );
}

add_action('wp_dashboard_setup', 'add_widget_calendario_dashboard');

function shortcode_meu_botao_customizado() {
    return '<button class="btn-acf-repetidor btn btn-success btn-block d-flex justify-content-center align-items-center" type="button" data-sorteio-id="' . get_the_ID() . '">Sortear</button>';
}
add_shortcode('meu_botao_customizado', 'shortcode_meu_botao_customizado');

// Função que verifica se existem inscrições para uma data
function tem_inscricoes_na_data($post_id, $data) {
    global $wpdb;
    $qtdSorteados = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*)
        FROM {$wpdb->evento_inscricoes} i
        JOIN {$wpdb->evento_inscricao_datas} d ON d.inscricao_id = i.id
        WHERE i.post_id = %d
        AND d.data_evento = %s
    ", $post_id, $data));
    return $qtdSorteados > 0;
}

// Validação antes de salvar o post (evita salvar se datas com inscrições forem alteradas)
add_action('acf/validate_save_post', function() {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['acf'])) return;

    $post_id = $_POST['post_ID'];
	$tipo_evento = get_field( 'tipo_evento', $post_id );
	//$old_repeater = get_field('evento_datas', $post_id);

	//echo "<pre>";
	//print_r($_POST['acf']);
	//echo "</pre>";

	//echo "<pre>";
	//print_r($old_repeater);
	//echo "</pre>";

    // Chaves dos campos
    $repeater_key = 'field_687640c4cec04'; // Repeater - Homolog
    $data_field_key = 'field_687640e0cec05'; // Campo data dentro do repeater - Homolog
    //$repeater_key = 'field_686fdf82bf3a8'; // Repeater - DEV
    //$data_field_key = 'field_686fdfa6bf3a9'; // Campo data dentro do repeater - DEV

	// Recupera as datas já salvas no banco
    $old_datas = [];
    $old_repeater = get_field('evento_datas', $post_id);

	if ( $tipo_evento === 'premio' ) {
		$premios = [];
		$repeater_key = 'field_691dcbfccb8c7'; // Repeater - Homolog
    	$data_field_key = 'field_691dcbfccb8c8'; // Campo data dentro do repeater - Homolog
		$old_repeater = get_field('evento_premios', $post_id);
	}

    if ($old_repeater) {
        foreach ($old_repeater as $item) {
            $old_datas[] = $item['data'];

			if ( $tipo_evento === 'premio' ) {
				$premios[$item['data']] = $item['premio'];
			}
        }
    }

    // Recupera os novos dados enviados no formulário
    $new_datas = [];
    if (!empty($_POST['acf'][$repeater_key])) {
        foreach ($_POST['acf'][$repeater_key] as $item) {
			$data_bruta = $item[$data_field_key];
			$data_formatada = DateTime::createFromFormat('Y-m-d H:i:s', $data_bruta);
			if ($data_formatada) {
				$new_datas[] = $data_formatada->format('Y-m-d H:i:s');
			}
		}
    }

    // Compara e identifica datas removidas ou alteradas
    $datas_removidas = array_diff($old_datas, $new_datas);

	//$texto = implode(', ', $datas_removidas);

    foreach ($datas_removidas as $data) {
        if (tem_inscricoes_na_data($post_id, $data)) {
            // Adiciona erro global (no topo do formulário)
			$data_formatada = DateTime::createFromFormat('Y-m-d H:i:s', $data)->format('d/m/Y H\hi');
			$data_formatada = str_replace('h00', 'h', $data_formatada);

			if ( $tipo_evento === 'premio' ) {
				acf_add_validation_error('', "Não é permitido remover ou alterar o prêmio <strong>$premios[$data]</strong> pois já existem inscrições.");
			}

			if ( $tipo_evento === 'data' ) {
				acf_add_validation_error('', "Não é permitido remover ou alterar a data $data_formatada pois já existem inscrições.");
			}
        }
    }

}, 10);

add_action('acf/validate_save_post', function() {

    $repeater_key = 'field_687640c4cec04'; // Repeater - Homolog
    $data_field_key = 'field_687640e0cec05'; // Campo data dentro do repeater - Homolog
    //$repeater_key = 'field_686fdf82bf3a8'; // Repeater - DEV
    //$data_field_key = 'field_686fdfa6bf3a9'; // Campo data dentro do repeater - DEV

    // Se não existir no POST, não faz nada
    if (empty($_POST['acf'][$repeater_key]) || !is_array($_POST['acf'][$repeater_key])) {
        return;
    }

    $datas = [];

    foreach ($_POST['acf'][$repeater_key] as $index => $linha) {
        if (!empty($linha[$data_field_key])) {
            $valor = $linha[$data_field_key];

            // Checa se já existe
            if (in_array($valor, $datas, true)) {
                // Aponta o erro diretamente no campo repetidor
                acf_add_validation_error("", "Não é possível cadastrar uma sessão com a mesma data e horário já existente.");
                return;
            }

            $datas[] = $valor;
        }
    }
});


// Função AJAX para buscar datas de inscrição
add_action('wp_ajax_buscar_datas_inscricao', 'buscar_datas_inscricao');
function buscar_datas_inscricao() {
    $user_id = get_current_user_id();
    $post_id = intval($_POST['postId']);
	$tipo_evento = get_field( 'tipo_evento', $post_id );

    global $wpdb;

    $tabela_inscricoes = 'int_inscricoes';
    $tabela_datas = 'int_inscricao_datas';
    $modelo = '';
    $datas = [];

	if ( $tipo_evento === 'periodo' ) {
		wp_send_json_success([
            'modelo' => 'periodo'
        ]);
	}

    // 1. Tenta buscar inscrição com datas (modelo novo)
    $resultado = $wpdb->get_results($wpdb->prepare("
        SELECT i.*, d.data_evento
        FROM {$tabela_inscricoes} i
        INNER JOIN {$tabela_datas} d ON d.inscricao_id = i.id
        WHERE i.post_id = %d AND i.user_id = %d
    ", $post_id, $user_id));

    if (!empty($resultado)) {
        $modelo = 'multi';
        foreach ($resultado as $row) {
            $datas[] = $row->data_evento;
        }
    } else {
        // 2. Se não encontrar no modelo novo, tenta buscar no modelo antigo
        $resultado = $wpdb->get_row($wpdb->prepare("
            SELECT i.*, NULL as data_evento
            FROM {$tabela_inscricoes} i
            WHERE i.post_id = %d AND i.user_id = %d
        ", $post_id, $user_id));

        if (!empty($resultado)) {
			$dataRetorno = get_field('data_evento', $post_id, false);
			$dataFormatada = null;

			if (preg_match('/^\d{8}$/', $dataRetorno)) {
				// Ex: "20250521"
				$dataFormatada = substr($dataRetorno, 0, 4) . '-' . substr($dataRetorno, 4, 2) . '-' . substr($dataRetorno, 6, 2);
			} else {
				// Tenta converter formatos comuns
				foreach (['Y-m-d', 'd/m/Y', 'm/d/Y'] as $formato) {
					$date = DateTime::createFromFormat($formato, $dataRetorno);
					if ($date && $date->format($formato) === $dataRetorno) {
						$dataFormatada = $date->format('Y-m-d');
						break;
					}
				}
			}

            $modelo = 'unico';
            $datas[] = $dataFormatada; // ou uma data padrão se quiser representar o antigo
        }
    }

	if ( $tipo_evento === 'premio' ) {
		$modelo = 'premio';
		$cadastro_premio = get_field( 'evento_premios', $post_id );
		$premios = [];
		
		foreach ( $cadastro_premio as $item ) {
			$premios[$item['data']] = $item['premio'];
		}
		
		wp_send_json_success([
			'datas' => $datas,
			'modelo' => $modelo,
			'premios' => $premios
		]);

		die();
	}

    if (!empty($datas)) {
        wp_send_json_success([
            'datas' => $datas,
            'modelo' => $modelo
        ]);
    } else {
        wp_send_json_error();
    }
}

// Retorna a data/hora do evento formatada (Eventos de única e multiplas datas)
function obter_datas_evento_formatadas(int $post_id) {

	if ($datas_evento = get_field('evento_datas', $post_id)) {

		$lista_datas = array_map(function ($item) {

			// $item['data'] contém data e hora no formato Y-m-d H:i:s
			$data_hora = $item['data'];

			$data_formatada = date('d/m/Y', strtotime($data_hora));

			// Pega a hora formatada, adaptando sua função para receber o datetime completo
			$hora_formatada = obter_hora_formatada($data_hora);

			return "{$data_formatada} {$hora_formatada}";

		}, $datas_evento);

		return implode(' | ', $lista_datas);
	}

	if ($data_evento = get_field('data_evento', $post_id)) {

		$data_formatada = date('d/m/Y', strtotime($data_evento));
		$hora_formatada = obter_hora_formatada($data_evento);

		return "{$data_formatada} {$hora_formatada}";
	}

	return null;
}

// Retorna a hora no formato necessário para a exibição nos sorteios
function obter_hora_formatada(string $hora, $separador_minutos = ':') {
    // Extrai só a parte da hora no formato "HH:mm"
    $hora_minuto = date('H:i', strtotime($hora));

    $hora_formatada = explode($separador_minutos, $hora_minuto, 2);

    if (!isset($hora_formatada[0]) || empty($hora_formatada[0])) {
        return '';
    }

    if ($hora_formatada[1] == '00') {
        return "{$hora_formatada[0]}h";
    } else {
        return "{$hora_formatada[0]}h{$hora_formatada[1]}";
    }
}

function wpza_replace_repeater_field( $where ) {
	$where = str_replace(
		"meta_key = 'evento_datas_$",
		"meta_key LIKE 'evento_datas_%",
		$where
	);
	return $where;
}

/**
* Adiciona a capability necessária para que usuários do tipo editor consigam visualizar o menu de usuários.
*/
function adicionar_permissao_menu_usuarios() {
    $role = get_role( 'editor' );
    if ( $role && !$role->has_cap( 'list_users' ) ) {
        $role->add_cap( 'list_users' );
    }
}
add_action( 'init', 'adicionar_permissao_menu_usuarios' );

/**
* Remove submenus dos menu de Usuários 
*/
function remover_submenus_usuario_para_editores() {

	$current_user = wp_get_current_user();

    if ( in_array( 'editor', (array) $current_user->roles, true ) ) {
        remove_submenu_page( 'users.php', 'profile.php' );
    }
}

add_action( 'admin_menu', 'remover_submenus_usuario_para_editores', 999 );

/**
 * Remove a opção "ver" da listagem de usuários 
*/
add_filter( 'user_row_actions', function( $actions, $user ) {

	$current_user = wp_get_current_user();

    if ( isset( $actions['view'] ) && in_array( 'editor', (array) $current_user->roles, true ) ) {
        unset( $actions['view'] );
    }
    return $actions;
}, 10, 2 );

/**
 * Retorna um endereço de e-mail com caracteres cifrados, sem remover o dominio.
 * 
 * @param string $email Endereço de e-mail a ser mascarado
 * @return string E-mail mascarado ou string vazia se inválido
 * @testedBy MascararEmailTest
 * @since 1.0.0
 */
function mascarar_email( string $email ): string {
    if ( !is_email( $email ) ) {
        return '';
    }

    [$nome, $dominio] = explode( '@', $email );
    [ $dominio_nome, $dominio_ext ] = array_pad( explode('.', $dominio, 2), 2, '' );

    $tamanho_nome = strlen( $nome );
    
    // Tratamento especial para nomes curtos
    if ( $tamanho_nome <= 1 ) {
        return $email;
    } elseif ( $tamanho_nome == 2 ) {
        $nome_mascarado = substr( $nome, 0, 1 ) . '*';
    } elseif ( $tamanho_nome == 3 ) {
        $nome_mascarado = substr( $nome, 0, 2 ) . '*';
    } elseif ( $tamanho_nome == 4 ) {
        $nome_mascarado = substr( $nome, 0, 3 ) . '*';
    } else {
        // Para nomes com 5+ caracteres: 3 primeiros + asteriscos + último
        $nome_mascarado = substr( $nome, 0, 3 ) . str_repeat( '*', $tamanho_nome - 4 ) . substr( $nome, -1 );
    }

    return $nome_mascarado . '@' . $dominio_nome . '.' . $dominio_ext;
}

if ( ! function_exists( 'dd' ) ) {
    /**
     * Var_dump and die method
     *
     * @param mixed $data Qualquer tipo de dado para ser debugado.
     *
     * @return void
     */
    function dd( $data, $die = false ) {
 
        ini_set( 'highlight.comment', '#969896; font-style: italic' );
        ini_set( 'highlight.default', '#FFFFFF' );
        ini_set( 'highlight.html', '#D16568' );
        ini_set( 'highlight.keyword', '#7FA3BC; font-weight: bold' );
        ini_set( 'highlight.string', '#F2C47E' );
 
        $output = highlight_string( "<?php\n\n" . var_export( $data, true ), true );
 
        echo "<div style=\"background-color: #1C1E21; padding: 1rem\">{$output}</div>";
		if ( $die )
        	die;
    }
}

add_action('acf/input/admin_footer', function() {
?>
<style>
    /* Quando o botão estiver desabilitado, bloqueia interação */
    .acf-button.disabled {
        pointer-events: none;
        opacity: 0.5; /* feedback visual */
        cursor: not-allowed;
    }
</style>
<script type="text/javascript">
(function($){
    // Bloqueia o botão por 2 segundos após cada clique
    $(document).on('click', '.acf-field-repeater[data-name="evento_premios"] .acf-button[data-event="add-row"]', function(e){
        var $btn = $(this);
        $btn.addClass('disabled'); // adiciona classe disabled para desabilitar o botão
        setTimeout(function(){
            $btn.removeClass('disabled') // habilita novamente após 2s
        }, 1000);
    });

    // Lógica para preencher data única baseada no índice
    acf.add_action('append', function($el){
        if ($el.closest('.acf-field-repeater[data-name="evento_premios"]').length) {
            var $fieldWrapper = $el.find('.acf-field[data-name="data"]');
            var $hiddenField = $fieldWrapper.find('input.input-alt');
            var $visibleField = $fieldWrapper.find('input.input');

            if($hiddenField.length){
                var now = new Date();

                // Incrementa segundos com base no índice da linha
                var rowIndex = $el.closest('tbody').find('.acf-row').not('.acf-clone').length;
                now.setSeconds(now.getSeconds() + rowIndex);

                var dia   = String(now.getDate()).padStart(2, '0');
                var mes   = String(now.getMonth() + 1).padStart(2, '0');
                var ano   = now.getFullYear();
                var hora  = String(now.getHours()).padStart(2, '0');
                var min   = String(now.getMinutes()).padStart(2, '0');
                var seg   = String(now.getSeconds()).padStart(2, '0');

                var formattedHidden = ano + '-' + mes + '-' + dia + ' ' + hora + ':' + min + ':' + seg;
                var formattedVisible = dia + '/' + mes + '/' + ano + ' ' + hora + ':' + min + ':' + seg;

                $hiddenField.val(formattedHidden).trigger('change');
                $visibleField.val(formattedVisible).prop('readonly', true);
            }
        }
    });
})(jQuery);
</script>
<?php
});


add_action('acf/input/admin_footer', function() {
?>
<style>
    /* Oculta a coluna "Data" no repeater evento_premios */
    .acf-field-repeater[data-name="evento_premios"] table.acf-table th[data-name="data"],
    .acf-field-repeater[data-name="evento_premios"] table.acf-table td[data-name="data"] {
        display: none !important;
    }

	.acf-field-repeater[data-name="evento_premios"] table.acf-table th.acf-th,
    .acf-field-repeater[data-name="evento_premios"] table.acf-table td.acf-field {
        width: 20% !important;
    }

	.acf-field-repeater[data-name="evento_premios"] table.acf-table th[data-name="quantidade_de_inscritos"],
    .acf-field-repeater[data-name="evento_premios"] table.acf-table td[data-name="quantidade_de_inscritos"] {
        width: 10% !important;
    }
</style>
<?php
});

add_action('acf/input/admin_footer', function() {
?>
<script type="text/javascript">
(function($){
    function toggleGrupoConfirmacao() {
        var valorSelecionado = $('[data-name="tipo_evento"] select').val();
        var $grupoConfirmacao = $('#acf-group_68d2f29eebcd9'); // ID do postbox que deve ser ocultado

        if (valorSelecionado === 'premio') {
            $grupoConfirmacao.hide(); 
        } else {
            $grupoConfirmacao.show();
        }
    }

    // Executa ao carregar a página
    $(document).ready(toggleGrupoConfirmacao);

    // Executa quando o campo tipo_evento mudar
    $(document).on('change', '[data-name="tipo_evento"] select', toggleGrupoConfirmacao);
})(jQuery);
</script>
<?php
});


function get_post_type_label( int $post_id ) {
	$post_type = get_post_type( $post_id );
	
	switch ( $post_type ) {
		case 'post':
			return 'sorteio';
		case 'cortesias':
				return 'cortesias';
		default:
			return null;
	}
}

/** Endpoints relacionados ao gerenciamento do fluxo de Gratuidade e cortesias */

add_action('rest_api_init', function () {

    register_rest_route('cortesias/v1', '/resgatar', [
        'methods'  => 'POST',
        'callback' => 'resgatar_cortesia_callback',
        'permission_callback' => '__return_true'
    ]);

});