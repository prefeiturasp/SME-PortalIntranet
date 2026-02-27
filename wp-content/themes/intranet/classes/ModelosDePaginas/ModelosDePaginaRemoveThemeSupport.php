<?php

namespace Classes\ModelosDePaginas;


class ModelosDePaginaRemoveThemeSupport
{

	protected $page_id;
	protected $page_template_slug;

	public function __construct()
	{
		$this->page_id = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID']) ? $_POST['post_ID'] : null);		
		$this->page_template_slug = get_page_template_slug($this->page_id);
		add_action( 'admin_init', array($this,'removeThemeSupport' ), 10,2);
		add_filter( 'theme_page_templates', array($this, 'removePageTemplateForContributor'));

		/* Escondendo o Botão  Add Gallery do plugin Responsive Lightbox & Gallery */
		add_action( 'admin_menu', array($this, 'remove_menus' ));
		add_action( 'admin_head', array($this, 'my_custom_admin_head') );


		add_filter('script_loader_tag', array($this, 'add_noscript_tag'));

	}

	public function removePageTemplateForContributor($pages_templates){
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;

		if ($roles[0] == 'contributor') {
			unset( $pages_templates['page.php'] );
			unset( $pages_templates['pagina-agenda-secretario.php'] );
			unset( $pages_templates['pagina-contato-sme.php'] );
			unset( $pages_templates['pagina-escolas.php'] );
			unset( $pages_templates['pagina-inicial.php'] );
			unset( $pages_templates['pagina-organograma.php'] );
			unset( $pages_templates['pagina-organograma.php'] );
			unset( $pages_templates['pagina-mapa-dres.php'] );
			//unset( $pages_templates['pagina-abas.php'] );
		}
		return $pages_templates;
	}

	public function removeThemeSupport()
	{
		if ($this->page_template_slug === 'pagina-layout-colunas.php'){
			//remove_post_type_support( 'page', 'editor' );
			//remove_post_type_support( 'page', 'thumbnail' );
		}elseif ($this->page_template_slug === 'pagina-imagem-video.php'){
			//remove_post_type_support( 'page', 'thumbnail' );
		}elseif ($this->page_template_slug === 'pagina-mais-noticias.php'){
			remove_post_type_support( 'page', 'editor' );
		}elseif ($this->page_template_slug === 'pagina-mapa-dres.php'){
			remove_post_type_support( 'page', 'editor' );
		}
	}

	/* Escondendo o Botão  Add Gallery do plugin Responsive Lightbox & Gallery */
	public function remove_menus(){

		remove_menu_page( 'edit.php?post_type=rl_gallery' );
	}


	public function my_custom_admin_head() {
		echo '<style>
		#rl-insert-modal-gallery-button {display: none !important;}
		</style>';
	}

	// Adicionando noscript aos scripts
	public 	function add_noscript_tag($tag)
	{
		$noScript = <<<END
<noscript>
Essa funcionalidade é implementada usando Javascript. Não pode funcionar sem ele
</noscript>
END;
		return str_replace('</script>', '</script>'.$noScript, $tag);
	}
}
new ModelosDePaginaRemoveThemeSupport();